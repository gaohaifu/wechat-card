<template>
	<view class="page-wrap">
		<view class="page-title">
			选择名片样式<text>图内文字仅供预览</text>
		</view>
		<radio-group class="card-radio" @change="radioChange">
			<view class="card-box" v-for="item in [0,1]" :key="item">
				<image class="card-bg" src="../../static/images/card-bg.png" />
				<view class="card-content">
					<view class="card-item">
						<image src="../../static/images/img.jpg"></image>
						<view class="card-user-box">
							<view class="card-user">陈立荣</view>
							<view>总经理</view>
							<view>厦门巴达尔科技有限公司</view>
						</view>
					</view>
					<view class="email-item">
						<image src="../../static/images/choose-card-icon01.png" />
						<text>123-323-123-111</text>
					</view>
					<view class="email-item">
						<image src="../../static/images/choose-card-icon02.png" />
						<text>22233@qq.com</text>
					</view>
					<view class="email-item">
						<image src="../../static/images/choose-card-icon03.png" />
						<text>得了了，测试测试乐乐</text>
					</view>
				</view>
				<label>
					<view class="choose-box">
						<text>给供应少老板的名片</text>
						<radio :value="item" :checked="chooseIndex === item" />
					</view>
				</label>
			</view>
		</radio-group>
		<view class="descript">
			<view>创建更多企业数字名片样式</view>
			<text>请前往q.camcard.com企业管理后台</text>
		</view>
		<view class="btn-box">
			<view class="download-btn">
				<image src="../../static/images/download_icon.png" />
				<text>下载分享</text>
			</view>
			<view class="download-btn invite-btn">
				<image src="../../static/images/wx_icon.png" />
				<text>微信邀请</text>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				list: [],
				current: 0,
				chooseIndex: 0,
			}
		},
		methods: {
			radioChange: function(evt) {
				this.chooseIndex = Number(evt.detail.value)
			}
		}
	}
</script>

<style>
	.page-wrap {
		padding: 0 32rpx;
		background-color: #F6F7F9;
	}
	.page-title {
		padding: 20rpx 0;
		height: 88rpx;
		font-size: 36rpx;
		color: #333;
	}
	.page-title text {
		font-size: 24rpx;
		color: #999;
		margin-left: 16rpx;
	}
	.card-radio {
		display: block;
	}
	.card-box {
		position: relative;
		height: 360rpx;
		border-radius: 40rpx;
		overflow: hidden;
		margin-bottom: 20rpx;
	}
	.card-bg {
		width: 100%;
		height: 100%;
	}
	.card-content {
		position: absolute;
		top: 42rpx;
		left: 24rpx;
		right: 24rpx;
	}
	.card-item {
		display: flex;
		margin-bottom: 30rpx;
	}
	.card-item image {
		flex-shrink: 0;
		width: 120rpx;
		height: 120rpx;
		border-radius: 60rpx;
		margin-right: 20rpx;
	}
	.card-user-box {
		flex: 1;
		border-bottom: 1px solid #F6F7F9;
		line-height: 48rpx;
		font-size: 24rpx;
		color: #333;
	}
	.card-user {
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 36rpx;
		color: #333;
		font-weight: 700;
	}
	.email-item {
		display: flex;
		align-items: center;
		font-size: 24rpx;
		color: #333;
		margin-bottom: 8rpx;
		overflow: hidden;
	}
	.email-item image {
		flex-shrink: 0;
		width: 24rpx;
		height: 24rpx;
		margin-right: 8rpx;
	}
	.choose-box {
		position: absolute;
		left: 0;
		right: 0;
		bottom: 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
		height: 80rpx;
		padding: 0 32rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		color: #fff;
		background: rgba(0,0,0,0.8);
		z-index: 1;
	}
	.btn-box {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 112rpx;
		margin: 0 -32rpx;
		background-color: #fff;
	}
	.download-btn {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 319rpx;
		height: 72rpx;
		line-height: 72rpx;
		font-size: 28rpx;
		color: #fff;
		background-color: #0256FF;
		margin: 0 24rpx;
		border-radius: 40rpx;
	}
	.download-btn image {
		width: 20rpx;
		height: 20rpx;
		margin-right: 8rpx;
	}
	.invite-btn {
		background-color: #07C160;
	}
	.descript {
		line-height: 48rpx;
		text-align: center;
		font-size: 28rpx;
		color: #333;
		padding: 276rpx 0 20rpx;
	}
	.descript text {
		font-size: 24rpx;
		color: #9E9E9E;
	}
</style>