<template>
	<view class="overall_content">
		<smallUserInfo page-title="首页" :is-back="false" 
			:is-show="isShowSmallUserInfo"
			:bg-color="bgColor"
			:back-ground="backGround"
			:theme-data="theme"></smallUserInfo>
		<view class="overall_padding contents">
			<!-- 快捷工具 -->
			<view class="flex_between tools">
				<view class="flex flex-v flex-vc flex-hc tool-item"
					v-for="(item, inx) in tools" :key="inx"
					:class="{'disabled' : item.disabled}"
					@click="toolsClick(item)">
					<text class="iconfont" :class="item.icon" :style="{color: item.color}"></text>
					<text>{{item.label}}</text>
				</view>
			</view>
			<!-- 认证状态 -->
			<view class="flex flex-hsb flex-vc cert-box" v-if="isShare">
				<view class="flex flex-vc left-box">
					<image src="../../static/images/cert-status.png" mode=""></image>
					<text>Ta的认证</text>
				</view>
				<view class="flex right-box">
					<view class="flex flex-vc flex-hc enterprise-cert">
						<image src="../../static/images/enterprise-cert.png" mode=""></image>
						<text>企业认证</text>
					</view>
					<view class="flex flex-vc flex-hc personal-cert">
						<image src="../../static/images/personal-cert.png" mode=""></image>
						<text>个人认证</text>
					</view>
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
					<view class="flex flex-v flex-vc flex-hc flex-wrap service-item"
						v-for="(item, inx) in services" :key="inx"
						@click="linkToService(item)">
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
					<view class="flex-1 flex flex-v flex-vc flex-hc"
						v-for="(item, inx) in visits" :key="inx">
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
				<view class="flex visit-item" v-for="(item, index) in visitStaffLists" :key="index">
					<image class="avatar" :src="item.avatar" mode="scaleToFill"></image>
					<view class="flex-1 right-box">
						<view class="flex flex-hsb flex-vc">
							<text class="name">{{item.name}}</text>
							<text class="time">{{item.createtime}}</text>
						</view>
						<view class="company">
							<!-- <text>it</text>
							<text>|</text> -->
							<text>{{item.position}}｜{{item.company}}</text>
						</view>
						<view class="counts">
							第 {{item.visitNum}} 次查看了我的名片‘{{item.myCardText}}’
						</view>
						<view class="from">
							来源：{{smartcardObj.origin[`${item.origin}`]}}
						</view>
					</view>
				</view>
				<view class="flex flex-hc flex-vc more" v-if="visitStaffLists.length > 0">
					<text>更访客数据</text>
					<text class="iconfont icon-Rightyou"></text>
				</view>
			</view>
			<!-- 企业视频 -->
			<view class="card-box" v-if="videofiles.length > 0">
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
					<video :src="item"
					    @error="videoErrorCallback" controls
						v-for="(item, index) in videofiles" :key="index"></video>
				</view>
			</view>
			<!-- 企业简介 -->
			<view class="card-box" v-if="description">
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
						<!-- <view class="title">
							销售分析
						</view> -->
						<text class="desc">
							{{description}}
						</text>
					</view>
				</view>
			</view>
			<!-- 偷懒直接设置空的div块 -->
			<view style="height: 110rpx;" v-if="isShare"></view>
			<!-- 分享栏 -->
			<view class="flex flex-vc share-box" v-if="isShare">
				<view class="flex flex-v flex-vc left-box">
					<text class="iconfont icon-daorutongxunlu"></text>
					<text>导入通讯录</text>
				</view>
				<view class="flex-1 flex">
					<view class="flex-1 primary-btn" @click="resendCard"
						:class="{'disabled' : this.userData.save_status !== '0'}">
						{{this.userData.save_status === '0' ? '回递名片' : '已回递'}}
					</view>
					<!-- <view class="flex-1 plain-btn" style="marign-right: 20rpx;">分享Ta的名片</view>
					<view class="flex-1 primary-btn">已回递</view> -->
				</view>
			</view>
		</view>
		<!--  #ifdef  MP-WEIXIN	 -->
		<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom"></bottomSheet>
		<!--  #endif -->
	</view>
