<template>
	<view style="padding-bottom: 138rpx;">
		<view class="flex_layout company-box">
			<image class="avatar" src="../../static/images/corp-icon.png" mode=""></image>
			<view class="flex-1">
				<view class="name">{{companyname}}</view>
				<view class="flex flex-vc flex-hc enterprise-cert">
					<image src="../../static/images/enterprise-cert.png" mode=""></image>
					<text>企业认证</text>
				</view>
			</view>
		</view>
		
		<view class="flex flex-hc title-box">
			<text>企业认证 - </text>
			<text>专属权益</text>
		</view>
		
		<view class="card-box">
			<view class="flex desc-box">
				<view class="sn">01</view>
				<view class="flex-1 flex flex-v">
					<text>分享名片卡片</text>
					<text>发名片时，分享卡片上显示企业认证标识</text>
				</view>
			</view>
			<view class="img-box">
				<image src="../../static/images/corp-cert-img01.png" mode="scaleToFill"></image>
			</view>
		</view>
		
		<view class="card-box">
			<view class="flex desc-box">
				<view class="sn">02</view>
				<view class="flex-1 flex flex-v">
					<text>名片详情页</text>
					<text>显示企业专属认证标识，增加名片可信度</text>
				</view>
			</view>
			<view class="img-box">
				<image src="../../static/images/corp-cert-img01.png" mode="scaleToFill"></image>
			</view>
		</view>
		
		<view class="flex flex-vc footer">
			<view class="flex-1 primary-btn" @click="linkTo">立即认证</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				companyname: ''
			}
		},
		onLoad(options) {
			this.companyname = options.companyname || '企业认证'
		},
		methods: {
			linkTo() {
				uni.navigateTo({
					url: '/pages/user/login'
				})
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	page {
		background-color: #fff;
	}

	.company-box {
		margin: 0 32rpx;
		padding: 32rpx 20rpx;
		background-color: #EFF4FF;
		border-radius: 16rpx;
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
		margin-bottom: 10rpx;
	}

	.company-box .enterprise-cert {
		width: 132rpx;
		height: 34rpx;
		line-height: 34rpx;
		border-radius: 20px 20px 20px 20px;
		background: #0256FF; 
		margin-right: 8rpx;
		font-size: 20rpx;
		color: #fff;
	}
	.company-box .enterprise-cert image {
		width: 24rpx;
		height: 24rpx;
		margin-right: 4rpx;
	}
	.title-box {
		padding: 40rpx 0;
		font-size: 36rpx;
		font-weight: 900;
		line-height: 40rpx;
	}
	.title-box text:first-child { color: #333;}
	.title-box text:last-child { color: #0256FF; margin-left: 20rpx;}
	
	.card-box {
		margin: 0 32rpx 20rpx;
		padding: 32rpx;
		background-color: #EFF4FF;
		border-radius: 16rpx;
	}
	.card-box .sn {
		margin-right: 20rpx;
		font-size: 64rpx;
		font-weight: 900;
		color: #0256FF;
		line-height: 80rpx;
	}
	.card-box .desc-box text:first-child {
		font-size: 32rpx;
		font-weight: 500;
		color: #333;
	}
	.card-box .desc-box text:last-child {
		font-size: 24rpx;
		font-weight: 500;
		color: #666;
	}
	.card-box .img-box {
		margin-top: 40rpx;
		padding: 20rpx 70rpx;
		background-color: #fff;
		border-radius: 16rpx;
	}
	.footer {
		position: fixed;
		height: 108rpx;
		width: 100%;
		left: 0;
		bottom: 0;
		background-color: #fff;
	}
	.footer .primary-btn{
		margin: 0 32rpx;
		height: 80rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #fff;
		background-color: #0256FF;
		border: solid 1px #0256FF;
	}
</style>