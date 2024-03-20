<template>
	<view class="page-wrap">
		<view class="page-title">
			<view>实名认证</view>
			<view class="descript">完成实名认证，让你的电子名片更可信，拓展更多人脉和商机</view>
			<view class="descript" style="color: red;">注：请仔细核对认证信息，姓名和手机号码请到个人资料修改</view>
		</view>
		<view class="username-box">
			<text>真实姓名</text>
			<input disabled v-model="form.name" placeholder="请输入真实姓名" />
		</view>
		<view class="identity-box">
			<view>身份证正面</view>
			<image :src="form.id_card_face ? cdnUrl + form.id_card_face : require('../../static/images/identity02.png')"
				@click="upIdCardFace('id_card_face')" />
		</view>
		<view class="identity-box">
			<view>身份证反面</view>
			<image :src="form.id_card_reverse ? cdnUrl + form.id_card_reverse : require('../../static/images/identity01.png')"
				@click="upIdCardFace('id_card_reverse')" />
		</view>
		<view class="username-box">
			<text>手机号</text>
			<input disabled v-model="form.phone" placeholder="请输入手机号" />
		</view>
		<view class="username-box">
			<text>验证码</text>
			<input v-model="form.code" placeholder="请输入验证码" />
			<view class="countdown" @click="countdownFn">{{ time === 60? '获取验证码' : time=== 0? '重新获取' : `${time}s后重新获取` }}</view>
		</view>
		<view class="attestation-btn" @click="submitFn">提交</view>
	</view>
</template>

<script>
	import { errorToShow, successToShow, checkMobile } from '@/config/common.js'
	import {cdnUrl} from '@/config/config.js'
	export default {
		data() {
			return {
				cdnUrl,
				time: 60,
				timer: null,
				form: {
					name: '',
					phone: '',
					code: '',
					id_card_face: '', // 正面
					id_card_reverse: '' // 反面
				}
			}
		},
		onLoad() {
			this.userData = uni.getStorageSync('userData')
			this.form.name = this.userData.name
			this.form.phone = this.userData.mobile
		},
		methods: {
			// 验证码倒计时
			countdownFn() {
				if (this.timer) return
				if (!this.form.phone) return errorToShow('请输入手机号码')
				else if (!checkMobile(this.form.phone)) return errorToShow('请输入正确的手机号码')
				this.$api.sendSmsVerify({
					mobile: this.form.phone,
					event: 'certified'
				},
				res =>{
					console.log(res, 'yanzhengma')
				})
				if (this.time === 0) this.time = this.$options.data().time - 1
				this.timer = setInterval(() => {
					this.time--
					if (this.time === 0) {
						clearInterval(this.timer)
						this.timer = null
					}
				},1000)
			},
			upIdCardFace(key) {
				this.$api.uploadImage('common/upload', {}, dataimg => {
					this.form[key] = dataimg.data.url // 相对路径 fullurl绝对路径
				}, 1)
			},
			submitFn() {
				if (!this.form.name) return errorToShow('请输入真是姓名')
				else if (!this.form.id_card_face) return errorToShow('请上传身份证正面照')
				else if (!this.form.id_card_reverse) return errorToShow('请上传身份证反面照')
				else if (!this.form.phone) return errorToShow('请输入手机号码')
				else if (!checkMobile(this.form.phone)) return errorToShow('请输入正确的手机号码')
				else if (!this.form.code) return errorToShow('请输入验证码')
				this.$api.realnameCertified({ ...this.form }, res => {
					console.log(res, '实名认证')
					if (res.code == 1) {
						successToShow(res.msg)
					} else errorToShow(res.msg)
				})
			}
		}
	}
</script>

<style>
	.page-wrap {
		margin: 0 32rpx;
		padding: 20rpx 0;
	}
	.page-title {
		padding: 12rpx 0;
		font-size: 36rpx;
		line-height: 48rpx;
		color: #333;
		font-weight: 700;
	}
	.descript {
		font-size: 24rpx;
		color: #999;
	}
	.username-box {
		display: flex;
		align-items: center;
		height: 100rpx;
		line-height: 100rpx;
		font-size: 28rpx;
		color: #666;
		border-bottom: 2rpx solid #E6E6E6;
		overflow: hidden;
		white-space: nowrap;
	}
	.username-box input {
		flex: 1;
		margin-left: 74rpx;
	}
	.identity-box {
		padding: 24rpx 0 32rpx;
		border-bottom: 2rpx solid #E6E6E6;
		font-size: 28rpx;
		color: #666;
	}
	.identity-box image {
		margin-top: 24rpx;
		width: 320rpx;
		height: 200rpx;
	}
	.countdown {
		flex-shrink: 0;
		border-radius: 40rpx;
		padding: 0 16rpx;
		height: 60rpx;
		line-height: 60rpx;
		font-size: 24rpx;
		color: #9E9E9E;
		border: 2rpx solid #9E9E9E;
	}
	.attestation-btn {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		font-size: 28rpx;
		color: #fff;
		background-color: #0256FF;
		border-radius: 60rpx;
		margin-top: 40rpx;
		margin-bottom: 24rpx;
	}
</style>