<template>
	<view>
		<view class="visit-header">
			<view class="card-count">
				<view class="card-count-content">
					<view>名片访问总量(次)</view>
					<view class="card-count-num">22</view>
				</view>
				<view class="card-count-content">
					<view>今日访问总量(次)</view>
					<view class="card-count-num">0</view>
				</view>
			</view>
			<view class="send-count">
				<view class="send-count-item">
					<text>发出名片总量(次):</text>
					<text>12</text>
				</view>
				<view class="send-count-item">
					<text>我的靠谱总量:</text>
					<text>12</text>
				</view>
			</view>
		</view>
		<view class="visit-section">
			<view class="tab-box">
				<view v-for="item in menus" :key="item.value" :class="{ active: current === item.value }" @click="tabHandle(item.value)">{{item.name}}</view>
			</view>
			<view class="flex more-search-box" v-if="current === 0">
				<view class="btn01">全部</view>
				<view class="flex flex-vc btn02">按名片查看<text class="iconfont icon-down"></text></view>
			</view>
			<view class="flex more-search-box" v-if="current === 2">
				<view class="btn01">待更新(0)</view>
				<view class="flex flex-vc btn02">已更新(0)</view>
			</view>
			<view v-for="it in [3, 6]" :key="it">
				<view class="card-date">2023/02/05</view>
				<view class="card-item" v-for="item in [1,2]" :key="item">
					<view class="photo-box">
						<image src="../../static/images/img.jpg"></image>
						<view class="visit-first" v-if="it===3">首次访问</view>
						<view class="visit-first lively" v-else>活跃</view>
					</view>
					<view class="card-content">
						<view class="flex-1">
							<view class="card-user">陈立荣</view>
							<view>总经理</view>
							<view>厦门巴达尔科技有限公司</view>
							<view class="card-source" v-if="it===3">来源 | 对方名片夹 16:22</view>
						</view>
						<view class="await-text" v-if="it===3">等待同意</view>
						<view class="card-exchange" v-else>交换名片</view>
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				current: 0,
				menus: [
					{
						name: '我的访客',
						value: 0
					},
					{
						name: '我看过的',
						value: 1
					},
					{
						name: '待更新名片',
						value: 2
					}
				]
			}
		},
		methods: {
			tabHandle(val) {
				if (this.current === val) return
				this.current = val
			}
		}
	}
</script>

<style>
	.visit-header {
		padding: 0 32rpx 32rpx;
		background-color: #0256FF;
	}
	
	.card-count {
		display: flex;
		overflow: hidden;
		padding-top: 8rpx;
		padding-bottom: 20rpx;
		border-bottom: 2rpx solid #8095FF;
	}
	
	.card-count-content {
		width: 50%;
		overflow: hidden;
		white-space: nowrap;
		font-size: 30rpx;
		color: #fff;
	}
	
	.card-count-num {
		height: 80rpx;
		line-height: 80rpx;
		font-size: 56rpx;
		font-weight: 700;
		margin-top: 16rpx;
	}
	
	.send-count {
		display: flex;
		padding: 26rpx 0;
		overflow: hidden;
	}
	
	.send-count-item {
		width: 50%;
		display: flex;
		margin: 10rpx 0;
		height: 28rpx;
		line-height: 28rpx;
		font-size: 26rpx;
		color: #fff;
		overflow: hidden;
		white-space: nowrap;
	}
	
	.send-count-item:nth-child(1) {
		border-right: 2rpx solid #8095FF;
	}
	
	.send-count-item text:nth-child(2) {
		flex: 1;
		text-align: right;
	}
	
	.send-count-item:nth-child(1) text:nth-child(2) {
		margin-right: 32rpx;
	}
	
	.send-count-item:nth-child(2) text:nth-child(1) { 
		margin-left: 32rpx;
	}
	
	.visit-section {
		margin-top: -32rpx;
		border-radius: 32rpx 32rpx 0 0;
		padding: 0 32rpx;
		background-color: #fff;
	}
	
	.tab-box {
		display: flex;
		justify-content: space-around;
		margin-bottom: 24rpx;
	}
	
	.tab-box view {
		padding-top: 8rpx;
		height: 80rpx;
		line-height: 80rpx;
	}
	
	.tab-box view.active {
		border-bottom: 4rpx solid #0256FF;
		color: #0256FF;
		font-weight: 700;
	}
	
	.card-item {
		display: flex;
		margin-bottom: 20rpx;
	}
	
	.photo-box {
		position: relative;
		flex-shrink: 0;
		width: 80rpx;
		height: 80rpx;
		margin-right: 32rpx;
	}
	
	.photo-box image {
		width: 100%;
		height: 100%;
		border-radius: 40rpx;
	}
	
	.visit-first {
		position: absolute;
		left: 0;
		right: 0;
		bottom: -16rpx;
		height: 32rpx;
		line-height: 32rpx;
		text-align: center;
		font-size: 18rpx;
		color: #333;
		border-radius: 20rpx;
		background-color: #F2F2F2;
	}
	
	.visit-first.lively {
		background: linear-gradient(180deg, #FFE043 0%, #FF5543 100%);
		color: #fff;
	}
	
	.card-content {
		flex: 1;
		display: flex;
		border-bottom: 1px solid #F6F7F9;
		line-height: 48rpx;
		font-size: 24rpx;
		color: #666;
		padding-bottom: 24rpx;
		overflow: hidden;
	}
	
	.card-user {
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 32rpx;
		color: #333;
		font-weight: 500;
	}
	
	.card-source {
		color: #B2B2B2;
	}
	
	.card-date {
		line-height: 40rpx;
		font-size: 28rpx;
		color: #333;
		padding: 14rpx;
	}
	
	.await-text {
		flex-shrink: 0;
		margin-left: 24rpx;
		line-height: 40rpx;
		font-size: 28rpx;
		color: #999;
		white-space: nowrap;
		margin-top: 80rpx;
	}
	
	.card-exchange {
		flex-shrink: 0;
		width: 160rpx;
		height: 64rpx;
		line-height: 64rpx;
		text-align: center;
		border-radius: 60rpx;
		border: 2rpx solid #0256FF;
		font-size: 24rpx;
		color: #0256FF;
		margin-top: 40rpx;
	}
	
	/* 更多搜索条件 */
	.more-search-box {
		margin-bottom: 14rpx;
	}
	
	.more-search-box .btn01,
	.more-search-box .btn02 {
		margin-right: 16rpx;
		height: 64rpx;
		padding: 0 32rpx;
		border-radius: 64rpx;
		font-size: 24rpx;
		font-weight: 400;
	}
	
	.more-search-box .btn01 {
		background-color: #E5EEFF;
		color: #0256FF;
		line-height: 64rpx;
	}
	
	.more-search-box .btn02 {
		background-color: #F2F2F2;
		color: #333;
	}
	
	.more-search-box .btn02 .iconfont {
		margin-left: 6rpx;
		width: 24rpx;
		height: 24rpx;
		line-height: 28rpx;
		font-size: 24rpx;
		text-align: center;
	}
</style>