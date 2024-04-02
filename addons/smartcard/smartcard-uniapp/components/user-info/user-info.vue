<template>
	<view class="content">
		<view class="userInfo flex_layout">
			<view class="userImg flex_layout">
				<image :src="userData.avatar" mode=""></image>
				<view class="name_grade">
					<view class="name flex_layout"><view>{{userData.nickname}}</view> <text class="name-position">{{userData.position}}</text></view>
					<view class="grade">{{userData.shortname}}</view>
				</view>
			</view>
			<view class="flex_layout">
				<view class="right_phone" @click="card">
					<i :style="{color:color}" class="iconfont icon-mingpianma"></i>
					<text>名片</text>
				</view>
				<view class="right_phone" @click="phone(userData.phone)">
					<i :style="{color:color}" class="iconfont icon-dadianhua"></i>
					<text>电话</text>
				</view>
				<view class="right_phone" @click="wechat">
					<i :style="{color:color}" class="iconfont icon-jiaweixin"></i>
					<text>微信</text>
				</view> 
			</view>
		</view>
		<view class="zzc" @click="wechat" v-if="codeStatus"></view>
		<view class="wechat_content" v-if="codeStatus">
			<view class="wechat_img padding" v-if="userData.wxQRCodeimage">
				<image :src="cndUrl+userData.wxQRCodeimage" mode="widthFix" :show-menu-by-longpress="true"></image>
				<view>长按保存图片，扫码添加微信</view>
			</view>
			<view v-else>
				<view class="padding" v-if="userData.wechat" @click="copyCode(userData.wechat)">
					<view class="title">加我微信</view>
					<view class="content">{{userData.wechat}}</view>
				</view>
			</view>
		</view>
		<view style="height: 170rpx;"></view>
	</view>
</template>

<script>
	import {
		  cdnUrl,
			baseUrl,
			baseApiUrl
		} from '../../config/config.js';
	export default {
		props: {
			userData:{
				type:Object
			},
			color:{
				type: String,
				default: ''
			},
		},
		data() {
		  return {
				cndUrl:cdnUrl,
				wechatImg:'../../static/images/nodata1.png',
				codeStatus:false
		  }
		},
		created() {
			
		},
		mounted() {
			
		},
		methods: {
			//复制微信号
			copyCode(value) {
				uni.setClipboardData({
					data: value, 
					success: () => {
						uni.showToast({
							title: "复制微信号成功"
						})
					}
				});
			},
			phone(num){
				//必须转为字符串类型
				var phone=num.toString()
				uni.makePhoneCall({
				    phoneNumber: phone, //仅为示例
						complete: function(res){
							//console.log(res);
						}
				});
			},
			card(){
				uni.switchTab({
					url:'/pages/myCard/myCard'
				})
			},
			wechat(){
				if(this.userData.wxQRCodeimage==''&&this.userData.wechat==''){
					uni.showToast({
						title:'还没有添加微信号',
						icon:'none'
					})
				}else{
					this.codeStatus=!this.codeStatus
				}
			}
		},
	};
</script>

<style>
	@import url('../../common/common.css');
	.userInfo{padding: 30rpx; background: #fff; justify-content: space-between; border-bottom: 1px solid #f1f1f1; position: fixed; top: var(--window-top); left: 0; width: 100%; z-index: 100;}
	.userImg image{width: 90rpx; height: 90rpx; border-radius: 50%; display: block;}
	.name_grade{padding-left: 20rpx;}
	.name{color: #333; font-size: 36rpx;}
	.name view{width: 100rpx; font-size: 28rpx; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
	.name text{display: block; color: #333; font-size: 24rpx; margin-left: 15rpx;}
	.grade{color: #999; font-size: 24rpx; line-height:50rpx; height:50rpx; width: 300rpx; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
	.right_phone{padding: 0 15rpx;}
	.right_phone i{display: block; font-size: 45rpx; text-align: center; color: #0084bf;}
	.right_phone text{display: block; text-align: center; margin-top: 10rpx;}
	.name-position{ border:1px solid #f0f0f0; padding:5rpx 15rpx; background:#f3f3f3;border-radius:10rpx;}
	.zzc{position: fixed; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99;}
	.wechat_content{position: fixed; width: 80%; left: 10%; top: 50%; transform: translateY(-50%); background: #fff; border-radius: 10px; z-index: 100;}
	.wechat_img{border-radius: 10px;background: #fff;}
	.wechat_img view{color: #f00;font-size: 28rpx;margin-top: 30rpx;text-align: center;}
	.wechat_img image{display: block; width: 100%;}
	.wechat_text{padding: 30rpx; text-align: center; font-size: 28rpx; color: #333;}
	.title{font-size: 30rpx;text-align: center;color: #333;}
	.content{font-size: 28rpx;margin-top: 30rpx;color: #333;text-align: center;}
</style>
