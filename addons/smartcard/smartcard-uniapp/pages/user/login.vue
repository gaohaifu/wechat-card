<template>
	<view class="content">
	  <!-- <cu-custom bgColor="bg-blue" :isBack="true"><block slot="backText">返回</block><block slot="content">登录</block></cu-custom> -->
		<block v-if="ismobile">
			<view class="">
				<view class="login-card">
					<view class="login-head">{{title}}</view>
					<view class="login-input login-margin-b">
						<input type="number" v-model="username" placeholder="请输入手机号" />
					</view>
					<view class="login-input">
						<input type="number" :password="true" v-model="password" placeholder="请输入密码(6-16位)" />
					</view>
					<view class="margin-top flex justify-center">
						<button class="cu-btn bg-blue shadow-blur round" :loading="loading"   @tap="login"  :style="{background:color}">{{ loading ? "登录中...":"手机登录"}}</button>
					</view>
					<view class="flex justify-center">
						<view class="text-gray text-sm margin-top-xl" :style="{color:color}" @tap="wechatLogin">微信登录</view>
					</view>
				</view>
			</view>
		</block>
		<block v-else>
			<!--  #ifndef  MP-WEIXIN	 -->
			<view class="login-bg">
				<view class="login-card">
					<view class="login-head">{{title}}</view>
					<view class="login-input login-margin-b">
						<input type="number" v-model="username" placeholder="请输入手机号" />
					</view>
					<view class="login-input">
						<input type="number" :password="true" v-model="password" placeholder="请输入密码(6-16位)" />
					</view>
					<view class="cu-bar btn-group margin-top">
						<button class="cu-btn bg-blue shadow-blur round" :loading="loading"   @tap="login"  :style="{background:color}">{{ loading ? "登录中...":"登 录"}}</button>
					</view>
					<view class="flex justify-center">
						<view class="text-gray text-sm margin-top-xl" :style="{color:color}" @tap="register">注册新账户</view>
					</view>
				</view>
			</view>
			<!--  #endif -->
			
			<!--  #ifdef  MP-WEIXIN	 -->
			<view class="logView">
				<button  @click="onGetUserProfile" class="logbt">
					<view class="login-head">{{title}}</view>
					<view class="loginTitile"><text decode="true">
					请点击微信登录，并授权获取公开信息，
					登录后您将获得更多权益</text></view>
					<view class="cu-btn bg-blue shadow-blur round" :style="{background:color}"><text class="cuIcon-lightauto"></text>微信登录</view>
					
				</button>
				<view class="text-gray text-sm margin-top-xl"  :style="{color:color}"  @click="changMobileLogin()">手机登录</view>
				<!-- <button open-type="getUserInfo" @getuserinfo="onGotUserInfo" class="logbt">
					<image class="logoimg" src="../../static/images/user.png"></image>
					<view class="loginTitile"><text decode="true">
					请点击微信登录，并授权获取公开信息，
					登录士卓曼后您将获得更多权益</text></view>
					<view class="loginBtn">微信登录</view>
				</button> -->
			   <view class="text-gray text-sm margin-top-xl" :style="{color:color}" @tap="register">注册新账户</view>
			</view>
			<!--  #endif -->
		</block>
	</view>
</template>