</template>

<script>
	import bottomSheet from '../../components/bbh-sheet/bottomSheet.vue';
	import smallUserInfo from "../../components/small-user-info/small-user-info.vue"
	import {smartcardObj} from '@/config/common.js'
	export default {
		components:{
			bottomSheet,
			smallUserInfo
		},
		data() {
			return {
				isShare: false,
				bgColor:'bg-gradual-custom',
				backGround:'',
				isShowSmallUserInfo: true,
				smartcardObj: smartcardObj,
				isShowBottom : false,			//底部弹窗开关
				userStaff: false,
				user_id: '',
				tools: [{
						id: 1,
						disabled: false,
						icon: 'icon-dadianhua',
						color: '#0256FF',
						label: '打电话',
						shareCode: '2' // 是否分享页还是默认页独有 0 无 1有 2都有
					},
					{
						id: 2,
						disabled: false,
						icon: 'icon-jiaweixin',
						color: '#07C160',
						label: '加微信',
						shareCode: '2' // 是否分享页还是默认页独有 0 无 1有 2都有
					},
					{
						id: 3,
						disabled: false,
						icon: 'icon-fayoujian',
						color: '#FF4E20',
						label: '发邮箱',
						shareCode: '2' // 是否分享页还是默认页独有 0 无 1有 2都有
					},
					{
						id: 4,
						disabled: false,
						icon: 'icon-dingwei',
						color: '#02B7FF',
						label: '定位',
						shareCode: '2' // 是否分享页还是默认页独有 0 无 1有 2都有
					},
					{
						id: 5,
						disabled: false,
						icon: 'icon-famingpian',
						color: '#0256FF',
						label: '发名片',
						shareCode: '0' // 是否分享页还是默认页独有 0 无 1有 2都有
					},
					{
						id: 6,
						disabled: false,
						icon: 'icon-baocunmingpian',
						color: '#0256FF',
						label: '保存名片',
						shareCode: '1' // 是否分享页还是默认页独有
					},
				],
				services: [],
				visits: [{
						label: '访问量(次)',
						value: 0
					},
					{
						label: '今日访问量(次)',
						value: 0
					},
					{
						label: '发名片(次)',
						value: 0
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
				theme: {}
			}
		},
		onLoad(e) {
			console.log('index调用onload',e); 
			// 分享给别人的名片id 有可能是自己的也有可能是别人的
			if(typeof(e.staff_id)== "undefined" || e.staff_id=='' ||  e.staff_id==null || e.staff_id=='null'){
				uni.showToast({
					title:'无用户信息！',
					icon:'none',
					duration:2000
				})
				this.tools = this.tools.filter(i => (i.shareCode === '0' || i.shareCode === '2'))
				this.services = [{
						icon: 'icon-ziliao',
						label: '资料',
						url: '/pages/userInfo/userInfo'
					},
					{
						icon: 'icon-moban',
						label: '模板'
					},
					{
						icon: 'icon-mingpianma',
						label: '名片码'
					},
					{
						icon: 'icon-fenxiangshezhi',
						label: '分享设置'
					},
				]
			}else{
				this.isShare = true
				this.staff_id=e.staff_id;
				this.tools = this.tools.filter(i => (i.shareCode === '1' || i.shareCode === '2'))
				this.services = [{
						icon: 'icon-qiyejianjie',
						label: '企业简介'
					},
					{
						icon: 'icon-xiangce',
						label: '我的相册'
					},
					{
						icon: 'icon-qiyedongtai',
						label: '企业动态'
					},
					{
						icon: 'icon-qiyexuanchuance',
						label: '企业宣传册'
					},
					{
						icon: 'icon-chenggonganli',
						label: '成功案例'
					},
					{
						icon: 'icon-rexiaoshangpin',
						label: '热销商品'
					},
				]
				uni.setStorageSync('staff_id',e.staff_id)
			}
		},
		onShow() {
			this.isShowSmallUserInfo = true
			this.refreshUser()
			// #ifdef MP-WEIXIN
			this.wxLogin();
			// #endif
		},
		onHide() {
			this.isShowSmallUserInfo = false
		},
		onPageScroll(e){
			if(e.scrollTop>0){
				this.bgColor='bg-gradual-white';
				this.backGround=this.color
			}else{
				this.bgColor='bg-gradual-custom';
				this.backGround='transparent'
			}
		},
		methods: {
			resendCard() {
				if(this.userData.save_status !== '0') return
				this.$api.resendCard({staff_id: this.staff_id}, res => {
					if(res.code === 1) {
						uni.showToast({
							icon: 'success',
							title: res.msg || '名片回递成功'
						})
					}else {
						uni.showToast({
							icon: 'none',
							title: res.msg || '名片回递失败'
						})
					}
				})
			},
			// 点击会计工具
			toolsClick(row) {
				// 打电话
				if(row.id === 1 && this.staffInfo.mobile) {
					uni.makePhoneCall({
						phoneNumber: this.staffInfo.mobile //仅为示例
					});
				} else if(row.id === 2 && this.staffInfo.wechat) {
					
				} else if(row.id === 5) { // 发名片
					this.$api.sendCard({
						staff_id: this.staff_id
					}, res => {
						if(res.code === 1) {
							uni.showToast({
								icon: 'none',
								title: res.msg
							})
						}
					})
				}
			},
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
						// 是否已填写个人资料
						if(data.isStaff === 0) {
							uni.navigateTo({
								url: '/pages/userInfo/userInfo?user_id=' + this.user_id
							})
							return
						}
						this.getIndex();
					}else{
						//微信小程序端
						// #ifdef MP-WEIXIN
						console.log("小程序: ",1);
						this.isShowBottom=true
						this.userStaff=false
						this.user_id='';
						// this.getIndex();
						// #endif						
					}
					
				})
			},
			//改版后小程序登录规则
			//小程序登录
			onGetUserProfile() {
				var platform='wechat';
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
										this.getIndex()
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
			getIndex() {
				var staff_id_c=this.staff_id || uni.getStorageSync('staff_id') || 0 // 分享进来？分享要去share页面不公用
				const condition = {
					staff_id: this.staff_id,
					user_id: this.user_id
				}
				this.$api.doIndex(condition, (res) => {
					if(res.code === 1) {
						this.allData=res.data
						console.log(this.allData);
						this.usertype=this.allData.usertype;     //是否是企业负责人（0：不是  1：是）
						this.userData = this.staffInfo = this.allData.staffInfo || {};
						if(this.userData.statusdata!='1'){
							this.certificateStatus=false;
						}
						this.myCardData = this.allData.myCardData || {};
						this.visitStaffLists = this.allData.visitStaffLists || [];
						this.videofiles = this.allData.videofiles || [];
						this.description = this.allData.description || '';
						this.updatetime=this.allData.newsTime
						this.companyInfo = this.allData.smartcardcompany || {};
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
						// this.mystaff_id= this.userInfo.staff_id || 0
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
						this.visits[0].value = this.myCardData.allVisitNum
						this.visits[1].value = this.myCardData.todayVisitNum
						this.visits[2].value = this.myCardData.sendCardNum
						if(!this.staffInfo.mobile) this.tools.find(i => i.id === 1).disabled = true
						if(!this.staffInfo.wechat) this.tools.find(i => i.id === 2).disabled = true
						if(!this.staffInfo.email) this.tools.find(i => i.id === 3).disabled = true
						if(!(this.staffInfo.smartcardcompany && (this.staffInfo.smartcardcompany.longitude && 
							this.staffInfo.smartcardcompany.latitude || 
							this.staffInfo.smartcardcompany.address))) this.tools.find(i => i.id === 4).disabled = true
						// if(!this.staffInfo.mobile) this.tools.find(i => i.id === 5).disabled = true
						this.tools.forEach(it => {
							if(it.disabled) it.color = '#999';
						})
						this.userData.save_status = `${this.allData.save_status}`
						uni.setStorage({
							key: 'userData',
							data: this.userData
						})
						this.theme = {
							color: this.color,
							backgroundimage: this.backgroundImg,
							cardimage: this.cardimage,
							fontcolor: this.fontcolor
						}
						uni.setStorage({
							key: 'themeData',
							data: this.theme
						})
						
						console.info(this.tools, '====', !this.staffInfo.mobile, !this.staffInfo.email)
					}else {
						// 这个是分享进来的 - 最好整合成一个页面
						if(this.user_id!=0 || this.user_id!=''){
							if(staff_id_c!=''){
								uni.showToast({
									title:'无此用户名片信息,即将跳转到个人名片主页...',
									icon:'none',
									duration:1500
								})
								setTimeout(()=>{
									uni.navigateTo({
										url:'/pages/myCard/myCard'
									})
								},2000)
								return false;
						    }
						}
						this.$common.errorToShow(res.msg, () => {
							// 自己搜索之类的
							if(staff_id_c==undefined || staff_id_c==null  ||  staff_id_c=='' || staff_id_c==0){
								if(this.user_id!=0){
									uni.navigateTo({
										url:'/pages/userInfo/userInfo?user_id=' + this.user_id
									})
								}
							}
						})
					}
				})
			},
			linkToService(row) {
				if(row.url) {
					uni.navigateTo({
						url: row.url
					})
				}
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
	.contents .card-box,
	.contents .cert-box{
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
	.tools .tool-item.disabled {
		color: #999;
		cursor: not-allowed;
	}

	.tools .tool-item .iconfont {
		width: 64rpx;
		height: 64rpx;
		font-size: 64rpx;
		text-align: center;
	}
	/* 认证状态 */
	.contents .cert-box {
		height: 84rpx;
		padding: 0 32rpx;
	}
	.cert-box .left-box {
		font-weight: 400;
		font-size: 28rpx;
		color: #333;
	}
	.cert-box .left-box image {
		width: 32rpx;
		height: 32rpx;
		margin-right: 8rpx;
	}
	.cert-box .right-box {
		font-weight: 400;
		font-size: 20rpx;
		color: #fff;
	}
	.cert-box .right-box .enterprise-cert,
	.cert-box .right-box .personal-cert {
		width: 132rpx;
		height: 34rpx;
		border-radius: 20px 20px 20px 20px;
	}
	.cert-box .right-box .enterprise-cert { background: #0256FF; margin-right: 8rpx;}
	.cert-box .right-box .personal-cert { background: #EAB863;}
	.cert-box .right-box image {
		width: 24rpx;
		height: 24rpx;
		margin-right: 4rpx;
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
	/* 底部栏 */
	.share-box {
		height: 108rpx;
		padding: 0 32rpx;
		position: fixed;
		bottom: 0;
		width: 100%;
		background-color: #fff;
	}
	.share-box .left-box { 
		margin-right: 20rpx; 
		text-align: center;
		font-size: 20rpx;
		color: #666;
		font-weight: 400;
		}
	.share-box .left-box .iconfont {
		width: 48rpx;
		height: 48rpx;
		font-size: 48rpx;
		text-align: center;
		color: #0256FF;
	}
	.share-box .primary-btn,
	.share-box .plain-btn{
		height: 80rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: 400;
	}
	.share-box .primary-btn {
		color: #fff;
		background-color: #0256FF;
		border: solid 1px #0256FF;
	}
	.share-box .primary-btn.disabled {
		background-color: #999;
		border-color: #999;
	}
	.share-box .plain-btn {
		color: #333;
		background-color: #fff;
		border: solid 1px #E6E6E6;
	}
	.contents .video { text-align: center;}
</style>