<template>
	<view class="my-plan-style">
		<!-- 3D轮播 -->
		<view class="">
			<swiper class="imageContainer" @change="handleChange" :style="{height:brochureHeight}" previous-margin="45rpx" next-margin="45rpx">
				<block v-for="(item,index) in handbookData" :key="index">
					<swiper-item class="swiperitem">
						<view class="swiperitem_view" :style="{height:brochureHeight}" :data-id="item.id" :data-url="item.pdffiles" :data-title="item.title" @click="yuedu">
							<image class="itemImg" :src="cdnUrl+item.picimages[0]" lazy-load mode="widthFix"></image>
							<view>{{item.title}}</view>
							<!-- <text>{{item.interest}}</text> -->
							<button :style="{background:color}"  @click="openFile(cdnUrl+item.pdffiles)">查看</button>
						</view>
					</swiper-item>
				</block>
			</swiper>
		</view>
		<block v-if="filePath">
			<web-view :src="filePath"></web-view>
		</block>
		
	</view>
</template>
<script>
	import {cdnUrl} from '../../config/config.js';
	export default {
		props: {
			brochureHeight: {
				type: String
			},
			handbookData:{
				type:Array
			},
			color:{
				type: String
			}
		},
		data(){
			return {
				filePath:'',
				cdnUrl:cdnUrl,
				imgList:[]
			}
		},
		watch: {
			handbookData(e){
				this.init(e);
			}
		},
		created() {
			
		},
		methods: {
			init(e){
				this.imgList=e
			},
			yuedu(e){
				let id=e.currentTarget.dataset.id;
				let title=encodeURIComponent(e.currentTarget.dataset.title);
				let url=e.currentTarget.dataset.url;
				console.log("id: ",id);
				// let fileUrl = "http://demo.usiyi.com/uploads/ceshi.pdf"
				// let links = encodeURIComponent(fileUrl)
				uni.navigateTo({
					url:'/pages/index/report?id=' + id + '&title='+title+'&url='+url
				})
				
			},
			openFile(src){
				const downloadTask = uni.downloadFile({
									url: src,
									success: function(res) {
										uni.hideLoading()
										var filePath = res.tempFilePath;
										uni.openDocument({
								 		filePath: filePath,
											showMenu: true,
											success: function(res) {
												console.log('打开文档成功');
											}
										});
									}
								});
								downloadTask.onProgressUpdate((res) => {
									uni.showLoading({
										title:'下载进度' + res.progress+'%',
									})
									// console.log('下载进度' + res.progress);
									// console.log('已经下载的数据长度' + res.totalBytesWritten);
									// console.log('预期需要下载的数据总长度' + res.totalBytesExpectedToWrite);
								});
			},
			handleChange(e){
				//console.log(e);
			}
		}
	}	
</script>
<style lang="scss">
	// 3D轮播样式
	.imageContainer {
		width: 100%;
		/* height: 500rpx; */
		/* background: #000; */
		height: 100%;
	}
	
	.swiperitem {
		padding: 0 15upx;
		box-sizing: border-box;
		view.swiperitem_view{
			background: #fff;
			padding: 80rpx 100rpx 30rpx;
			border-radius: 10px;
			view{
				color: #333;
				font-size: 32rpx;
				text-align: center;
				margin-top: 50rpx;
			}
			text{
				display: block;
				color: #666;
				font-size: 24rpx;
				text-align: center;
				margin-top: 20rpx;
			}
			button{
				width: 225rpx;
				height: 75rpx;
				line-height: 75rpx;
				margin: 20rpx auto 0;
				display: block;
				background: #0084bf;
				color: #fff;
				font-size: 24rpx;
				border-radius: 5px;
			}
		}
	}
	
	.itemImg {
		width: 100%;
		display: block;
		/* height: 380rpx; */
		border-radius: 15rpx;
		box-shadow:0px 4upx 15upx 0px rgba(153,153,153,0.24);
	}
	
	.swiperactive {
		width: 95%;
		opacity: 1;
		z-index: 10;
		/* height: 430rpx; */
		height: 287upx;
		top: 0%;
		transition: all .2s ease-in 0s;
	}
	
	.zhankai{
		text-align: center;
		.iconfont{
			margin-left: 10upx;
		}
	}
</style>