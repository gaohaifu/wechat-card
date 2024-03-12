<template>
	<view class="overall_content">
		<view class="header_background" :style="{'background-image':'url('+cdnUrl+backgroundImg+')'}">
			<cu-custom :bgColor="bgColor" :isBack="false" :backGround="backGround">
				<block slot="backText">返回</block>
				<block slot="content">首页</block>
			</cu-custom>
			</block>
			<view class="header_padding">
				<view class="header_message" :style="{'background-image':'url('+cdnUrl+cardimage+')'}">
					<view class="flex_layout userImg">
						<image :src="userData.avatar?userData.avatar:'../../static/images/user.png'" mode=""></image>
						<view class="name_position">
							<view :style="{color:fontcolor}">{{userData.name?userData.name:''}}</view>
							<text :style="{color:fontcolor}">{{userData.position}}</text>
							<text :style="{color:fontcolor}">{{companyInfo.name}}</text>
						</view>
					</view>
					<view class="waitOp" :style="{color:fontcolor}" v-if="!certificateStatus">未认证</view>
					<view class="extra">
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-dianhua"></i><text
								:style="{color:fontcolor}">{{userData.mobile?userData.mobile:'暂未填写'}}</text></view>
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-youjian1"></i><text
								:style="{color:fontcolor}">{{userData.email?userData.email:'暂未填写'}}</text></view>
						<view class="flex_layout" v-if="companyInfo.address"><i :style="{color:fontcolor}"
								class="iconfont icon-weizhi"></i><text
								:style="{color:fontcolor}">{{companyInfo.address?companyInfo.address:'暂未填写'}}</text>
						</view>
					</view>
					<!--切换按钮-->
					<!-- <view class="change_tab flex_layout" v-if="user_id==userData.user_id" @click="changeTab">
						<image src="../../static/images/change.png" mode=""></image><text>切换</text>
					</view> -->
				</view>
			</view>
		</view>
		<view class="overall_padding contents">
			<!-- 快捷工具 -->
			<view class="flex_between tools">
				<view class="flex flex-v flex-vc flex-hc tool-item" v-for="(item, inx) in tools" :key="inx">
					<text class="iconfont" :class="item.icon" :style="{color: item.color}"></text>
					<text>{{item.label}}</text>
				</view>
			</view>
			<!-- 快捷服务 -->
			<view class="services">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">服务</view>
					<view class="flex flex-vc more">
						<text>查看详情</text>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="flex_between">
					<view class="flex flex-v flex-vc flex-hc flex-wrap service-item" v-for="(item, inx) in services" :key="inx">
						<text class="iconfont" :class="item.icon"></text>
						<text>{{item.label}}</text>
					</view>
				</view>
			</view>
			<!-- 名片数据 -->
			<view class="visits">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">我的名片数据</view>
					<view class="flex flex-vc more">
						<text>查看详情</text>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="flex flex-hc statistics">
					<view class="flex-1 flex flex-v flex-vc flex-hc" v-for="(item, inx) in visits" :key="inx">
						<!-- <text class="num">{{item.value}}</text> -->
						<uni-badge class="uni-badge-left-margin" :text="item.value" absolute="rightTop" size="small">
							<view class="box"><text class="box-text num">{{item.value}}</text></view>
						</uni-badge>
						<text class="label">{{item.label}}</text>
					</view>
				</view>
				<view class="title2">
					最近访问
				</view>
				<view class="flex visit-item">
					<image class="avatar" src="../../static/images/img.jpg" mode="scaleToFill"></image>
					<view class="flex-1 right-box">
						<view class="flex flex-hsb flex-vc">
							<text class="name">陈丽容</text>
							<text class="time">今天 3:00</text>
						</view>
						<view class="company">
							<!-- <text>it</text>
							<text>|</text> -->
							<text>it｜陈氏空间（厦门）设计装修工程</text>
						</view>
						<view class="counts">
							第 4 次查看了我的名片‘厦门八达尔科技有限公司—总经理’
						</view>
						<view class="from">
							来源：我向对方发出的名片
						</view>
					</view>
				</view>
				<view class="flex flex-hc flex-vc more">
					<text>更访客数据</text>
					<text class="iconfont icon-Rightyou"></text>
				</view>
			</view>
			<view class="card-box">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">企业视频</view>
					<view class="flex flex-vc more" @click="toggleCardBox('showEnterpriseVideo')">
						<template v-if="!showEnterpriseVideo">
							<text>查看详情</text>
							<text class="iconfont icon-Rightyou"></text>
						</template>
						<text class="iconfont icon-down" v-else></text>
					</view>
				</view>
				<view class="video" v-show="showEnterpriseVideo">
					
				</view>
			</view>
			<!-- 企业简介 -->
			<view class="card-box">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">企业简介</view>
					<view class="flex flex-vc more" @click="toggleCardBox('showEnterpriseProfile')">
						<template v-if="!showEnterpriseProfile">
							<text>查看详情</text>
							<text class="iconfont icon-Rightyou"></text>
						</template>
						<text class="iconfont icon-down" v-else></text>
					</view>
				</view>
				<view class="" v-show="showEnterpriseProfile">
					<view class="desc-box">
						<view class="title">
							销售分析
						</view>
						<text class="desc">
							智能评定员工销售能力，销售排行情况一目了然
							总览每月销售数据，制定销售目标得心应手
						</text>
					</view>
				</view>
			</view>
		</view>
		<!--  #ifdef  MP-WEIXIN	 -->
		<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom"></bottomSheet>
		<!--  #endif -->
	</view>
