<template>
	<view class="overall_content">
		<view class="header_background" :style="{'background-image':'url('+cdnUrl+backgroundImg+')'}">
			<cu-custom :bgColor="bgColor" :isBack="isBack" :backGround="backGround">
				<block slot="backText">返回</block>
				<block slot="content">首页</block>
			</cu-custom>
			<view class="header_padding">
				<view class="header_message" :style="{'background-image':'url('+cdnUrl + cardimage+')'}">
					<view class="flex_layout userImg">
						<image :src="userData.avatar?userData.avatar:'../../static/images/user.png'" mode=""></image>
						<view class="name_position">
							<view :style="{color:fontcolor}">{{userData.name?userData.name:''}}</view>
							<text :style="{color:fontcolor}">{{userData.position}}</text>
							<text :style="{color:fontcolor}">{{companyInfo.name}}</text>
						</view>
					</view>
					<view class="cert-status" :class="{'waitOp': !certificateStatus, 'op': certificateStatus}"
						:style="{'background-image':'url('+(certificateStatus? smartcardBG.cert : smartcardBG.unCert)+')',
									color:(certificateStatus ? 'white' : fontcolor)}"
						@click="linkToCert">
						{{!certificateStatus? '未认证' : '已认证'}}
					</view>
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
					<view class="isHC-box" v-if="smartcardObj.save_status[userData.save_status]">
						<view class="bg">
							{{smartcardObj.save_status[userData.save_status]}}
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="overall_padding contents">
			<!-- 发名片 -->
			<button type="primary" open-type="share" class="share-btn">发名片</button>
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
			<!-- 快捷服务 -->
			<view class="services">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">服务</view>
					<!-- <view class="flex flex-vc more">
						<text>查看详情</text>
						<text class="iconfont icon-Rightyou"></text>
					</view> -->
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
					<view class="flex flex-vc more" @click="linkToCard('more')">
						<!-- <text>查看详情</text> -->
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="flex flex-hc statistics">
					<view class="flex-1 flex flex-v flex-vc flex-hc"
						v-for="(item, inx) in visits" :key="inx"
						 @click="linkToCard(item)">
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
							<text>{{item.position}}{{item.interviewee && item.interviewee.company ? ` | ${item.interviewee.company}` : ''}}</text>
						</view>
						<view class="counts">
							第 {{item.interviewee ? item.interviewee.visitNum || 0 : 0}} 次查看了我的名片{{itme.typedata_text ? `‘${item.typedata_text}’` : ''}}
						</view>
						<view class="from">
							来源：{{smartcardObj.origin[`${item.origin}`]}}
						</view>
					</view>
				</view>
				<view class="flex flex-hc flex-vc more" v-if="visitStaffLists.length > 0" @click="linkToCard('more')">
					<text>更多访客数据</text>
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
		</view>
		<!--  #ifdef  MP-WEIXIN	 -->
		<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom"></bottomSheet>
		<!--  #endif -->
		<myCardCode ref="myCardCode"></myCardCode>
	</view>
</template>

