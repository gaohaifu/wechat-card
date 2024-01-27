<template>
	<view class="login">
		<view class="content">
			<!-- 头部logo -->
			<view class="logoLogin flex_layout">
				<view class="logoImg">
					<image :src="user.avatar?user.avatar:'../../static/images/user.png'" ></image>
					<text>{{user.nickname?user.nickname:'未登录'}}</text>
				</view>
			</view>
			<!-- 主体表单 -->
			<view class="main">
				<view class="fillIn_item">
					<wInput v-model="jobNumber" @input="jobinput" type="text" maxlength="11" imgSrc='../../static/images/login1.png' placeholder="请输员工工号"></wInput>
				</view>
				<view class="fillIn_item">
					<wInput v-model="account" @input="inputss" type="number" maxlength="11" imgSrc='../../static/images/login2.png' placeholder="请输入手机号"></wInput>
				</view>
				<view class="fillIn_item">
					<wInput
						v-model="captcha"
						type="number"
						maxlength="6"
						placeholder="请输入验证码"
						imgSrc='../../static/images/login3.png'
						isShowCode
						ref="runCode"
						@setCode="getCaptcha()"
					></wInput>
				</view>
			</view>
			<wButton text="绑定" :rotate="isRotate" @click.native="startBind()"></wButton>
			<view class="wayLogin">这是员工福利的必选，和员工管理平台绑定</view>
			<view class="wayLogin">绑定您的信息，用于关联您的积分优惠券</view>
		</view>
	</view>
</template>

<script>
var _this;
import wInput from '../../components/watch-login/watch-input.vue'; //input
import wButton from '../../components/watch-login/watch-button.vue'; //button
export default {
	data() {
		return { 
			loadModal: false,
			showAgree: true,
			modalName: '',
			base: this.$common.baseInfo(),
			account: '',
			jobNumber: '',
			captcha: '',
			isRotate: false, //按钮旋转状态
			user: this.$common.userInfo(),
			path: {
				//函数数值判断填充进对应的参数
				encryptedData: '1',
				iv: '1',
				signature: '1',
				code: '1',
				// #ifdef MP-WEIXIN
				type: 'wechat',
				// #endif
				// #ifdef MP-ALIPAY
				type: 'alipay',
				avatar: '',
				nickName: '',
				// #endif
			},
			group_id:1
		};
	},
	onLoad(e) {
		// if(e.group_id){
		//  this.group_id=e.group_id;	
		// }
		// this.user = this.$common.userInfo();
		// if (!this.user) {
		// 	this.$common.toLogin();
		// }
		
	},
	onShow() {
		console.log(this.$common.userInfo());
	},
	components: {
		wInput,
		wButton
	},
	mounted() {
		_this = this;
		//this.isLogin();
	},
	created() {
	},
	methods: {
		inputss: function(e){
			this.account=e
		},
		jobinput: function(e){
			this.jobNumber=e
		},
		getCaptcha() {
			//获取验证码
			if (!_this.$common.testString(this.account,'mobile')) {
				uni.showToast({
					icon: 'none',
					position: 'bottom',
					title: '手机号格式不正确'
				});
				return false;
			}else{ 
				
			_this.$api.sendSmsVerify(
				{
					event: 'changemobile',
					mobile: _this.account
				},
				res => {
					if (res.code) {
						_this.$common.successToShow(res.msg);
						this.$refs.runCode.$emit('runCode'); //触发倒计时（一般用于请求成功验证码后调用）
					} else {
						_this.$common.errorToShow(res.msg);
					}
				}
			);
		 }
		},
		checkmobileBind(){
			_this.$api.changeMobile(
				{
					mobile: _this.account,
					captcha: _this.captcha,
					event:'changemobile',
					group_id:_this.group_id,
					number:_this.jobNumber
				},
				res => {
					this.isRotate=false;
					if (res.code) {
						_this.$common.refreshUser(res => {
							if(this.user.avatar.indexOf("data:") != -1){ 
								var cavatar='/images/user.png';
								this.user.avatar=cavatar;
							}
						})
						//uni.setStorageSync(user['mobile'],_this.account);
						_this.$common.successToShow(res.msg,function(){
							_this.$common.navigateTo('/pages/index/index');
						});
					} else {
						    _this.$common.errorToShow(res.msg,function(){
							//_this.$common.navigateTo('/pages/index/index');
						});
					}
				}
			);
		},
		//验证码登录
		startBind() {
			//登录
			if (this.isRotate) {
				//判断是否加载中，避免重复点击请求
				return false;
			}
			if (!_this.$common.checkMobile(this.account)) {
				_this.$common.errorToShow('手机号不正确');
				return false;
			}
			if (this.captcha=='') {
				_this.$common.errorToShow('请输入验证码');
				return false;
			}
			
			_this.isRotate = true;
			setTimeout(() => {
				//验证手机是否存在
				_this.checkmobileBind(); 
			}, 1000);
		}
	}
};
</script>

<style>
@import url('../../components/watch-login/css/icon.css');
@import url('./css/main.css');
@import url('../../common/common.css');
	.logoLogin{justify-content: center; padding: 100upx 0; margin-top: 100upx;}
	.logoImg{width:100%; height: 200upx; }
	.logox{color: #fff; font-size: 36upx; padding: 50upx;}
	.logoImg image{width: 200upx; height: 200upx; margin: 0 auto; display: block;border-radius: 200upx; overflow: hidden;}
	.logoImg text{display: block; margin-top: 40upx; text-align: center; color: #333; font-size: 36upx;}
	.oBorder{box-shadow: none; padding: 0;} 
	.colorFff{color: #fff;}
	.fillIn_item{padding: 20upx 0;}
	.fillIn_name{color: #333; font-size: 28upx; font-weight: bold; padding: 20upx 0;}
	.buttonBorder{background: rgba(38,248,248,0.8); color: #fff; font-size: 30upx; width: 420upx; height: 72upx;}
	.wayLogin{padding: 30upx 0; margin-top: 50upx; text-align: center; color: #999; font-size: 24upx;}
	.wayLogin text{color: #26f8f8; margin-left: 20upx;}
	.cuIcon-weixin{color: rgba(255,255,255,0.8);}
</style>