</template>

<script>
	import { doIndexShare } from '../../config/newApi.js'
	import bottomSheet from '../../components/bbh-sheet/bottomSheet.vue';
	export default {
		components:{
			bottomSheet
		},
		data() {
			return {
				isShowBottom : false,			//底部弹窗开关
				userStaff: false,
				user_id: '',
				tools: [{
						icon: 'icon-dadianhua',
						color: '#0256FF',
						label: '打电话'
					},
					{
						icon: 'icon-jiaweixin',
						color: '#07C160',
						label: '加微信'
					},
					{
						icon: 'icon-fayoujian',
						color: '#FF4E20',
						label: '发邮箱'
					},
					{
						icon: 'icon-dingwei',
						color: '#02B7FF',
						label: '定位'
					},
					{
						icon: 'icon-famingpian',
						color: '#0256FF',
						label: '发名片'
					},
				],
				services: [{
						icon: 'icon-ziliao',
						label: '资料'
					},
					{
						icon: 'icon-moban',
						label: '模板'
					},
					{
						icon: 'icon-mingpianma',
						label: '名片吗'
					},
					{
						icon: 'icon-fenxiangshezhi',
						label: '分享设置'
					},
				],
				visits: [{
						label: '访问量(次)',
						value: 324
					},
					{
						label: '今日访问量(次)',
						value: 324
					},
					{
						label: '发名片(次)',
						value: 324
					},
				],
				showEnterpriseVideo: true,
				showEnterpriseProfile: true,
				allData:'',
				userData:{
					nickname:'',
					name:'',
					avatar:'',
					position:''
				},
				staffInfo: {}, // 名片信息
				myCardData: {}, // 我的名片数据
				visitStaffLists: [], // 访客列表
				videofiles: {}, // 企业视频
				description: '', // 企业简介
				certificateStatus:true,
				nickname:'',
				transmit:{
					company_id:'',
					nickname:'',
					position:'',
					shortname:'',
					avatar:'',
					phone:''
				},
				staff_id: 0,
				mystaff_id:0,
				color: '',
				backgroundImg: '',
				cardimage: '',
				fontcolor: '',
				showGlance: false,
				myselfstatus: false,
				
			}
		},
		// mounted() {
		// 	const innerAudioContext = uni.createInnerAudioContext();
		// 	innerAudioContext.autoplay = true;
		// 	innerAudioContext.src = 'https://bjetxgzv.cdn.bspapp.com/VKCEYUGU-hello-uniapp/2cc220e0-c27a-11ea-9dfb-6da8e309e0d8.mp3';
		// 	innerAudioContext.onPlay(() => {
		// 	  console.log('开始播放');
		// 	});
		// 	innerAudioContext.onError((res) => {
		// 	  console.log(res.errMsg);
		// 	  console.log(res.errCode);
		// 	});
		// },
		onLoad(e) {
			console.log('index调用onload',e); // 分享的？
			var that=this;
			if(typeof(e.staff_id)== "undefined" || e.staff_id=='' ||  e.staff_id==null || e.staff_id=='null'){
				uni.showToast({
					title:'无用户信息！',
					icon:'none',
					duration:2000
				})
			}else{
				this.staff_id=e.staff_id;
				uni.setStorageSync('staff_id',e.staff_id)
			}
		},
		onShow() {
			this.refreshUser()
			// #ifdef MP-WEIXIN
			this.wxLogin();
			// #endif
		},
		methods: {
			toggleCardBox(dataProp) {
				this[dataProp] = !this[dataProp]
			},
			wxLogin(){
				uni.login({
					success:(res) => {
						this.code = res.code;
						console.log("res.code: ",res.code);
					},
					fail: function (error) {
						console.log('login failed ' + error);
					}
				});
			},
			refreshUser(){
				console.log('index调用onshow');
				this.$api.refreshUser(
				 {},
				data => {
					console.log(data);
					if(data.code==1){
						this.user_id=data.data.user.id;
						this.getIndexShare();
					}else{
						//微信小程序端
						// #ifdef MP-WEIXIN
						console.log("小程序: ",1);
						this.isShowBottom=true
						this.userStaff=false
						this.user_id='';
						this.getIndexShare();
						// #endif						
					}
					
				})
			},
			//改版后小程序登录规则
			//小程序登录
			onGetUserProfile() {
				var platform='wechat';
				var that=this;
				var fid=uni.getStorageSync('parentid')?uni.getStorageSync('parentid'):''; 
				uni.getUserProfile({
					 desc: '用于完善会员资料', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
					success: res => {
						console.log(res)
						this.$api.third(
							{
								code: this.code,
								platform:platform,
								encrypted_data: res.encryptedData,
								iv: res.iv,
								raw_data: res.rawData,
								signature: res.signature
							},
							data => {
								console.log(data);
								//console.log(data.data.userinfo) 
								var res=data.data;
								if (data.code == 1) {
									this.userStaff=true
									this.$common.successToShow('登录成功!');
									try {
										this.$db.set('upload',1)
										this.$db.set('login', 1)
										this.$db.set('auth',res.auth)
										this.$db.set('user', res.userinfo)
										this.user_id=res.userinfo.id
										this.getIndexShare()
									} catch (e) {
										console.log("e: ",e);
									}
								}else{
									this.wxLogin();
								}
							}
						);
					},
					fail: (res) => {
						console.log("res: ",res);
						this.wxLogin();//重新获取登录code
						uni.hideLoading()
						if (res.errMsg == "getUserInfo:cancel" || res.errMsg == "getUserInfo:fail auth deny") {
							uni.showModal({
								title: '用户授权失败',
								showCancel: false,
								content: '请点击重新授权，如果未弹出授权，请尝试长按删除小程序，重新进入!',
								success: function(res) {
									if (res.confirm) {
										console.log('用户点击确定')
										//uni.navigateBack()
										this.isShowBottom = true;
									}
								}
							})
						}	
					}
				})
			},
			//底部开关
			closeBottom(){
				this.isShowBottom = false;
				this.onGetUserProfile()
			},
			getIndexShare() {
				var staff_id_c=this.staff_id || uni.getStorageSync('staff_id') || ''
				const condition = {
					staff_id: this.staff_id,
					user_id: this.user_id
				}
				doIndexShare(condition, (res) => {
					if(res.code === '1') {
						this.allData=data.data
						console.log(this.allData);
						this.usertype=this.allData.usertype;     //是否是企业负责人（0：不是  1：是）
						this.userData = this.staffInfo = res.staffInfo || {};
						if(this.userData.statusdata!='1'){
							this.certificateStatus=false;
						}
						this.myCardData = res.myCardData || {};
						this.visitStaffLists = res.visitStaffLists || [];
						this.videofiles = res.videofiles || [];
						this.description = res.description || '';
						this.updatetime=this.allData.newsTime
						this.color=this.staffInfo?this.staffInfo.smartcardtheme.colour:''
						this.backgroundImg=this.staffInfo?this.staffInfo.smartcardtheme.backgroundimage:''
						this.cardimage=this.staffInfo?this.staffInfo.smartcardtheme.cardimage:''
						this.fontcolor=this.staffInfo?this.staffInfo.smartcardtheme.fontcolor:''          
						if(uni.getStorageSync('color')==this.color){
							console.log('已有color');
						}else{
							uni.setStorageSync('color',this.color)
						}
						if(uni.getStorageSync('backgroundImg')==this.backgroundImg){
							console.log('已有backgroundImg');
						}else{
							uni.setStorageSync('backgroundImg',this.backgroundImg)
						}
						this.showGlance=this.visitStaffLists.map(item=>{
							return item.avatar
						})
						this.staff_id=this.staffInfo.id
						this.mystaff_id= this.userInfo.staff_id || 0
						this.myselfstatus = this.staff_id != this.mystaff_id
						this.transmit={
							company_id:this.companyInfo.id,
							position:this.userData.position,
							shortname:this.companyInfo.name,
							avatar:this.userData.avatar,
							phone:this.userData.mobile,
							wxQRCodeimage:this.userData.wxQRCodeimage,
							wechat:this.userData.wechat,
							staff_id:this.userData.id 
						};
						this.nickname=this.userData.name
					}else {
						if(this.user_id!=0 || this.user_id!=''){
							if(staff_id_c!=''){
								uni.showToast({
									title:'无此用户名片信息,即将跳转到个人名片主页...',
									icon:'none',
									duration:1500
								})
								setTimeout(()=>{
									uni.navigateTo({
										url:'pages/myCard/myCard'
									})
								},2000)
								return false;
						    }
						}
						this.$common.errorToShow(data.msg,function(){
							if(staff_id_c==undefined || staff_id_c==null  ||  staff_id_c=='' || staff_id_c==0){
								if(that.user_id!=0){
										uni.navigateTo({
											url:'pages/userInfo/userInfo'
										})
								}
							}
						})
					}
				})
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	page { background-color: #F6F7F9;}

	.header_background {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}

	.header_padding {
		padding: 0 30rpx;
	}

	.overall_padding {
		padding: 30rpx 15rpx;
	}

	.header_background {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}

	.header_padding {
		padding: 0 30rpx;
	}

	.overall_padding {
		padding: 30rpx 15rpx;
	}

	.grade {
		text-align: right;
		font-size: 26rpx;
		margin-top: 20rpx;
	}

	.header_message {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
		padding: 40rpx 40rpx;
		margin-top: 30rpx;
		border-radius: 15px;
		color: #e1d27e;
		box-shadow: 0 0 10px #999;
		position: relative;
	}

	.userImg image {
		display: block;
		width: 130rpx;
		height: 130rpx;
		border-radius: 50%;
	}

	.name_position {
		padding-left: 30rpx;
		width: 400rpx;
	}

	.name_position view {
		font-size: 42rpx;
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.name_position text {
		display: block;
		font-size: 26rpx;
		margin-top: 10rpx;
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.contents .tools,
	.contents .services,
	.contents .visits,
	.contents .card-box{
		margin-top: 20rpx;
		padding: 0rpx 20rpx 20rpx;
		background-color: #fff;
		border-radius: 16rpx;
	}

	/* 工具列表 */
	.tools {
		background-color: transparent;
		padding: 20rpx 0;
	}
	.tools .tool-item {
		width: 125rpx;
		height: 136rpx;
		background-color: #fff;
		border-radius: 20rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #333;
	}

	.tools .tool-item .iconfont {
		width: 64rpx;
		height: 64rpx;
		font-size: 64rpx;
		text-align: center;
	}
	/* 快捷服务 */
	.services .service-item {
		width: 25%;
		height: 136rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #333;
	}
	.services .service-item .iconfont {
		width: 48rpx;
		height: 48rpx;
		font-size: 48rpx;
		color: #0256FF;
		text-align: center;
	}
	/* card-box title */
	.title-bar {
		height: 88rpx;
	}

	.title-bar view {
		font-size: 24rpx;
		color: #333;
	}

	.title-bar .title {
		font-weight: 700;
		font-size: 32rpx;
	}

	.title-bar .iconfont {
		width: 32rpx;
		height: 32rpx;
		text-align: center;
	}
	/* 我的名片数据 */
	.visits .statistics { height: 116rpx;}
	.visits .statistics .num{
		font-size: 28rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .statistics .label {
		margin-top: 4rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #666;
	}
	.visits .title2 {
		padding: 16rpx 0;
		font-size: 24rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .visit-item {
		padding: 20rpx 0rpx;
	}
	.visits .visit-item .avatar {
		margin-right: 16rpx;
		width: 60rpx;
		height: 60rpx;
		border-radius: 50%;
	}
	.visits .visit-item .right-box {
		border-bottom: solid 1px #E6E6E6;
	}
	.visits .visit-item .right-box > view { margin: 4rpx 0; line-height: 40rpx;}
	.visits .visit-item .name {
		font-size: 28rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .visit-item .time,
	.visits .visit-item .company,
	.visits .visit-item .from {
		font-size: 22rpx;
		font-weight: 400;
		color: #999;
	}
	.visits .more {
		padding: 16rpx 0;
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
	}
	.visits .more .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
		text-align: center;
	}
	/* 企业视频 + 企业简介 */
	.card-box .desc-box {
		padding: 16rpx 0;
		text-align: center;
	}
	.card-box .desc-box .title {
		font-size: 32rpx;
		font-weight: 500;
		color: #333;
	}
	.card-box .desc-box .desc {
		margin-top: 16rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #666;
	}
	.card-box image {
		margin-top: 16rpx;
		width: 88%;
		height: auto;
		margin: 0 auto;
	}
</style>