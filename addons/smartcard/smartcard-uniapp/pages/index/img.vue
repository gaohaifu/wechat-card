<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<block v-if="reveal">
			<block v-if="picimages.length>0">
				<view class="img_flex flex_layout">
					<view @click="previewImg(index)" class="imgContent" v-for="(item,index) in picimages" :key="index"><image :src="cdnUrl+item" mode="aspectFill"></image></view>
				</view>
			</block>
			<block v-else>
				<view class="photo_album">
					<!-- <view class="noData_img"><image src="../../static/images/nodata2.png" mode=""></image></view>
					<view class="point_out" @click="pointOut"><button>TA上传相册</button></view> -->
					<view class="none_thread flex_layout">
						<view class="thread_item"></view>
						<view class="text_item flex_layout"><text></text>暂无更多<text></text></view>
						<view class="thread_item"></view>
					</view>
				</view>
			</block>
		</block>
		<block v-if="editable">
			<view style="height: 100rpx;"></view>
			<view class="add_btn" @click="addBtn"><button :style="{background:color}">+ 新增照片</button></view>
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
				staff_id:1,
				cdnUrl:cdnUrl,
				picimages:'',
				reveal:false,
				editable:false,
				color:'',
				params:''
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.params=options.transmit;
			this.userData = JSON.parse(options.transmit);
			this.userData.nickname=options.nickname;
			this.staff_id=options.staff_id
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
			addBtn(){
				uni.navigateTo({
					url:'addMypic/addMypic?staff_id='+this.staff_id+'&transmit='+this.params
				})
			},
			previewImg(index){
				var array=this.picimages.map(item=>{
					return this.cdnUrl+item
				})
				uni.previewImage({
					current:array[index], //预览图片的下标
					urls: array//预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
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
							this.picimages=data.data.picimages
							var uid=uni.getStorageSync('user').id;
							if(data.data.user.id==uid){
								this.editable=true;
							}
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
	.img_flex{padding: 20rpx 30rpx; justify-content: space-between;}
	.imgContent{width: -webkit-calc(50% - 15rpx); width:-moz-calc(50% - 15rpx); width:calc(50% - 15rpx); padding: 10rpx 0;}
	.imgContent image{display: block; width: 100%; height: 300rpx;}
	.add_btn{position: fixed; left: 0; bottom: 0; z-index: 100; height: 100rpx; width: 100%;}
	.add_btn button{width: 100%; height: 100rpx; background: #0084bf; color: #fff; font-size: 30rpx; border-radius: 0; line-height: 100rpx; margin: 0; padding: 0;}
	.edit_button button{width: 150rpx; height: 50rpx; background: #0084bf; color: #fff; font-size: 24rpx; border-radius: 0; line-height: 50rpx; margin: 0; padding: 0; display: block; margin: 30rpx auto 0;}
</style>