<script>
	var _this;
	import {baseLogo,cdnUrl,title} from '../../config/config.js';
	export default {
		data() {
			return {
				loading: false,
				user:[],
				username: "",
				password: "",
				class_id:'',
				ismobile:false,
				group_id:1,
				code:'',
				baseLogo:baseLogo,
				title:title,
				color:''
			};
		},
		mounted() {
			_this = this;
		},
		onLoad(e) {
			this.color=uni.getStorageSync('color')
		},
		onShow() {
			this.user = this.$common.userInfo();
			console.log("this.user: ",this.user);
			if (typeof(this.user)== "undefined" || this.user=='' ||  this.user==null) {
				
			}else{
				this.$common.navigateTo('index');
			}
			// #ifdef MP-WEIXIN
			this.wxLogin();
			// #endif
			// #ifdef H5
			var ua = navigator.userAgent.toLowerCase();
			var isWeixin = ua.indexOf('micromessenger') != -1;
			if (isWeixin) {
				var jweixin = require('jweixin-module')  
				jweixin.ready(function(){  
				   console.log("123: ",123);
				   this.wxLogin();
				});
				let oUrl = window.location.href;
				window.location = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' + store.state.init.initData.wechat.appid +
					`&redirect_uri=${API_URL}user/wxOfficialAccountLogin&response_type=code&scope=snsapi_userinfo&state=` +
					oUrl;
				//window.location.href =cdnUrl+'/third/connect/wechat';  // 自己的appid
				
				// 也可以跳转到自己写的错误页面
				// uni.reLaunch({
				// 	url:'/pages/address/address'
				// })
			}
			// #endif
		},
		methods: {
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
			//切换微信登录
			wechatLogin(){
				_this.ismobile=false;
			},
			//切换手机登录
			changMobileLogin(){
				_this.ismobile=true;
				
			},
			register(){
				this.$common.navigateTo('register');
			},
			login() {
				_this.loading = true;
				if (_this.username == '' || _this.username.length<11) {
					uni.showToast({
						icon: 'none',
						title: '请输入正确的手机号'
					});
					_this.loading = false;
					return;
				}
				if (_this.password == '') {
					uni.showToast({
						icon: 'none',
						title: '请输入密码'
					});
					_this.loading = false;
					return;
				}
				_this.$api.login(
					 {
						account: _this.username,
						password: _this.password,
					 },
					data => {
						//console.log(data);
						if (data.code == 1) {
							_this.loading = false;
							//console.log(data);
							_this.$common.successToShow(data.msg,function(){
								_this.$common.navigateTo('../index/index');
							});
							try {
								_this.$db.set('upload', 1)
								_this.$db.set('login', 1)
								_this.$db.set('token', data.data.userinfo.token)
								_this.$db.set('user', data.data.userinfo)	
								_this.$db.set('auth', data.data.auth)							
							} catch (e) {}
							
							
						}else{
							_this.loading = false;
							uni.showToast({
								duration: 1500,
								icon: 'none',
								title: data.msg
							});
						}
						
					}
				)
			},
			//小程序登录（微信登录）
			onGotUserInfo() {
				this.$common.errorToShow('正在登录中...');
				var platform='wechat';
				uni.login({
					success: loginRes => {
						uni.hideLoading();
						console.log('第一次登录'+loginRes.code)
						if (loginRes.code) {
							uni.getUserInfo({
								withCredentials: true,
								success: res => {
									console.log('用户信息'+loginRes.code+res.encryptedData+res.iv+res.rawData+res.signature)
									_this.$api.third(
										{
											code: loginRes.code,
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
												} catch (e) {
													console.log("e: ",e);
												}
												//扫码进来的，如果是员工认证，group_id=2，如果是认证普通用户group=1
												if(data.data.userinfo.group_id==0){
													uni.navigateTo({
														url: '../user/index?group_id='+_this.group_id
													});
												}else{
													uni.switchTab({
														url: '../index/index'
													});
												}
												
											}
										}
									);
								},
								fail: (res) => {
									if (res.errMsg == "getUserInfo:cancel" || res.errMsg == "getUserInfo:fail auth deny") {
										uni.showModal({
											title: '用户授权失败',
											showCancel: false,
											content: '请点击重新授权，如果未弹出授权，请尝试长按删除小程序，重新进入!',
											success: function(res) {
												if (res.confirm) {
													console.log('用户点击确定')
												}
											}
										})
									}

								}
							})
						}
					}
				})
			},
			//改版后小程序登录规则
			//小程序登录
			onGetUserProfile() {
				// uni.showLoading({
				// 	title:"正在登录中..."
				// })
				var platform='wechat';
				var that=this;
				var fid=uni.getStorageSync('parentid')?uni.getStorageSync('parentid'):''; 
				uni.getUserProfile({
					 desc: '用于完善会员资料', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
					success: res => {
						console.log(res)
						_this.$api.third(
							{
								code: _this.code,
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
									} catch (e) {
										console.log("e: ",e);
									}
									uni.navigateTo({
										url: '../index/index'
									});
									
								}else{
									_this.wxLogin();
								}
							}
						);
					},
					fail: (res) => {
						console.log("res: ",res);
						_this.wxLogin();//重新获取登录code
						uni.hideLoading()
						if (res.errMsg == "getUserInfo:cancel" || res.errMsg == "getUserInfo:fail auth deny") {
							uni.showModal({
								title: '用户授权失败',
								showCancel: false,
								content: '请点击重新授权，如果未弹出授权，请尝试长按删除小程序，重新进入!',
								success: function(res) {
									if (res.confirm) {
										console.log('用户点击确定')
										uni.navigateBack()
									}
								}
							})
						}
							
					}
				})
				// uni.login({
				// 	success: loginRes => {
				// 		uni.hideLoading();
				// 		console.log('第一次登录'+loginRes.code)
				// 		if (loginRes.code && loginRes.code!='') {
				// 			console.log('2222222222222222222')
							
				// 		}
				// 	}
				// })
			}
		}
	}
