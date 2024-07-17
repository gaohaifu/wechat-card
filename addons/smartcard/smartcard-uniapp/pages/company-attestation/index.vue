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
		<view class="attestation-title">企业认证 - <text>专属权益</text></view>
		<view class="attestation-box pad attestation-mar">
			<view class="equity-box">
				<view class="equity-num">01</view>
				<view class="equity-content">
					<view>分享名片卡片</view>
					<text>发名片时，分享卡片上显示企业认证标识</text>
				</view>
			</view>
			<view class="equity-img"><image src="../../static/images/corp-cert-img01.png" /></view>
		</view>
		<view class="attestation-box pad">
			<view class="equity-box">
				<view class="equity-num">02</view>
				<view class="equity-content">
					<view>名片详情页</view>
					<text>显示企业专属认证标识，增加名片可信度</text>
				</view>
			</view>
			<view class="equity-img"><image src="../../static/images/corp-cert-img01.png" /></view>
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
		padding: 40rpx 0;
		text-align: center;
		font-size: 36rpx;
		color: #333;
		font-weight: 700;
	}
	.attestation-title text {
		margin-left: 8rpx;
		color: #0256FF;
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
		width: 478rpx;
		height: 412rpx;
	}
	.equity-box {
		display: flex;
		align-items: center;
		margin-bottom: 40rpx;
	}
	.equity-num {
		flex-shrink: 0;
		font-size: 64rpx;
		color: #0256FF;
		font-weight: 700;
		margin-right: 20rpx;
	}
	.equity-content {
		flex: 1;
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