<script>
	import bottomSheet from '../../components/bbh-sheet/bottomSheet.vue';
	import myCardCode from '../../components/mycard-code/index.vue'
	import {smartcardObj, weixinShare} from '@/config/common.js'
	import {
		cdnUrl
	} from '@/config/config.js'
	export default {
		components:{
			bottomSheet,
			myCardCode
		},
		data() {
			return {
				cdnUrl,
				certificateStatus: false,
				isShare: false,
				bgColor:'bg-gradual-custom',
				backGround:'',
				smartcardObj: smartcardObj,
				isShowBottom : false,			//底部弹窗开关
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
					// {
					// 	id: 5,
					// 	disabled: false,
					// 	icon: 'icon-famingpian',
					// 	color: '#0256FF',
					// 	label: '发名片',
					// 	shareCode: '0' // 是否分享页还是默认页独有 0 无 1有 2都有
					// },
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
					id: 1,
					label: '访问量(次)',
					value: 0
				},
				{
					id: 2,
					label: '今日访问量(次)',
					value: 0
				},
				{
					id: 3,
					label: '发名片(次)',
					value: 0
				}],
				showEnterpriseVideo: true,
				showEnterpriseProfile: true,
				allData:'',
				userType: '', // 是否企业管理人员
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
				companyInfo: {},
				nickname:'',
				user_id: '', // 当前登陆人用户id
				staff_id: 0, // 分享出去的企业员工id
				mystaff_id:0,
				color: '',
				backgroundImg: '',
				cardimage: '',
				fontcolor: '',
				showGlance: false,
				myselfstatus: false,
				theme: {},
				origin: 1, // 来源:1=我向对方发出了名片,2=对方的名片夹,3=对方的名片浏览记录
			}
		},
		onLoad(e) {
			console.log('index调用onload',e); 
			// 分享给别人的名片id 有可能是自己的也有可能是别人的
			if(typeof(e.staff_id)== "undefined" || e.staff_id=='' ||  e.staff_id==null || e.staff_id=='null'){
				this.origin = e.origin
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
						label: '模板',
						url: '/pages/change/change'
					},
					{
						icon: 'icon-mingpianma',
						label: '名片码',
						doFun: this.openCode
					},
					{
						icon: 'icon-fenxiangshezhi',
						label: '分享设置',
						url: '/pages/share/setting'
					},
				]
			}else{
				this.isShare = true
				this.staff_id=e.staff_id;
				this.tools = this.tools.filter(i => (i.shareCode === '1' || i.shareCode === '2'))
				// this.services = [{
				// 		icon: 'icon-qiyejianjie',
				// 		label: '企业简介',
				// 		url: '/pages/company/contentUs/contentUs'
				// 	},
				// 	{
				// 		icon: 'icon-xiangce',
				// 		label: '我的相册',
				// 		url: '/pages/company/img/img'
				// 	},
				// 	{
				// 		icon: 'icon-qiyedongtai',
				// 		label: '企业动态',
				// 		url: '/pages/company/trends/trends'
				// 	},
				// 	{
				// 		icon: 'icon-qiyexuanchuance',
				// 		label: '企业宣传册',
				// 		url: '/pages/company/brochure/brochure'
				// 	},
				// 	{
				// 		icon: 'icon-chenggonganli',
				// 		label: '成功案例',
				// 		url: '/pages/company/case/case'
				// 	},
				// 	{
				// 		icon: 'icon-rexiaoshangpin',
				// 		label: '热销商品',
				// 		url: '/pages/company/goods/goods'
				// 	},
				// ]
				uni.setStorageSync('staff_id',e.staff_id)
			}
		},
		onShow() {
			this.refreshUser()
			// #ifdef MP-WEIXIN
			this.wxLogin();
			// #endif
		},
		onHide() {
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
		onShareAppMessage(res) {
			if (res.from === 'menu') {// 来自右上角分享按钮
			  console.log(res.target)
			}
			this.sendCard()
			console.info('this.companyInfo', this.companyInfo, 'share staff_id: ', this.staffInfo.id, 'share user_id: ', this.user_id)
			const staff_id = this.staffInfo.id; // 9
			const user_id = this.user_id; // 11
			return {
			  title: (this.companyInfo.name ? `${this.companyInfo.name}名片夹` : "名片夹"),
			  path: weixinShare.url + '?origin=1&isShare=1&staff_id=' + staff_id + '&user_id='+ user_id + '&pageTitle='+this.userData.name,
			  // imageUrl: "https://qiniu-web-assets.dcloud.net.cn/unidoc/zh/uni@2x.png",
			  type: weixinShare.type, // 0正式版 2体验版 1开发板
			}
		},
		methods: {
			showModalPhone() {
				uni.showModal({
					title: '需要读取您的联系人授权',
					content: '请前往“设置”开启相关权限',
					showCancel: false
					
				})
			},
			createPhoneMan() {
				uni.getSetting({
					success: (res) => {
						// console.info(res, '=============>>>')
						if (!res.authSetting['scope.addPhoneContact']) {
						  this.showModalPhone()
						} else {
							uni.authorize({
								scope: 'scope.addPhoneContact',
								success: () => {
									  uni.addPhoneContact({
											mobilePhoneNumber: this.staffInfo.mobile,
											firstName: this.staffInfo.name,
											// nickName: this.staffInfo.name,
											organization: this.staffInfo.companyname,
											fail(err) {
												console.info('err......phone', err)
											}
									  })
								},
								fail(err) {
									this.showModalPhone()
								}
							})
						}
					},
					fail(err) {
						this.showModalPhone()
					}
				})
				
			},
			linkToCert() {
				// console.info(this.certificateStatus, '=====', this.userData.usertype, '====usertype')
				if(!this.certificateStatus && this.userData.usertype === '1') { // 企业负责人（管理员）
					uni.navigateTo({
						url: '/pages/company-attestation/index'
					})
				} else if(!this.certificateStatus && this.userData.usertype === '0') { // 普通用户
					uni.navigateTo({
						url: '/pages/user-attestation/index'
					})
				} 
			},
			openCode() {
				this.$refs.myCardCode && this.$refs.myCardCode.open();
			},
			linkToCard(row) {
				if(row !== 'more') return; // 暂时只有查看更多能跳转
				uni.switchTab({
					url: '/pages/mycard-data/index'
				})
			},
			sendCard() {
				this.$api.sendCard({
					staff_id: this.staffInfo.id
				}, res => {
					if(res.code === 1) {
						// uni.showToast({
						// 	icon: 'none',
						// 	title: res.msg
						// })
						this.visits.find(i => i.id === 3).value++
					}
				})
			},
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
				}  else if(row.id === 2 && this.staffInfo.wechat) {
					uni.setClipboardData({
						data: this.staffInfo.wechat,
						success() {
							uni.showToast({
								icon:'none',
								title: '复制成功，可前往加好友'
							})
						}
					})
				} else if(row.id === 3 && this.staffInfo.email) {
					uni.setClipboardData({
						data: this.staffInfo.email,
						success() {
							uni.showToast({
								icon:'none',
								title: '复制成功，可前去发邮件'
							})
						}
					})
				} else if (row.id === 4 && this.companyInfo.latitude && this.companyInfo.longitude) {
					uni.openLocation({
						latitude: this.companyInfo.latitude,
						longitude: this.companyInfo.longitude
					})
				} else if(row.id === 5) { // 发名片
					
					// 小程序不支持？
					uni.share({
						provider: "weixin",
						scene: "WXSceneSession",
						type: 0,
						// href: "http://uniapp.dcloud.io/",
						miniProgram: {
							id: smartcardObj.weixinId,
							path: '/pages/myCard/myCard?staff_id'+this.staff_id,
							type: 2,
							webUrl: ''
						},
						title: this.companyInfo.name || "名片夹",
						summary: "我正在使用HBuilderX开发uni-app，赶紧跟我一起来体验！",
						imageUrl: "https://qiniu-web-assets.dcloud.net.cn/unidoc/zh/uni@2x.png",
						success: function (res) {
							console.log("success:" + JSON.stringify(res));
						},
						fail: function (err) {
							console.log("fail:" + JSON.stringify(err));
						}
					});
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
				let condition = {
					staff_id: this.staff_id,
					// user_id: this.user_id
				}
				let api = this.$api.doIndex
				if(this.isShare) {
					api = this.$api.doIndexShare
					condition.origin = this.origin
				}
				api(condition, (res) => {
					if(res.code === 1) {
						if(res.isStaff === 0) {
							uni.navigateTo({
								url: '/pages/userInfo/userInfo?user_id=' + this.user_id
							})
							return
						}
						this.allData=res.data
						console.log(this.allData);
						this.usertype=this.allData.usertype;     //是否是企业负责人（0：不是  1：是）
						this.userData = this.staffInfo = this.allData.staffInfo || {};
						this.myCardData = this.allData.myCardData || {};
						this.visitStaffLists = this.allData.visitStaffLists || [];
						this.videofiles = this.allData.videofiles || [];
						this.description = this.allData.description || '';
						this.updatetime=this.allData.newsTime
						this.companyInfo = this.staffInfo.smartcardcompany || {};
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
						this.nickname=this.userData.name
						this.visits[0].value = this.myCardData.allVisitNum
						this.visits[1].value = this.myCardData.todayVisitNum
						this.visits[2].value = this.myCardData.sendCardNum
						if(!this.staffInfo.mobile) this.tools.find(i => i.id === 1).disabled = true
						if(!this.staffInfo.wechat) this.tools.find(i => i.id === 2).disabled = true
						if(!this.staffInfo.email) this.tools.find(i => i.id === 3).disabled = true
						if(!(this.staffInfo.smartcardcompany && (this.staffInfo.smartcardcompany.longitude && 
							this.staffInfo.smartcardcompany.latitude))) this.tools.find(i => i.id === 4).disabled = true
						// if(!this.staffInfo.mobile) this.tools.find(i => i.id === 5).disabled = true
						this.tools.forEach(it => {
							if(it.disabled) it.color = '#999';
						})
						this.userData.save_status = `${this.allData.save_status}`
						this.userData.usertype = `${this.allData.usertype}`
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
						
						if(this.isShare) {
							this.services = this.allData.services || []
						}
						if(this.userData.is_certified === 0){
							this.certificateStatus=false;
						}
						
						console.info('首页请求的接口名称： ', api, 
							'allData', this.allData, 'isShare', this.isShare, 
							'services', this.services)
					}else {
						uni.navigateTo({
							url:'/pages/userInfo/userInfo?user_id=' + this.user_id
						})
					}
				})
			},
			linkToService(row) {
				if(row.url) {
					uni.navigateTo({
						url: `${row.url}?user_id=${this.user_id}`
					})
				}
				if(row.doFun) row.doFun();
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
		position: relative;
		height: 360rpx;
		box-sizing: border-box;
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
		padding: 40rpx 40rpx;
		margin-top: 30rpx;
		border-radius: 15px;
		color: #e1d27e;
		box-shadow: 0 0 10px #999;
		position: relative;
		overflow: hidden;
	}
	
	.userImg {
		position: relative;
		padding-bottom: 20rpx;
		border-bottom: 1rpx solid #D2E7FF;
		margin-bottom: 16rpx;
	}
	
	.userImg::after {
		content: '';
		position: absolute;
		bottom: -2rpx;
		left: 0;
		width: 100%;
		border-bottom: solid 1rpx #FAFDFF;
		height: 0;
	}
	
	.userImg image {
		display: block;
		width: 130rpx;
		height: 130rpx;
		border-radius: 50%;
	}
	
	.cert-status {
		position: absolute;
		right: -4rpx;
		top: 40rpx;
		width: 106rpx;
		height: 44rpx;
		line-height: 44rpx;
		font-size: 20rpx;
		font-weight: 400;
		text-align: center;
		box-sizing: border-box;
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}
	
	.cert-status.waitOp {
		color: #8897AD;
	}
	
	.cert-status.op {
		color: #fff;
	}
	
	.extra {
		font-size: 24rpx;
	}
	
	.extra>view {
		margin-bottom: 8rpx;
	}
	
	.extra .iconfont {
		width: 30rpx;
		height: 30rpx;
		background: #3658FF;
		border-radius: 50%;
		font-size: 20rpx;
		line-height: 32rpx;
		text-align: center;
		color: #fff!important;
		margin-right: 8rpx;
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
	
	.isHC-box {
		position: absolute;
		bottom: -1rpx;
		right: -1rpx;
		width: 100rpx;
		height: 100rpx;
	}
	.isHC-box::after {
		content: '';
		position: absolute;
		bottom: 0;
		right: 0;
		width: 0;
		height: 0;
		border-top: 0 solid skyblue;
		border-bottom: 100rpx solid skyblue;
		border-left: 100rpx solid transparent;
		border-right: 0 solid transparent;
	}
	
	.isHC-box .bg {
		position: relative;
		top: 41rpx;
		z-index: 99;
		left: 24rpx;
		transform: rotate(-45deg);
		font-size: 24rpx;
		color: #fff;
		font-weight: 400;
	}
	
	
	/* 内容 */

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
		width: 64rpx;
		height: 64rpx;
		line-height: 64rpx;
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
	.share-btn {
		margin-bottom: 20rpx;
	}
	button.share-btn[type="primary"] {
		background-color: #0256FF;
	}
</style>