</script>

<style>
	page{ background: #fff;}
	.content{ height: 100%;}
	.logView{
		display: flex;
		align-items: center;
		justify-content:center ;
		flex-direction:column;
		align-items: center;     /* 垂直居中 */
		 width: 100%;
		 position: fixed;
		 left: 50%;
		 top: 50%;
		 transform: translate(-50%,-50%);
	}
	.logbt {
		display: flex;
		align-items: center;
		justify-content:center ;
		flex-direction:column;
		align-items: center;     /* 垂直居中 */
		 width: 100%;
		background: none;
		border: none !important; 	
	}

	.logbt:after {
		border: none !important;
	}
   .logbt .logoimg{
	   width: 200rpx;
	   height: 200rpx;
	   display: block;
   }
   .logbt .wechatimg{
   	  width: 150rpx;
   	  height: 150rpx;
   	   display: block;
   }
   .loginTitile{ padding: 50rpx; font-size: 28rpx; color: #787878; line-height: 1.3; text-align: center;} 
   .loginBtn{ width: 300rpx; height: 70rpx; line-height: 70rpx; color: #fff; background:#2562a1; border-radius: 10rpx; border: none;}
	image {
		width: 100rpx;
		height: 100rpx;
	}
.mobileLogin{ background: none; color: #999; text-align: center; margin: 40rpx auto; border: none; font-size: 26rpx;}
	.landing[type=primary] {
		height: 84rpx;
		line-height: 84rpx;
		border-radius: 44rpx;
		font-size: 32rpx;
		/* background: linear-gradient(left, #86B5F4, #4790EF); */
		background-color: #ffbc32;
	}

	.login-btn {
		padding: 10rpx 20rpx;
		margin-top: 60rpx;
	}

	.login-function {
		overflow: auto;
		padding: 20rpx 20rpx 30rpx 20rpx;
	}

	.login-forget {
		float: left;
		font-size: 26rpx;
		color: #999;
	}

	.login-register {
		color: #666;
		float: right;
		font-size: 26rpx;

	}

	.login-input input {
		background: #F2F5F6;
		font-size: 28rpx;
		padding: 10rpx 25rpx;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 40rpx;
	}

	.login-margin-b {
		margin-bottom: 25rpx;
	}

	.login-input {
		padding: 10rpx 20rpx;
	}

	.login-head {
		font-size: 34rpx;
		text-align: center;
		padding: 25rpx 10rpx 55rpx 10rpx;
	}
.login-head image{ width: 200rpx;}
	.login-card {
		background: #fff;
		border-radius: 12rpx;
		padding: 10rpx 25rpx;
		position: relative;
		margin-top: 120rpx;
	}

	.login-bg {
		height: 100%;
		padding: 25rpx;
	}
	.login-head{ font-size: 90rpx;
	font-weight: bold;
	-webkit-mask-image:-webkit-gradient(linear, 0 0, 0 bottom, from(white), to(rgba(210, 1, 160, 0.53)));}
</style>
