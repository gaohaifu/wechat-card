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
					<!-- <view class="grade">{{userData.shortname}}</view> -->
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
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-dianhua"></i><text :style="{color:fontcolor}">{{userData.mobile?userData.mobile:'暂未填写'}}</text></view>
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-youjian1"></i><text :style="{color:fontcolor}">{{userData.email?userData.email:'暂未填写'}}</text></view>
						<view class="flex_layout" v-if="companyInfo.address"><i :style="{color:fontcolor}" class="iconfont icon-weizhi"></i><text :style="{color:fontcolor}">{{companyInfo.address?companyInfo.address:'暂未填写'}}</text></view>
					</view>
					<!--切换按钮-->
					<view class="change_tab flex_layout" v-if="user_id==userData.user_id" @click="changeTab"><image src="../../static/images/change.png" mode=""></image><text>切换</text></view>
				</view>
			</view>
		</view>
		<view class="overall_padding">
			<view class="share_btn flex_layout">
				<!--  #ifdef  MP-WEIXIN	 -->
				<block v-if="userStaff">
					<block v-if="user_id==userData.user_id">
						<view class="left_share"><button :style="{color:color,border: `1px solid ${color}`}" open-type="share">分享我的名片</button></view>
						<view class="right_share" @click="maintenance">
							<button :style="{background:color,border: `1px solid ${color}`}">名片维护</button>
						</view>
					</block>
					<block v-else>
						<view class="left_share"><button :style="{color:color,border: `1px solid ${color}`}" open-type="share">介绍TA给朋友</button></view>
						<view class="right_share"><button :style="{background:color,border: `1px solid ${color}`}" @click="saveToContact(userData.name,userData.mobile,userData.position,companyInfo.name,userData.email,userData.address)">保存名片到手机</button></view>
					</block>
				</block>
				<block v-else>
					<view class="left_share"><button :style="{color:color,border: `1px solid ${color}`}" open-type="share">介绍TA给朋友</button></view>
					<view class="right_share"><button :style="{background:color,border: `1px solid ${color}`}" @click="saveToContact(userData.name,userData.mobile,userData.position,companyInfo.name,userData.email,userData.address)">保存名片到手机</button></view>
				</block>
			<!--  #endif -->
			<!--  #ifndef  MP-WEIXIN	 -->
			<block v-if="userStaff">
				<block v-if="user_id==userData.user_id">
				<view  class="left_share"><button :style="{background:color,border: `1px solid ${color}`}" @click="shareH5()"><text class="margin-right-xs iconfont icon-fenxiangxiao"></text>分享我的名片</button></view>
				<view class="right_share" @click="maintenance">
						<button :style="{background:color,border: `1px solid ${color}`}"><text style="font-size: 46rpx;" class="margin-right-xs iconfont icon-weizhixinxiweihu"></text>名片维护</button>
					</view>
				</block>
				<block v-else>
					<view class="full_share"><button :style="{background:color,border: `1px solid ${color}`}" @click="shareH5()"><text style="font-size:36rpx" class="margin-right-xs iconfont icon-jieshao"></text>介绍TA给朋友</button></view>
				</block>
			</block>
			<!--  #endif -->
			
			</view>
			<view class="contact_way">
				<scroll-view class="scroll-view" scroll-x="true" @scroll="scroll">
					<view class="contactWay_item" @click="phone(userData.mobile)" v-if="userData.mobile">
						<view class="contactWay_shadow">
							<view class="contactWay_title flex_layout"><i :style="{color:color}" class="iconfont icon-bodadianhua_"></i>拨打电话</view>
							<view class="contactWay_content">{{userData.mobile}}</view>
						</view>
					</view>
					<view class="contactWay_item" v-if="userData.wechat">
						<view class="contactWay_shadow"  @click="copyCode(userData.wechat,'wechat')">
							<view class="contactWay_title flex_layout"><i :style="{color:color}" class="iconfont icon-weixin"></i>加我微信</view>
							<view class="contactWay_content">{{userData.wechat}}</view>
						</view>
					</view>
					<view class="contactWay_item"  v-if="userData.email">
						<view class="contactWay_shadow"   @click="copyCode(userData.wechat,'email')">
							<view class="contactWay_title flex_layout"><i :style="{color:color}" class="iconfont icon-_youxiang"></i>邮箱</view>
							<view class="contactWay_content">{{userData.email}}</view>
						</view>
					</view>
					<view class="contactWay_item"  v-if="companyInfo.address">
						<view class="contactWay_shadow">
							<view class="contactWay_title flex_layout"><i :style="{color:color}" class="iconfont icon-dizhi"></i>地址</view>
							<view class="contactWay_content">{{companyInfo.address}}</view>
						</view>
					</view>
				</scroll-view>
			</view>
			<view class="glance_over flex_layout" v-if="showGlance.length>0">
				<view class="flex_layout">
					<view class="flex_layout glance_img">
						<block v-for="(item,index) in showGlance" :key="index">
							<image v-if="index<6" :src="item" mode=""></image> 
						</block>
					</view>
					<view class="leaveOut" v-if="allData.visitStaffNum>6">...</view>
					<view class="peopleNum">{{allData.visitStaffNum}}人浏览</view>
				</view>
				<view class="right_thumb flex_layout" v-if="allData.favorStaffNum">
					靠谱{{allData.favorStaffNum}}
					<block v-if="allData.isFavor">
						<!--已点赞状态-->
						<view @click="helpBtn(0)" :style="{color:color}" class="iconView iconfont icon-thumbup-fill"></view>
					</block>
					<block v-else>
						<!--未点赞状态-->
						<view @click="helpBtn(1)" class="iconView iconfont icon-dianzan1"></view>
					</block>
				</view>
			</view>
			<view class="operate_items flex_layout">
				<view class="operate_left flex_layout">服务</view>
				<view class="operate_right flex_layout" @click="changeLayout()">
					<image style="width:20px; height:20px" src="../../static/images/change1.png" mode=""></image>
				</view>
			</view>
			<view class="footer_content" :class="layoutStatus?'footer_content_layout':''">
				<view class="operate_item flex_layout" @click="userInfo">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon1.png"></image> --><i :style="{color:color}" class="iconfont icon-gerenjianjie f64"></i>个人简介</view>
					<view class="operate_right flex_layout">
						<view class="label_flex flex_layout">
							<view v-for="(item,index) in allData.tagsLists" :key="index">{{item.name}}</view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				
				<!-- <view class="operate_item flex_layout" @click="video">
					<view class="operate_left flex_layout"><i :style="{color:color}" class="iconfont icon-shipin f60"></i>我的视频</view>
					<view class="operate_right flex_layout">
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view> -->
				<view class="operate_item flex_layout" @click="img">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon3.png"></image> --><i :style="{color:color}" class="iconfont icon-image f52"></i>我的相册</view>
					<view class="operate_right flex_layout">
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" @click="contentUs">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon4.png"></image> --><i :style="{color:color}" class="iconfont icon-qiye f52"></i>企业简介</view>
					<view class="operate_right flex_layout">
						<view class="respectNum">
							<view class="respectImg flex_layout"><image v-for="(item,index) in allData.visitCompanyLists" :key="index" :src="item.avatar" mode=""></image></view>
							<view class="respectText">已有<text>{{allData.visitCompanyNum?allData.visitCompanyNum:0}}</text>人查看过该企业简介</view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" @click="brochure">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon5.png"></image> --><i :style="{color:color}" class="iconfont icon-65yingjishouce f52"></i>企业宣传册<text>（{{allData.designNum?allData.designNum:0}}本）</text></view>
					<view class="operate_right flex_layout">
						<view class="respectNum">
							<view class="respectImg flex_layout"><image  v-for="(item,index) in allData.visitDesignLists" :key="index" :src="item.avatar" mode=""></view>
							<view class="respectText">已递给<text>{{allData.visitDesignNum?allData.visitDesignNum:0}}</text>位客户</view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" @click="caseTab">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon6.png"></image> --><i :style="{color:color}" class="iconfont icon-shujia f68"></i>成功案例<text>（{{allData.casesNum?allData.casesNum:0}}个）</text></view>
					<view class="operate_right flex_layout">
						<view class="respectNum">
							<view class="respectImg flex_layout"><image  v-for="(item,index) in allData.visitCasesLists" :key="index" :src="item.avatar" mode=""></view>
							<view class="respectText">已分享<text>{{allData.visitCasesNum?allData.visitCasesNum:0}}</text>位客户</view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" @click="goods">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon7.png"></image> --><i :style="{color:color}" class="iconfont icon-shangpin1 f56"></i>热销商品<text>（{{allData.goodsNum?allData.goodsNum:0}}款）</text></view>
					<view class="operate_right flex_layout">
						<view class="respectNum">
							<view class="respectImg flex_layout"><image  v-for="(item,index) in allData.visitGoodsLists" :key="index" :src="item.avatar" mode=""></view>
							<view class="respectText">被<text>{{allData.visitGoodsNum?allData.visitGoodsNum:0}}</text>位客户了解过</view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" @click="trends">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon8.png"></image> --><i :style="{color:color}" class="iconfont icon-017-webbrowser f52"></i>企业动态</view>
					<view class="operate_right flex_layout" v-if="updatetime">
						<view class="respect_trends">{{updatetime}} <text>更新</text></view>
						<image src="../../static/images/right.png" mode=""></image>
					</view> 
				</view>
				<!--消息列表-->
				<view class="operate_item flex_layout" @click="news">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon9.png"></image> --><i :style="{color:color}" class="iconfont icon-weixin f56"></i>消息列表</view>
					<view class="operate_right">
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				
				<view class="operate_item flex_layout" @click="colleague" v-if="usertype==1 && !myselfstatus">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon9.png"></image> --><i :style="{color:color}" class="iconfont icon-webicon306 f64"></i>员工维护</view>
					<view class="operate_right flex_layout">
						<view class="respectNum">
						  <view class="respectText">员工数量<text>{{allData.staffnum?allData.staffnum:0}}</text></view>
						</view>
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				<view class="operate_item flex_layout" v-if="user_id" @click="dropOut">
					<view class="operate_left flex_layout"><!-- <image src="../../static/images/icon9.png"></image> --><i :style="{color:color}" class="iconfont icon-tuichu f56"></i>退出登录</view>
					<view class="operate_right">
						<image src="../../static/images/right.png" mode=""></image>
					</view>
				</view>
				
			</view>
			<view class="fullBottom" v-if="isShowBottom"><image src="../../static/images/bg1.jpg" mode="aspectFill"></view>
			<!--  #ifdef  MP-WEIXIN	 -->
			<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom"></bottomSheet>
			<!--  #endif -->
			<view class="bottomtag flex_layout" v-if="myselfstatus">
				<!--  #ifdef  MP-WEIXIN	 -->
				<button :style="{border:'1px solid '+color,color:color}" @click="mysmartcard()">我的数字名片</button> 
				<button  :style="{background:color,color:fontcolor}" open-type="share">分享Ta的名片</button>
				<!-- #endif -->
				<!--  #ifndef MP-WEIXIN -->
				<button :style="{border:'1px solid '+color,color:color}" @click="mysmartcard()">我的数字名片</button>
				<button  :style="{background:color,color:fontcolor}"  @click="shareH5()">分享Ta的名片</button>
				<!-- #endif -->
				
			</view>
		</view>
	</view>
