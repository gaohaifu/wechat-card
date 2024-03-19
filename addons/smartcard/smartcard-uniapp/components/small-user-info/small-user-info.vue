<template>
	<view class="header_background" :style="{'background-image':'url('+cdnUrl+theme.backgroundimage+')'}">
		<cu-custom :bgColor="bgColor" :isBack="isBack" :backGround="backGround">
			<block slot="backText">返回</block>
			<block slot="content">{{pageTitle}}</block>
		</cu-custom>
		<view class="header_padding">
			<view class="header_message" :style="{'background-image':'url('+cdnUrl + theme.cardimage+')'}">
				<view class="flex_layout userImg">
					<image :src="userData.avatar?userData.avatar:'../../static/images/user.png'" mode=""></image>
					<view class="name_position">
						<view :style="{color:theme.fontcolor}">{{userData.name?userData.name:''}}</view>
						<text :style="{color:theme.fontcolor}">{{userData.position}}</text>
						<text :style="{color:theme.fontcolor}">{{companyInfo.name}}</text>
					</view>
				</view>
				<view class="cert-status" :class="{'waitOp': !certificateStatus, 'op': certificateStatus}"
					:style="{'background-image':'url('+(certificateStatus? smartcardBG.cert : smartcardBG.unCert)+')', color:theme.fontcolor}">
					{{!certificateStatus? '未认证' : '未认证'}}
				</view>
				<view class="extra">
					<view class="flex_layout"><i :style="{color:theme.fontcolor}" class="iconfont icon-dianhua"></i><text
							:style="{color:theme.fontcolor}">{{userData.mobile?userData.mobile:'暂未填写'}}</text></view>
					<view class="flex_layout"><i :style="{color:theme.fontcolor}" class="iconfont icon-youjian1"></i><text
							:style="{color:theme.fontcolor}">{{userData.email?userData.email:'暂未填写'}}</text></view>
					<view class="flex_layout" v-if="companyInfo.address"><i :style="{color:theme.fontcolor}"
							class="iconfont icon-weizhi"></i><text
							:style="{color:theme.fontcolor}">{{companyInfo.address?companyInfo.address:'暂未填写'}}</text>
					</view>
				</view>
				<!--切换按钮-->
				<!-- <view class="change_tab flex_layout" v-if="user_id==userData.user_id" @click="changeTab">
					<image src="../../static/images/change.png" mode=""></image><text>切换</text>
				</view> -->
				<view class="isHC-box" v-if="smartcardObj.save_status[userData.save_status]">
					<view class="bg">
						{{smartcardObj.save_status[userData.save_status]}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		smartcardBG,
		smartcardObj
	} from '../../config/common.js'
	import {
		cdnUrl
	} from '@/config/config.js'
	export default {
		name: "small-user-info",
		props: {
			// 解决页面返回后导致保存模板的数据不生效问题
			isShow: {
				type: Boolean,
				default: true
			},
			bgColor: {
				type: String,
				default: 'bg-gradual-custom'
			},
			backGround: {
				type: String,
				default: ''
			},
			isBack: {
				type: Boolean,
				default: false
			},
			pageTitle: {
				type: String,
				default: '标题'
			},
			themeData: {
				type: Object,
				default() {
					return {
						backgroundimage: smartcardBG.backgroundimage, // 页面背景
						cardimage: smartcardBG.cardimage, // 主题背景
						fontcolor: '#333', // 字体颜色
						color: '#333', // 主题颜色
					}
				}
			}
		},
		data() {
			return {
				cdnUrl: cdnUrl,
				smartcardBG: smartcardBG,
				smartcardObj: smartcardObj,
				theme: this.themeData || {},
				userData: {
					nickname: '微信昵称',
					name: '微信昵称',
					avatar: '',
					position: ''
				},
				allData: {},
				certificateStatus: true
			};
		},
		watch: {
			themeData: {
				deep: true,
				handler(vl = {}) {
					if(vl.cardimage) this.theme = vl
				}
			},
			isShow(vl) {
				if(vl) this.getSessionData()
			}
		},
		created() {
			this.getSessionData()
			console.info('----------small user info 回退是否会执行当前生命周期？？？', 
				this.userData.save_status, this.smartcardObj.save_status[this.userData.save_status])
		},
		methods: {
			getSessionData() {
				this.userData = uni.getStorageSync('userData') || {}
				const themeData = uni.getStorageSync('themeData') || {}
				if(themeData.cardimage) this.theme = themeData
				if(this.userData.statusdata!='1'){
					this.certificateStatus=false;
				}
			}
		}
	}
</script>

<style scoped>
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
</style>