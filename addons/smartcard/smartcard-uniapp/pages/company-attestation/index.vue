<!-- 企业认证 - 中转- 静态 -->
<template>
	<view class="page-wrap">
		<view class="attestation-box">
			<view class="company-box">
				<image class="company-icon" src="../../static/images/corp-icon.png" />
				<view class="company-content">
					<view class="company-name">{{ companyname || company || '企业名字' }}</view>
					<view class="attestation">
						<image src="../../static/images/enterprise-cert.png" />
						<text>企业认证</text>
					</view>
				</view>
			</view>
		</view>
		<view class="attestation-title"><text>我们的优势</text></view>
		<view class="attestation-box pad attestation-mar">
			<view class="equity-box">
				<view class="equity-num">01</view>
				<view class="equity-content">
					<view>依托微信 使用便捷 高效</view>
					<text>电子名片通依托微信小程序开发，可以直接通过微信一键发送名片，分享、交换名片，便捷，高效。</text>
				</view>
			</view>
			<view class="equity-img"><image src="../../static/images/cert01.png" /></view>
		</view>
		<view class="attestation-box pad">
			<view class="equity-box">
				<view class="equity-num">02</view>
				<view class="equity-content">
					<view>名片上的官方网站</view>
					<text>可以展示企业的简介、宣传片，业务产品信息，联系信息、导航定位等，让您的客户直观全面的了解你的企业。</text>
				</view>
			</view>
			<view class="equity-img"><image src="../../static/images/cert02.png" /></view>
		</view>
		<view class="attestation-box pad">
			<view class="equity-box">
				<view class="equity-num">03</view>
				<view class="equity-content">
					<view>云上名片 数据不丢失</view>
					<text>所有客户名片资料都可以上传到云服务，随时获取、管理、分析数据信息。没有丢失隐患。</text>
				</view>
			</view>
			<view class="equity-img"><image src="../../static/images/cert02.png" /></view>
		</view>
		<view class="attestation-btn">
			<view @click="linkTo">立即认证</view>
		</view>
	</view>
</template>

<script>
	import { navigateTo } from '@/config/common.js'
	export default {
		name: 'coporationCert',
		props: {
			company: String
		},
		data() {
			return {
				companyname: '',
				userData: uni.getStorageSync('userData') || {}
			}
		},
		onLoad(options) {
			this.companyname = options.companyname || '企业认证'
		},
		methods: {
			linkTo() {
				navigateTo('/pages/company-attestation/attestation')
			}
		}
	}
</script>

<style>
	.page-wrap {
		padding-bottom: 128rpx;
	}
	.attestation-box {
		background-color: #EFF4FF;
		margin: 0 32rpx;
		border-radius: 16rpx;
	}
	.attestation-box.pad {
		padding: 32rpx;
	}
	.company-box {
		padding: 32rpx 20rpx;
		display: flex;
		align-items: center;
	}
	.company-icon {
		flex-shrink: 0;
		width: 80rpx;
		height: 80rpx;
		margin-right: 20px;
	}
	.company-content {
		flex: 1;
		overflow: hidden;
	}
	.company-name {
		font-size: 32rpx;
		font-weight: 700;
		color: #333;
		line-height: 40rpx;
	}
	.attestation {
		display: inline-block;
		padding: 0 12rpx;
		height: 32rpx;
		line-height: 32rpx;
		background-color: #0256FF;
		border-radius: 20rpx;
		font-size: 20rpx;
		color: #fff;
		margin-top: 10rpx;
	}
	.attestation image {
		width: 24rpx;
		height: 24rpx;
		margin-right: 4rpx;
		vertical-align: middle;
	}
	.attestation-title {
		position: relative;
		padding: 40rpx 80rpx;
		text-align: center;
		font-size: 36rpx;
		color: #333;
		font-weight: 700;
	}
	.attestation-title::before,
	.attestation-title::after {
		content: '';
		position: absolute;
		width: 28rpx;
		height: 9rpx;
		background: linear-gradient( 270deg, #0256FF 0%, rgba(2,86,255,0) 100%);
		border-radius: 36rpx 36rpx 36rpx 36rpx;
	}
	.attestation-title::before {
		left: 180rpx;
		top: 64rpx;
		-webkit-transform: rotate(45deg);
		transform: rotate(45deg);
	}
	.attestation-title::after {
		right: 180rpx;
		top: 64rpx;
		-webkit-transform: rotate(135deg);
		transform: rotate(135deg);
	}
	.attestation-title text {
		position: relative;
		padding: 30rpx;
	}
	.attestation-title text::before,
	.attestation-title text::after {
		content: '';
		position: absolute;
		width: 36rpx;
		height: 12rpx;
		background: linear-gradient( 270deg, #0256FF 0%, rgba(2,86,255,0) 100%);
		border-radius: 45rpx 45rpx 45rpx 45rpx;
		transform: rotateX(45deg);
	}
	.attestation-title text::before {
		left: -50rpx;
		top: 50rpx;
		-webkit-transform: rotate(45deg);
		transform: rotate(45deg);
	}
	.attestation-title text::after {
		right: -50rpx;
		top: 50rpx;
		-webkit-transform: rotate(135deg);
		transform: rotate(135deg);
	}
	.attestation-mar {
		margin-bottom: 20rpx;
	}
	.equity-img {
		padding: 20rpx;
		border-radius: 16rpx;
		background-color: #fff;
		text-align: center;
	}
	.equity-img image{
		width: 500rpx;
		height: 455rpx;
	}
	.equity-box {
		position: relative;
		padding-top: 60rpx;
		margin-bottom: 40rpx;
	}
	.equity-num {
		position: absolute;
		right: 0;
		top: 0;
		font-size: 84rpx;
		color: rgba(2, 86, 255, .4);
		font-weight: 700;
		margin-right: 20rpx;
		font-family: Source Han Sans, Source Han Sans;
		font-weight: 700;
	}
	.equity-content {
		overflow: hidden;
		font-size: 32rpx;
		color: #333;
		line-height: 36rpx;
		font-weight: 500;
	}
	.equity-content text {
		color: #666;
		font-size: 24rpx;
		font-weight: normal;
	}
	.attestation-btn {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		height: 108rpx;
		background-color: #fff;
	}
	.attestation-btn view {
		margin: 14rpx 32rpx 0;
		height: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		text-align: center;
		color: #fff;
		background-color: #0256FF;
		border-radius: 60rpx;
	}
</style>