</template>

<script>
	var _this;
	import {cdnUrl,baseUrl} from '../../config/config.js';
	import bottomSheet from '../../components/bbh-sheet/bottomSheet.vue';
	export default {
		components:{
			bottomSheet
		},
		data() {
			return {
				cdnUrl:cdnUrl,
				bgColor:'bg-gradual-custom',
				color:'',
				glance:['../../static/images/user.png','../../static/images/user.png','../../static/images/user.png','../../static/images/user.png','../../static/images/user.png','../../static/images/user.png'],
			    showGlance:[],
				helpNum:8,
				helpStatus:true,
				staff_id:0,
				allData:'',
				userData:{
					nickname:'',
					name:'',
					avatar:'',
					position:''
				},
				repeatTab:true,
				userStaff:true,
				updatetime:'',
				administrators_id:'',
				user_id:'',
				usertype:'',
				isShowBottom : false,			//底部弹窗开关
				code:'',
				scrollTop:'',   //滚动条位置
				backGround:'',
				backgroundImg:'',
				cardimage:'',
				fontcolor:'',
				layoutStatus:true,
				companyInfo:[],
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
				myselfstatus:false,
				mystaff_id:0
				
			}
		},
		onLoad(e) {
			console.log('index调用onload',e);
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
		mounted() {
			_this= this;
		},
		onShow() {
			this.refreshUser()
			// #ifdef MP-WEIXIN
			this.wxLogin();
			// #endif
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
		//发送给朋友
		onShareAppMessage(res) {
			console.log(res);
		  return {
		      title:'这是'+this.userData.name+'的名片',
		      path:'/pages/index/index?staff_id='+this.staff_id,
		      //imageUrl:res.target.dataset.img,
		      //desc:this.sharedata.desc,
		      //content:this.sharedata.content,
		      success(res){
		          uni.showToast({
		              title:'分享成功'
		          })
		      },
		      fail(res){
		          uni.showToast({
		              title:'分享失败',
		              icon:'none'
		          })
		      }
		  }
		},
		
		
		// 加载更多
		onReachBottom: function() {
			
		},
		methods: {
			colleague(){
					var transmit=JSON.stringify(this.transmit);
					uni.navigateTo({
						url:'colleague?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
					})
				// }else{
				// 	uni.showToast({
				// 		title:'暂无权限',
				// 		icon:'none'
				// 	})
				// 	return false;
				// }
			},
			mysmartcard(){
				this.staff_id=0;
				uni.setStorageSync('staff_id',0)
				this.mystaff_id=0;
				console.log(this.mystaff_id);
				uni.navigateTo({
					url:'/pages/index/index?staff_id='+this.mystaff_id
				})
			},
			//复制微信号
			copyCode(value,type) {
				var msg='';
				if(type=='wechat'){
					var msg='微信号';	
				}
				if(type=='email'){
					var msg='邮箱号';	
				}
				uni.setClipboardData({
					data: value, 
					success: () => {
						uni.showToast({
							title: "复制"+msg+"成功"
						})
					}
				});
			},
			shareH5(){
				var url=baseUrl+'/pages/index/index?staff_id='+this.mystaff_id
				uni.setClipboardData({
									data: url, 
									success: () => {
										uni.showModal({
											title: '提示',
											content: '确认复制分享链接？请将链接粘贴发送给客户。',
											cancelText: "取消", // 取消按钮的文字  
											confirmText: "确认", // 确认按钮的文字  
											showCancel: false, // 是否显示取消按钮，默认为 true
											confirmColor: '#4DB6AC',
											cancelColor: '#999',
											success: (res) => {
											if(res.confirm) {  
												this.$common.successToShow('复制成功');
											} else {  
												console.log('cancel') //点击取消之后执行的代码
												}  
											} 
										})
										
									}
								});
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
						this.indexData();
					}else{
						//微信小程序端
						// #ifdef MP-WEIXIN
						console.log("小程序: ",1);
						// uni.showToast({
						// 	title:'分享者信息不存在！',
						// 	icon:"none"
						// })
						this.isShowBottom=true
						this.userStaff=false
						this.user_id='';
						this.indexData();
						// #endif
						//h5端
						// #ifdef H5
						console.log("h5: ",1);
						if(this.staff_id==0){
							this.$common.navigateTo('../user/login');
						}else{
							this.indexData();
						}
						//
						this.indexData();
						// #endif
						//app端
						//#ifdef APP-PLUS
						if(this.staff_id==0){
							this.$common.navigateTo('../user/login');
						}else{
							this.indexData();
						}
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
										this.indexData()
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
			changeTab(){
				uni.navigateTo({
					url:'change?staff_id='+this.staff_id+'&user_id='+this.user_id
				})
			},
			//底部开关
			closeBottom(){
				this.isShowBottom = false;
				this.onGetUserProfile()
			},
			maintenance(){
				let user_id=this.user_id;
				let company_id=this.companyInfo.id;
				uni.navigateTo({
					url:'../user/userInfo?user_id='+user_id+'&company_id='+company_id
				})
			},
			//首页全部信息接口（包含个人信息）
			indexData(){
				var staff_id_c=this.staff_id!=0 && this.staff_id!=null?this.staff_id:uni.getStorageSync('staff_id')
				const parm={
					staff_id:staff_id_c,
					user_id:this.user_id
				};
				var that=this;
				this.$api.indexData(
					 parm,
					data => {
						if(data.code==1){
							this.allData=data.data
							console.log(this.allData);
							this.usertype=data.data.usertype;     //是否是领导角色（0：不是  1：是）
							this.userData=data.data.staffInfo
							if(this.userData.statusdata!='1'){
								this.certificateStatus=false;
							}
							this.companyInfo=data.data.companyInfo
							this.updatetime=data.data.newsTime
							this.color=data.data.staffInfo?data.data.staffInfo.smartcardtheme.colour:''
							this.backgroundImg=data.data.staffInfo?data.data.staffInfo.smartcardtheme.backgroundimage:''
							this.cardimage=data.data.staffInfo?data.data.staffInfo.smartcardtheme.cardimage:''
							this.fontcolor=data.data.staffInfo?data.data.staffInfo.smartcardtheme.fontcolor:''          
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
							this.showGlance=data.data.visitStaffLists.map(item=>{
								return item.avatar
							})
							this.staff_id=data.data.staffInfo.id
							if(data.data.userInfo){
								this.mystaff_id=data.data.userInfo.staff_id;
							}else{
								this.mystaff_id=0
							}
							
							if(this.staff_id==this.mystaff_id){
								this.myselfstatus=false
							}else{
								this.myselfstatus=true
							}
							this.transmit={
								company_id:this.companyInfo.id,
								//nickname:this.userData.name,
								position:this.userData.position,
								shortname:this.companyInfo.name,
								avatar:this.userData.avatar,
								phone:this.userData.mobile,
								wxQRCodeimage:this.userData.wxQRCodeimage,
								wechat:this.userData.wechat,
								staff_id:this.userData.id 
							};
							this.nickname=this.userData.name
						}else{
							if(this.user_id!=0 || this.user_id!=''){
								if(staff_id_c!=''){
									uni.showToast({
										title:'无此用户名片信息,即将跳转到个人名片主页...',
										icon:'none',
										duration:1500
									})
									setTimeout(()=>{
										uni.navigateTo({
											url:'index'
										})
									},2000)
									return false;
							    }
							}
							this.$common.errorToShow(data.msg,function(){
								if(staff_id_c==undefined || staff_id_c==null  ||  staff_id_c=='' || staff_id_c==0){
									if(that.user_id!=0){
											uni.navigateTo({
												url:'../user/addInfo'
											})
									}
								}
							})
						}
						
					})
			},
			
			//保存到通讯录
			saveToContact(name,phone,position='',companyName='',email='',address){
				uni.addPhoneContact({
				    nickName: name,
				    lastName: '',
				    firstName: name,
				    title: position,
				    mobilePhoneNumber: phone, //手机号
				    organization: companyName,
					email:email,
					workAddressStreet:address,
				    success: function () {
				        console.log('success');
				    },
				    fail: function () {
				        console.log('fail');
				    }
				});
			},
			changeLayout(){
			  this.layoutStatus=!this.layoutStatus;
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
			scroll(e){
				console.log(e);
			},
			//点赞按钮
			helpBtn(type){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				if(!this.repeatTab){
					return false;
				}
				this.repeatTab=false
				const parm={
					staff_id:this.staff_id,
				};
				this.$api.favorOptionData(
					 parm,
					data => {
						if(data.code==1){
							this.repeatTab=true
							//点赞数量
							this.allData.favorStaffNum=data.data.favorNum
							if(type==0){
								//已点赞状态，取消点赞操作
								this.allData.isFavor=0
							}else{
								//未点赞状态，点赞操作
								this.allData.isFavor=1
							}
						}
					})
			},
			// NoHelpBtn(){
			// 	this.helpStatus=true
			// 	this.helpNum--
			// },
			userInfo(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'message?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			contentUs(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				
				uni.navigateTo({
					url:'contentUs?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			img(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'img?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			video(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'video?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			brochure(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'brochure?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			caseTab(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'case?transmit='+transmit+'&nickname='+this.nickname+'&usertype='+this.usertype
				})
			},
			goods(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'goods?transmit='+transmit+'&nickname='+this.nickname+'&usertype='+this.usertype
				})
			},
			trends(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'trends?transmit='+transmit+'&nickname='+this.nickname+'&usertype='+this.usertype
				})
			},
			
			news(){
				if(this.user_id=='' || this.user_id==0){
					this.$common.navigateTo('../user/login');
					return false
				}
				var transmit=JSON.stringify(this.transmit);
				uni.navigateTo({
					url:'newsList?transmit='+transmit+'&nickname='+this.nickname+'&staff_id='+this.staff_id
				})
			},
			dropOut(){
				uni.showModal({
					title: '提示',
					content: '是否确认退出登录？',
					success: (res)=> {
						if (res.confirm) {
							this.logout()
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					}
				});
			},
			//退出登录
			logout(){
				let that=this;
				this.$api.logout(
				 {},
				data => {
					if(data.code==1){
						//this.$common.successToShow(data.msg,function(){
							uni.showToast({
								title:data.msg,
								icon:'none',
								success: () => {
									this.wxLogin()
									setTimeout(()=>{
										that.user_id='';
										that.$db.del('upload', 1)
										that.$db.del('login', 1)
										that.$db.del('color')
										that.$db.del('user')
										that.$db.del('auth')
										// #ifdef MP-WEIXIN
										console.log("小程序: ",1);
										// uni.showToast({
										// 	title:'分享者信息不存在！',
										// 	icon:"none"
										// })
										this.$common.navigateTo('../user/login');
										//this.isShowBottom=true
										this.userStaff=false
										this.user_id='';
										
										// #endif
										//h5端
										// #ifdef H5
										console.log("h5: ",1);
										this.$common.navigateTo('../user/login');
										// #endif
										//app端
										//#ifdef APP-PLUS
										this.$common.navigateTo('../user/login');
										// #endif
									},1500)
									
								}
							})
						//});
					}
				})
			}
		}
	}	
</script>

<style>
	@import url('../../common/common.css');
	page{background: #fefefe;}
	.header_background{background-repeat: no-repeat; background-position: left top; background-size: cover;}
	.header_padding{padding: 0 30rpx;}
	.overall_padding{padding: 30rpx 15rpx;}
	.grade{text-align: right; font-size: 26rpx; margin-top: 20rpx;}
	.header_message{background-repeat: no-repeat; background-position: left top; background-size: cover; padding: 40rpx 40rpx; margin-top: 30rpx; border-radius: 15px; color: #e1d27e; box-shadow: 0 0 10px #999; position: relative;}
	.userImg image{display: block; width: 130rpx; height: 130rpx; border-radius: 50%;}
	.name_position{padding-left: 30rpx; width: 400rpx;}
	.name_position view{font-size: 42rpx; width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
	.name_position text{display: block; font-size: 26rpx; margin-top: 10rpx; width: 100%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
	.extra{margin-top: 60rpx;}
	.extra>view{font-size: 24rpx; margin-top: 20rpx;}
	.extra i{color: #5a544e; line-height: 1;}
	.extra text{color: #8d887f; margin-left: 12rpx;}
	.share_btn{justify-content: space-between; padding: 30rpx 0;}
	.share_btn view{width:-webkit-calc(50% - 15rpx); width:-moz-calc(50% - 15rpx); width:calc(50% - 15rpx);}
	.share_btn view button{width: 100%; height: 80rpx; border-radius: 8px; border: 1px solid #cccccc; display: flex; align-items: center; justify-content: center; margin: 0; padding: 0; font-size: 32rpx;}
	.left_share button{color: #cccccc; background: #fff;}
	.right_share button{color: #fff; background: #cccccc;}
	.full_share{ width: 100% !important; display: block;}
	.full_share button{width:100% !important;color: #fff; background: #cccccc;}
	.scroll-view{overflow-x: auto; overflow-y: hidden; width: 100%; white-space: nowrap; margin: 0 -10rpx;}
	.contactWay_item{padding: 10rpx; display: inline-block;}
	.contactWay_shadow{border-radius: 5px; box-shadow: 0 0 5px #ccc; padding: 20rpx 30rpx;}
	.contactWay_title{color: #333; font-size: 28rpx;}
	.contactWay_title i{color: #cccccc; margin-right: 15rpx;}
	.contactWay_content{margin-top: 15rpx; color: #666; font-size: 24rpx;}
	.glance_img image{display: block; width: 40rpx; height: 40rpx; margin-right: 10rpx; border-radius: 3px;}
	.glance_over{padding: 20rpx 0; justify-content: space-between;}
	.leaveOut{color: #666; font-size: 24rpx; letter-spacing: 3rpx;}
	.peopleNum{margin-left: 20rpx; color: #333; font-size: 28rpx;}
	.right_thumb{color: #333; font-size: 28rpx;}
	.iconView{color: #333; font-size: 48rpx; margin-left: 10rpx;}
	.operate_right image{width: 12rpx; height: 22rpx; display: block;}
	.operate_item{padding: 0 25rpx; height: 130rpx; justify-content: space-between; margin-bottom: 25rpx; background-color: #fff; border-radius: 8px; box-shadow: 0 0 10px #eee;}
	.operate_items{padding: 0 20rpx; height: 130rpx; justify-content: space-between; margin-bottom: 20rpx; background-color: #fff; }
	.operate_left{font-size: 32rpx;}
	.operate_left i{color: #cccccc; margin-right: 10rpx;}
	.operate_left image{display: block; width: 42rpx; height: 42rpx; margin-right: 10rpx;}
	.operate_left text{font-size: 24rpx; color: #999;}
	.label_flex{padding-right: 10px;}
	.label_flex view{color: #333; font-size: 24rpx; padding: 10rpx; border: 1px solid #ccc; line-height: 1; margin-right: 10rpx;}
	.respectNum{padding-right: 20rpx;}
	.respectImg{justify-content: flex-end;}
	.respectImg image{width: 48rpx; height: 48rpx; border: 3px solid #fff; border-radius: 50%; margin-right: -20rpx;}
	.respectImg image:last-child{margin: 0;}
	.respectText{color: #999; font-size: 22rpx; margin-top: 5rpx;}
	.respectText text{color: #333;}
	.respect_trends{color: #333; font-size: 24rpx; padding-right: 20rpx;}
	.respect_trends text{font-size: 28rpx; margin-left: 15rpx;}
	.hide{display:none}
	.change_tab{background: rgba(0,0,0,0.5); border-radius: 100rpx 0 0 100rpx; padding: 10rpx 15rpx; box-shadow: 0 0 10px #ccc; position: absolute; right: 0; bottom: 40rpx; z-index: 100;}
	.change_tab text{color: #fff; font-size: 24rpx; margin-left: 10rpx;}
	.change_tab image{display: block; width: 30rpx; height: 30rpx;}
	.footer_content_layout{display: flex; align-items: center; flex-wrap: wrap;}
	.footer_content_layout .operate_item{width: 33.33%; display: block; height: auto; padding: 20rpx 0; box-shadow: none; margin-bottom: 0;}
	.footer_content_layout .operate_left{display: block; font-size: 26rpx; text-align: center;}
	.footer_content_layout .operate_left i{display: block; text-align: center; margin-bottom: 20rpx; margin-right: 0; height: 70rpx;}
	/* .footer_content_layout .operate_left text{display: inline-block;} */
	.footer_content_layout .operate_right{display: none;}
	
	.f68{font-size: 68rpx;}
	.f64{font-size: 64rpx;}
	.f60{font-size: 60rpx;}
	.f56{font-size: 56rpx;}
	.f52{font-size: 52rpx;}
	.f48{font-size: 48rpx;}
	.waitOp{ position: absolute; right: 20rpx; top: 50rpx; }
	.fullBottom{ position: fixed; top: 0; left: 0; height: 100%; width: 100%;}
	.fullBottom image{ height: 100%; width: 100%;}
	.bottomtag{ bottom:0; position:fixed; left:0; width:100%; padding:20rpx; height:120rpx; background:#fff; box-shadow:-5px 3px 3px #ccc}
	.bottomtag button{ font-size:28rpx; border-radius:50rpx; background:#fff}
</style>

