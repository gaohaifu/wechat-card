<!-- 切换名片主题 -->
<template>
	<view class="overall_content fixed">
		<smallUserInfo page-title="切换背景" :theme-data="theme"
			:is-show="false"
			bg-color="bg-gradual-custom"
			back-ground="transparent"
			:is-back="true"></smallUserInfo>
		<view class="message_content">
			<view class="message_item">
				<view class="title">选择主题</view>
				<view class="scroll">
					<scroll-view class="scroll_view" scroll-x="true">
						<view class="scroll-item" v-for="(item,index) in templet1" :key="index" @click="activeTab(index,item.id)">
							<image :src="cdnUrl+item.cardimage" mode=""></image>
							<view class="zzc" v-if="item.status==2"></view>
							<view class="active" v-if="item.status==2">
								<view :style="{color:color}" class="iconfont icon-dui"></view>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
			<!-- <view class="message_item">
				<view class="title">选择背景</view>
				<view class="scroll">
					<scroll-view class="scroll_view" scroll-x="true">
						<view class="scroll-item" v-for="(item,index) in templet1" :key="index" @click="activeTab(index)">
							<image :src="cdnUrl+item.cardimage" mode=""></image>
							<view class="zzc" v-if="item.active"></view>
							<view class="active" v-if="item.active">
								<view :style="{color:color}" class="iconfont icon-dui"></view>
							</view>
						</view>
					</scroll-view>
				</view>
			</view> -->
		</view>
		<view class="btn primary-btn" @click="doSave">保存</view>
	</view>
</template>

<script>
	import smallUserInfo from "../../components/small-user-info/small-user-info.vue"
	import {
		smartcardBG
	} from '@/config/common.js'
	import {
		cdnUrl
	} from '@/config/config.js'
	export default {
		components: {
			smallUserInfo
		},
		data() {
			return {
				smartcardBG: smartcardBG,
				cdnUrl:cdnUrl,
				bgColor:'bg-gradual-custom',
				staff_id:'',
				user_id:'',
				userData:'',
				templet1:[],
				theme: {
					color: '',
					fontcolor: '',
					cardimage: '',
					backgroundimage: ''
				},
				themeid: ''
			}
		},
		onLoad(options) {
			const themeData = uni.getStorageSync('themeData') || {}
			if(themeData.cardimage) this.theme = themeData
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: themeData.color
			})
			this.staff_id=options.staff_id
			this.user_id=options.user_id
			this.themeList();
		},
		methods: {
			activeTab(index,id){
				this.themeid = id
				this.templet1 = this.templet1.map((i, inx) => {
					i.status = 1
					if(inx === index) {
						i.status = 2
						this.theme = i
					}
					return i
				})
			},
			//主题列表
			themeList(){
				const parm={
					
				};
				this.$api.themeList(
				parm,
				data => {
					if(data.code==1){
						this.templet1=data.data.map(item=>{
							return{
								...item
							}
						})
						const fi = this.templet1.find(i => i.status === 2)
						if(fi) {
							this.theme = {
								color: fi.colour,
								fontcolor: fi.fontcolor,
								cardimage: fi.cardimage,
								backgroundimage: fi.backgroundimage
							}
						}
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			},
			doSave() {
				if(!this.themeid) return;
				const parm={
				  Theme_id: this.themeid
				};
				this.$api.themeEdit(
				parm,
				data => {
					if(data.code==1){
						uni.showToast({
							icon: 'none',
							title: data.msg
						})
						const fi = this.templet1.find(i => i.status ===2)
						uni.setStorageSync('color',fi.color)
						uni.setStorageSync('themeData', {
							color: fi.color,
							fontcolor: fi.fontcolor,
							backgroundimage: fi.backgroundimage,
							cardimage: fi.cardimage
						})
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');
	page{background: #fefefe;}
	
	.extra{margin-top: 60rpx;}
	.extra>view{font-size: 24rpx; margin-top: 20rpx;}
	.extra i{color: #5a544e; line-height: 1;}
	.extra text{color: #8d887f; margin-left: 12rpx;}
	.share_btn{justify-content: space-between; padding: 30rpx 0;}
	.share_btn view{width:-webkit-calc(50% - 15rpx); width:-moz-calc(50% - 15rpx); width:calc(50% - 15rpx);}
	.share_btn view button{width: 100%; height: 80rpx; border-radius: 8px; border: 1px solid #0084bf; display: flex; align-items: center; justify-content: center; margin: 0; padding: 0; font-size: 32rpx;}
	.title{color: #999; font-size: 28rpx; padding: 30rpx 12rpx;}
	.scroll_view{white-space: nowrap;}
	.scroll-item{display: inline-block; margin: 0 12rpx; position: relative;}
	.scroll-item>image{width: 145rpx; height: 85rpx; display: block; border-radius: 10rpx;}
	.zzc{position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; border-radius: 10rpx;}
	.active{position: absolute; left: 50%; top: 50%; width: 50rpx; height: 50rpx; border-radius: 50%; background: rgba(255,255,255,0.8); transform: translate(-50%,-50%); display: flex; align-items: center; justify-content: center; z-index: 100;}
	.active view{display: block; font-size: 30rpx;}
	.change_tab{background: rgba(0,0,0,0.5); border-radius: 100rpx 0 0 100rpx; padding: 10rpx 15rpx; box-shadow: 0 0 10px #ccc; position: absolute; right: 0; bottom: 40rpx; z-index: 100;}
	.change_tab text{color: #fff; font-size: 24rpx; margin-left: 10rpx;}
	.change_tab image{display: block; width: 30rpx; height: 30rpx;}
	.waitOp{ position: absolute; right: 20rpx; top: 50rpx; }
	.primary-btn {
		width: 80%;
		margin: 100rpx 10%;
		height: 80rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #fff;
		background-color: #0256FF;
		border: solid 1px #0256FF;
	}
</style>
