<template>
	<view class="header_message" :style="{'background-image':'url('+smartcardBG.cardimage+')'}">
		<view class="flex_layout userImg">
			<image :src="userData.avatar?userData.avatar:'../../static/images/user.png'" mode=""></image>
			<view class="name_position">
				<view :style="{color:fontcolor}">{{userData.name?userData.name:''}}</view>
				<text :style="{color:fontcolor}">{{userData.position}}</text>
				<text :style="{color:fontcolor}">{{companyInfo.name}}</text>
			</view>
		</view>
		<view class="cert-status" :class="{'waitOp': !certificateStatus, 'op': certificateStatus}"
			:style="{'background-image':'url('+(certificateStatus? smartcardBG.cert : smartcardBG.unCert)+')', color:fontcolor}">
			{{!certificateStatus? '未认证' : '未认证'}}
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
		<view class="isHC-box">
			<view class="bg">
				已互存
			</view>
		</view>
	</view>
</template>

<script>
	import {
		smartcardBG
	} from '../../config/common.js'
	export default {
		name: "small-user-info",
		data() {
			return {
				smartcardBG: smartcardBG,
				userData: {
					nickname: '',
					name: '',
					avatar: '',
					position: ''
				},
				allData: {},
				certificateStatus: false
			};
		},
		methods: {
			//首页全部信息接口（包含个人信息）
			indexData() {
				var staff_id_c = this.staff_id != 0 && this.staff_id != null ? this.staff_id : uni.getStorageSync(
					'staff_id')
				const parm = {
					staff_id: staff_id_c,
					user_id: this.user_id
				};
				var that = this;
				this.$api.indexData(
					parm,
					data => {
						if (data.code == 1) {
							this.allData = data.data
							this.$emit('@getAllUserData', this.allData)
							console.log(this.allData);
							// this.usertype = data.data.usertype; //是否是领导角色（0：不是  1：是）
							this.userData = data.data.staffInfo
							if (this.userData.statusdata != '1') {
								this.certificateStatus = false;
							}
						} else {
							if (this.user_id != 0 || this.user_id != '') {
								if (staff_id_c != '') {
									uni.showToast({
										title: '无此用户名片信息,即将跳转到个人名片主页...',
										icon: 'none',
										duration: 1500
									})
									setTimeout(() => {
										uni.navigateTo({
											url: 'index'
										})
									}, 2000)
									return false;
								}
							}
							this.$common.errorToShow(data.msg, function() {
								if (staff_id_c == undefined || staff_id_c == null || staff_id_c == '' ||
									staff_id_c == 0) {
									if (that.user_id != 0) {
										uni.navigateTo({
											url: '../user/addInfo'
										})
									}
								}
							})
						}

					})
			},
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
		color: #fff;
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