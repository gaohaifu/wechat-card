<!-- 成员认证 -->
<template>
	<view class="page-wrap">
		<view class="page-title">
			<view>实名认证</view>
			<view class="descript">完成实名认证，让你的电子名片更可信，拓展更多人脉和商机</view>
		</view>
		<view class="username-box">
			<text>真实姓名</text>
			<input v-model="form.name" placeholder="请输入真实姓名" />
		</view>
		<view class="identity-box">
			<view>身份证正面</view>
			<image src="../../static/images/identity02.png" />
		</view>
		<view class="identity-box">
			<view>身份证反面</view>
			<image src="../../static/images/identity01.png" />
		</view>
		<view class="username-box">
			<text>手机号</text>
			<input v-model="form.phone" placeholder="请输入手机号" />
		</view>
		<view class="username-box">
			<text>验证码</text>
			<input v-model="form.code" placeholder="请输入验证码" />
			<view class="countdown" @click="countdownFn">{{ time === 60? '获取验证码' : time=== 0? '重新获取' : `${time}s后重新获取` }}</view>
		</view>
		<view class="attestation-btn">下一步</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				time: 60,
				timer: null,
				form: {
					name: '',
					phone: '',
					code: '',
				}
			}
		},
		methods: {
			countdownFn() {
				if (this.timer) return
				if (this.time === 0) this.time = 59
				this.timer = setInterval(() => {
					this.time--
					if (this.time === 0) {
						clearInterval(this.timer)
						this.timer = null
					}
				},1000)
			}
		}
	}
</script>

<style>
	.page-wrap {
		margin: 0 32rpx;
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