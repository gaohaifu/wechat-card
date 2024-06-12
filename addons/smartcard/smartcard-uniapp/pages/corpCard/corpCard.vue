<!-- 企业名片 -->
<template>
	<view>
		<view class="no-data" v-if="userData.is_authentication != 2">
			<image class="img" src="../../static/images/empty.png" mode=""></image>
			您还未完成企业认证，<text class="btn" @click="linkToCert">前往认证</text>
		</view>
		<view v-else>
			<view class="flex_layout company-box">
				<image class="avatar" src="../../static/images/corp-icon.png" mode=""></image>
				<view class="flex-1">
					<view class="name">{{companyInfo.companyname}}</view>
					<view class="merito">{{companyInfo.position}}</view>
				</view>
			</view>
			<view class="flex menu-box">
				<view class="flex-1 flex flex-vc flex-hc menu-item" :class="{'active' : item.value === activeId}"
					v-for="(item, inx) in menu" :key="inx">
					{{item.label}}
				</view>
			</view>
			<!-- 统计 + 按钮 -->
			<view class="card-box">
				<view class="flex statistics">
					<view class="flex-1 flex flex-v flex-hc flex-vc" v-for="(item, inx) in cards" :key="inx"
						@click="linkTo(item)">
						<view class="num">{{item.value}}</view>
						<view class="flex flex-vc">
							<text>{{item.label}}</text>
							<text class="iconfont icon-Rightyou"></text>
						</view>
					</view>
				</view>
				<view class="flex flex-hsb" style="padding: 32rpx 0;">
					<view class="flex flex-vc flex-hc plain-btn">
						<text class="iconfont icon-bianji"></text>
						<text>在线录入</text>
					</view>
					<view class="flex flex-vc flex-hc primary-btn">
						<text class="iconfont icon-jiaweixin"></text>
						<text>微信邀请</text>
					</view>
				</view>
				<view class="flex flex-vc flex-hc more">
					<text>更多邀请方式</text>
					<text class="iconfont icon-Rightyou"></text>
				</view>
			</view>
			<!-- 成员动态 -->
			<view class="card-box">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">我的名片数据</view>
					<view class="flex flex-vc more">
						<text>全部动态</text>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="member-list">
					<view class="flex_between" v-for="(it, inx) in memberList" :key="inx">
						<view class="flex-1">
							<text class="name">{{it.name}}</text>
							<text>加入了企业</text>
						</view>
						<text class="time">{{it.createtime}}</text>
					</view>
				</view>
			</view>
		</view>
		<!--  #ifdef  MP-WEIXIN	 -->
		<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom" @cancelBottom="cancelBottom"></bottomSheet>
		<!--  #endif -->
	</view>
</template>

<script>
	import bottomSheet from '@/components/bbh-sheet/bottomSheet.vue';
	import {isLogin} from '@/config/common.js'
	export default {
		components: {
			bottomSheet
		},
		data() {
			return {
				activeId: '01',
				menu: [{
						label: '成员管理',
						value: '01'
					},
					{
						label: '模板管理',
						value: '02'
					},
				],
				cards: [{
						id: 1,
						label: '全部成员',
						value: 0
					},
					{
						id: 2,
						label: '申请加入',
						value: 0
					},
					{
						id: 3,
						label: '等待激活',
						value: 0
					}
				],
				keyword: '',
				memberList: [],
				companyInfo: {},
				userData: {}
			}
		},
		onLoad() {
			this.getData()
			this.userData = uni.getStorageSync('userData')
		},
		methods: {
			getData() {
				this.$api.myCompany({}, res => {
					if(res.code === 1) {
						const memberInfo = res.memberInfo || {}
						this.cards.find(i => i.id === 1).value = memberInfo.allNum
						this.cards.find(i => i.id === 2).value = memberInfo.applyNum
						this.cards.find(i => i.id === 3).value = memberInfo.activationNum
						this.memberList = res.memberList || []
						this.companyInfo = res.companyInfo || {}
					}
				})
			},
			linkToCert(type) {
				if(!this.checkUser()) return;
				uni.navigateTo({
					url: '/pages/company-attestation/index'
				})
			},
			linkTo(row) {
				uni.navigateTo({
					url: '/pages/member/member?type=' + row.id
				})
			},
			// 以下都是检测登录逻辑===============参考首页
			checkUser() {
				const flag = isLogin()
				if(!flag) {
					this.isShowBottom=true
					this.user_id='';
				}
				return flag
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
						// this.getIndex();
					}else{
						//微信小程序端
						// #ifdef MP-WEIXIN
						console.log("小程序: ",1);
						this.isShowBottom=true
						this.user_id='';
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
										// this.getIndex()
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
			cancelBottom() {
				this.isShowBottom = false;
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	page {
		background-color: #F6F7F9;
	}
	.no-data {
		padding: 100rpx 20rpx;
		font-size: 28rpx;
		color: #999;
		text-align: center;
	}
	.no-data .img {
		display: block;
		margin: 0 auto;
		width: 400rpx;
		height: 400rpx;
	}
	.no-data .btn {
		color: #0256FF;
	}
	.company-box {
		padding: 16rpx 32rpx;
		background-color: #fff;
	}

	.company-box .avatar {
		margin-right: 20rpx;
		width: 80rpx;
		height: 80rpx;
		border-radius: 16rpx;
	}

	.company-box .name {
		font-size: 32rpx;
		font-weight: 700;
		color: #333;
		line-height: 40rpx;
	}

	.company-box .merito {
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
		line-height: 40rpx;
	}


	/* 导航 */
	.menu-box {
		height: 80rpx;
		background-color: #fff;
	}

	.menu-box .menu-item {
		position: relative;
		font-size: 28rpx;
		font-weight: 700;
		color: #333;
	}

	.menu-box .menu-item.active {
		color: #0256FF;
	}

	.menu-box .menu-item.active::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 112rpx;
		height: 4rpx;
		background-color: #0256FF;
	}

	/* 统计 + 按钮 */
	.card-box {
		margin: 20rpx 32rpx;
		padding: 28rpx 24rpx;
		background-color: #fff;
		border-radius: 16rpx;
	}

	.card-box .statistics {
		height: 144rpx;
		border-bottom: solid 2rpx #E6E6E6;
	}

	.card-box .statistics .num {
		margin-bottom: 12rpx;
		font-size: 36rpx;
		font-weight: 700;
		color: #333;
	}

	.card-box .primary-btn,
	.card-box .plain-btn {
		padding: 0 54rpx;
		height: 72rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 72rpx;
		line-height: 72rpx;
		font-size: 28rpx;
		font-weight: 400;
	}

	.card-box .primary-btn .iconfont,
	.card-box .plain-btn .iconfont {
		margin-right: 8rpx;
		width: 40rpx;
		height: 40rpx;
		font-size: 40rpx;
		line-height: 40rpx;
		text-align: center;
	}

	.card-box .primary-btn {
		color: #fff;
		background-color: #07C160;
		border: solid 1px #07C160;
	}

	.card-box .plain-btn {
		color: #666;
		background-color: #fff;
		border: solid 1px #E6E6E6;
	}

	.card-box .more {
		height: 72rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #666;
	}

	.card-box .more .iconfont {
		width: 32rpx;
		height: 32rpx;
		line-height: 32rpx;
		font-size: 24rpx;
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

	.member-list {
		margin: 20rpx 0;
		font-size: 28rpx;
		font-weight: 400;
		color: #333;
	}

	.member-list .name {
		color: #0256FF;
		margin-right: 8rpx;
	}

	.member-list .time {
		font-size: 24rpx;
		color: #999;
	}
</style>