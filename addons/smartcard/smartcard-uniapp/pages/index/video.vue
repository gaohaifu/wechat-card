<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<block v-if="reveal">
			<block v-if="videofiles.length>0">
				<view class="video_content">
					<block v-for="(item,index) in videofiles" :key="index">
						<view class="video_item"><video :id="`video${index}`" @play="playVideo(`video${index}`)" :src="cdnUrl+item"></video></view>
					</block>
				</view>
			</block>
			<block v-else>
				<view class="photo_album">
					<!-- <view class="noData_img"><image src="../../static/images/nodata1.png" mode=""></image></view>
					<view class="point_out" @click="pointOut"><button>请TA提供视频介绍</button></view> -->
					<view class="none_thread flex_layout">
						<view class="thread_item"></view>
						<view class="text_item flex_layout"><text></text>暂无更多<text></text></view>
						<view class="thread_item"></view>
					</view>
				</view>
			</block>
		</block>
	</view>
</template>

<script>
	var _this;
	import userInfo from '@/components/user-info/user-info.vue'
	import {cdnUrl} from '../../config/config.js';
	export default {
		components: {
		  userInfo
		},
		data() {
			return {
				userData:'',
				staff_id:0,
				videofiles:'',
				cdnUrl:cdnUrl,
				reveal:false,
				color:''
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.userData = JSON.parse(options.transmit);
			this.staff_id=options.staff_id
			this.userData.nickname=options.nickname;
			this.staffInfoData();
		},
		mounted() {
			_this= this;
		},
		onShow() {
			
		},
		// 加载更多
		onReachBottom: function() {
			
		},
		methods: {
			playVideo(currentId){
				let _this = this;
				this.videoContent = uni.createVideoContext(currentId,_this).play();
				// 获取视频列表
				let trailer = this.videofiles;
				trailer.forEach((item, index) =>{	// 获取json对象并遍历, 停止非当前视频
					if (item != null && item != "") {
						let temp = 'video' + index;
						if (temp != currentId) {
							// 暂停其余视频
							uni.createVideoContext(temp,_this).pause(); //暂停视频播放事件
						}
					}
	 
				})
			},
			//个人信息接口
			staffInfoData(){
				this.reveal=false
				const parm={
					staff_id:this.staff_id
				};
				this.$api.staffInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.reveal=true
							this.videofiles=data.data.videofiles
						}
					})
			},
			pointOut(){
				uni.showToast({
					title:'更新提醒已收到，敬请期待！',
					icon:'none'
				})
			}
		}
	}	
</script>

<style>
	@import url('../../common/common.css');
	page{background: #f2f6f7;}
	.photo_album{padding: 30rpx;}
	.noData_img image{width: 360rpx; height: 360rpx; display: block; margin: 150rpx auto 0;}
	.point_out{margin-top: 80rpx;}
	.point_out button{width: 340rpx; height: 90rpx; line-height: 90rpx; text-align: center; color: #fff; font-size: 30rpx; border-radius: 5px; background: #0084bf;}
	.none_thread{width: 440rpx; margin: 50rpx auto 0;}
	.thread_item{width: 140rpx; height: 1px; background: #cccccc;}
	.text_item{width: 160rpx; color: #999; font-size: 24rpx; justify-content: space-between;}
	.text_item text{display: block; width: 10rpx; height: 10rpx; border-radius: 10rpx; background: #ccc;}
	.video_item{padding: 15rpx 30rpx;}
	.video_item video{display: block; width: 100%; height: 390rpx;}
</style>
