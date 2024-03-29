<template>
	<uni-popup ref="codePopupRef" background-color="#fff">
		<view class="code-content">
			<uni-icons class="close-icon" type="closeempty" @click="close" />
			<view class="code-title">我的名片码</view>
			<view class="code-desc">对方微信扫一扫即可查看保存</view>
			<view class="code-img">
				<image :src="base64Code" />
			</view>
			<view class="size-box">
				<view class="size-item" v-for="(item, index) in sizeList" :class="{on: index === active}" :key="index" @click="sizeChange(index)">
					<view class="size-title">{{item.title}}</view>
					<view>{{item.subtitle}}</view>
				</view>
			</view>
			<view class="download">
				<uni-icons size="24" color="#fff" type="download" />
				<text @click="downloadFn">下载名片码</text>
			</view>
		</view>
	</uni-popup>
</template>

<script>
	export default {
		name: 'MycardCode',
		data() {
			return {
				active: 0,
				base64Code: '',
				sizeList: [
					{
						title: '微信扫一扫',
						subtitle: '适用于面对面交换名片',
						value: 0
					},
					{
						title: '8cm x 8cm',
						subtitle: '适用于纸质名片',
						value: 1
					},
					{
						title: '20cm x 20cm',
						subtitle: '适用于个人简历、台卡',
						value: 2
					},
					{
						title: '60cm x 60cm',
						subtitle: '适用于海报、易拉宝',
						value: 3
					}
				]
			}
		},
		mounted() {
			// this.open()
			this.getImageCode()
		},
		methods: {
			getImageCode() {
				this.$api.getMiniCode({
					path: 'pages/myCard/myCard',
					scene: 'a=1',
					check_path: false, // 默认是true，检查page 是否存在，为 true 时 page 必须是已经发布的小程序存在的页面（否则报错）；为 false 时允许小程序未发布或者 page 不存在， 但page 有数量上限（60000个）请勿滥用。
					env_version: 'develop' // develop开发版，release正式版，trial体验版
				}, (res) => {
					console.log(res, 'miniCode', res.data.img)
					this.base64Code = res.data.img;
					// this.base64Code = 'data:image/png;base64,' + uni.arrayBufferToBase64(res.data.img).replace(/[\r\n]/g, '');
					console.info(this.base64Code, '=====>')
				})
			},
			sizeChange(index) {
				if (this.active === index) return
				this.active = index
			},
			open() {
				this.$refs.codePopupRef.open('bottom')
			},
			close() {
				this.$refs.codePopupRef.close()
			},
			downloadFn () {
				const that = this
				uni.downloadFile({
					url: that.base64Code,
					success: res => {
						if (res.statusCode === 200) {
							uni.saveImageToPhotosAlbum({
								filePath: res.tempFilePath,
								success: function() {
									console.log('保存成功')
								},
								fail: function() {
									console.log('保存失败')
								}
							})
						} else {
							console.log('保存失败')
						}
					}
				})
			}
		}
	}
</script>

<style>
	.code-content {
		position: relative;
		top: -20px;
		padding: 32rpx;
		min-height: 300px;
		border-radius: 20px;
		background-color: #fff;
	}
	.close-icon {
		position: absolute;
		left: 32rpx;
		top: 32rpx;
	}
	.code-title {
		font-size: 32rpx;
		font-weight: 700;
		color: #333;
		text-align: center;
		margin-bottom: 64rpx;
	}
	.code-desc {
		font-size: 28rpx;
		text-align: center;
		color: #777;
	}
	.code-img {
		/* padding: 32rpx 0; */
		margin: 32rpx auto;
		width: 360rpx;
		height: 360rpx;
	}
	.code-img image {
		width: 100%;
		height: 100%;
	}
	.size-box {
		padding: 32rpx 0;
		display: flex;
		flex-wrap: wrap;
		margin: 0 -8rpx;
	}
	.size-item {
		width: calc(50% - 16rpx);
		margin: 8rpx;
		padding: 8px;
		border-radius: 8rpx;
		box-sizing: border-box;
		font-size: 26rpx;
		color: #999;
		text-align: center;
		border: 1px solid #ebebeb;
	}
	.size-item.on {
		background-color: #e4f5ff;
		color: #3cafe5;
		border-color: #33a1f0;
	}
	.size-item.on .size-title {
		color: #3cafe5;
	}
	.size-title {
		color: #333;
		margin-bottom: 8rpx;
	}
	.download {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 74rpx;
		background-color: #0256FF;
		border-radius: 20px;
		font-size: 30rpx;
		color: #fff;
	}
	.download text {
		margin-left: 8rpx;
	}
</style>