<template>
	<view class="overall_content">
		<view class="header_background" :style="{'background-image':'url('+cdnUrl+backgroundImg+')'}">
			<cu-custom :bgColor="bgColor" :isBack="true" :backGround="backGround">
				<block slot="backText">返回</block>
				<block slot="content">切换背景</block>
			</cu-custom>
			<view class="header_padding">
				<smallUserInfo @getAllUserData="getAllUserData"></smallUserInfo>
			</view>
		</view>
		<view class="message_content">
			<view class="message_item">
				<view class="title">选择板式</view>
				<view class="scroll">
					<scroll-view class="scroll_view" scroll-x="true">
						<view class="scroll-item" v-for="(item,index) in templet1" :key="index" @click="activeTab(index,item.id)">
							<image :src="cdnUrl+item.backgroundimage" mode=""></image>
							<view class="zzc" v-if="item.status==2"></view>
							<view class="active" v-if="item.status==2">
								<view :style="{color:color}" class="iconfont icon-dui"></view>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
			<view class="message_item">
				<view class="title">选择背景</view>
				<view class="scroll">
					<scroll-view class="scroll_view" scroll-x="true">
						<view class="scroll-item" v-for="(item,index) in templet2" :key="index" @click="activeTab1(index)">
							<image :src="item.img" mode=""></image>
							<view class="zzc" v-if="item.active"></view>
							<view class="active" v-if="item.active">
								<view :style="{color:color}" class="iconfont icon-dui"></view>
							</view>
						</view>
					</scroll-view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import smallUserInfo from "../../components/small-user-info/small-user-info.vue"
	import {
		smartcardBG,
		cdnUrl
	} from '../../config/common.js'
	export default {
		components: {
			smallUserInfo
		},
		data() {
			return {
				smartcardBG: smartcardBG,
				cdnUrl:cdnUrl,
				bgColor:'bg-gradual-custom',
				color:'',
				backgroundImg:'',
				backGround:'',
				cardimage:'',
				staff_id:'',
				user_id:'',
				userData:'',
				templet1:[],
				templet2:[],
				color:'',
				fontcolor:'',
				companyInfo:[],
				certificateStatus:true
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			this.backgroundImg=uni.getStorageSync('backgroundImg')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.staff_id=options.staff_id
			this.user_id=options.user_id
			this.indexData();
			this.themeList();
		},
		onPageScroll(e){
			this.scrollTop=e.scrollTop
			if(e.scrollTop>0){
				this.bgColor='bg-gradual-white';
				this.backGround=this.color
			}else{
				this.bgColor='bg-gradual-custom';
				this.backGround='transparent'
			}
		},
		methods: {
			activeTab(index,id){
				const parm={
				  Theme_id:id
				};
				this.$api.themeEdit(
				parm,
				data => {
					if(data.code==1){
						for(var i=0; i<this.templet1.length; i++){
							this.templet1[i].status=1
						}
						this.templet1[index].status=2
						this.color=this.templet1[index].colour
						this.backgroundImg=this.templet1[index].backgroundimage
						this.cardimage=this.templet1[index].cardimage
						this.fontcolor=this.templet1[index].fontcolor
						uni.setStorageSync('color',this.color)
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			},
			activeTab1(index){
				for(var i=0; i<this.templet2.length; i++){
					this.templet2[i].active=false
				}
				this.templet2[index].active=true
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
						for(var i=0; i<this.templet1.length; i++){
							if(this.templet1[i].status==2){
								this.color=this.templet1[i].colour
								this.backgroundImg=this.templet1[i].backgroundimage
								this.cardimage=this.templet1[i].cardimage
								this.fontcolor=this.templet1[i].fontcolor
								uni.setStorageSync('color',this.color)
							}
						}
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			},
			//首页全部信息接口（包含个人信息）
			indexData(){
				const parm={
					staff_id:this.staff_id,
					user_id:this.user_id
				};
				this.$api.indexData(
				parm,
				data => {
					if(data.code==1){
						this.allData=data.data
						this.usertype=data.data.usertype;     //是否是领导角色（0：不是  1：是）
						this.userData=data.data.staffInfo
						if(this.userData.statusdata!='1'){
							this.certificateStatus=false;
						}
						
						this.companyInfo=data.data.companyInfo
						this.showGlance=data.data.visitStaffLists.map(item=>{
							return item.avatar
						})
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			},
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
</style>
