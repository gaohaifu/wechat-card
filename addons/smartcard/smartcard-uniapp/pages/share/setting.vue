<template>
	<view class="page-wrap">
		<view class="flex menu-box">
			<view class="flex-1 flex flex-vc flex-hc menu-item" :class="{'active' : item.value === activeId}"
				v-for="(item, inx) in menu" :key="inx" @click="tabchange(inx)">
				{{item.label}}
			</view>
		</view>
		<view class="scene-box">
			<view class="scene-date">{{ `${systemDate.year}年${systemDate.month}月${systemDate.day}日 ${systemDate.time}`}}</view>
			<view class="scene">
				<image class="scene-photo" src="../../static/images/img.jpg" />
				<view class="scene-text">设置招呼语和分享封面后，您发给微信朋友的名片将展示如下</view>
			</view>
			<view class="scene">
				<image class="scene-photo photo-right" src="../../static/images/img.jpg" />
				<view class="scene-text text-right">
					<view>{{ word ? word : '这是我的名片，请收下' }}</view>
					<image class="card-img" src="../../static/images/mp.jpg" />
				</view>
			</view>
		</view>
		<view class="setting-box">
			<view class="word-box">
				<text>招呼语</text>
				<input v-model="word" placeholder="请输入招呼语" />
				<image src="../../static/images/right.png" />
			</view>
			<view>分享封面</view>
			<scroll-view class="cover-box" scroll-x="true">
				<view class="cover-img" :class="{'active': checkIndex === item}" v-for="item in [1,2,3,4,5,6]" :key="item" @click="checkHandle(item)">
					<image src="../../static/images/mp.jpg" />
					<view class="checked-icon">
						<image src="../../static/images/cover_icon.png" />
					</view>
				</view>
			</scroll-view>
			<view class="save-box">
				<view class="send-box">
					<image src="../../static/images/send_icon.png" />
					<view>发送名片</view>
				</view>
				<view class="save-btn">保存</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			const date = new Date()
			const month = date.getMonth()+ 1
			const day = date.getDay()
			const hour = date.getHours()
			const minutes = date.getMinutes()
			return {
				systemDate: {
					year: date.getFullYear(),
					month:  month > 9 ? month : '0' + month,
					day: day > 9 ? day : '0' + day,
					time: '' + (hour > 9 ? hour : '0' + hour) + ':' +(minutes > 9 ? minutes : '0' + minutes)
				},
				activeId: '01',
				menu: [
					{
						label: '分享卡片设置',
						value: '01'
					},
					{
						label: '分享隐私设置',
						value: '02'
					},
				],
				checkIndex: 1,
				word: '',
			}
		},
		methods: {
			tabchange (index) {
				this.activeId = this.menu[index].value
			},
			checkHandle(index) {
				this.checkIndex = index
			}
		}
	}
</script>

<style>
	.page-wrap {
		background-color: #F6F7F9;
		padding-bottom: 448rpx;
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
	.scene-box {
		padding: 1rpx 32rpx;
		background-color: #F6F7F9;
	}
	.scene-date {
		text-align: center;
		font-size: 24rpx;
		line-height: 28rpx;
		color: #999;
		margin-top: 16rpx;
		margin-bottom: 24rpx;
	}
	.scene {
		display: flex;
		margin-bottom: 44rpx;
	}
	.scene-photo {
		flex-shrink: 0;
		width: 80rpx;
		height: 80rpx;
		border-radius: 40rpx;
		margin-right: 20rpx;
	}
	.scene-text {
		flex: 1;
		margin-right: 104rpx;
		padding: 16rpx;
		background-color: #fff;
		margin-top: 8rpx;
		font-size: 28rpx;
		color: #333;
		line-height: 40rpx;
		border-radius: 16rpx;
		overflow: hidden;
	}
	.photo-right {
		margin-right: 0;
		margin-left: 20rpx;
		order: 2;
	}
	.text-right {
		margin-right: 0;
		margin-left: 104rpx;
		text-align: right;
		padding-top: 20rpx;
	}
	.card-img {
		max-width: 100%;
		height: 340rpx;
		margin-top: 16rpx;
	}
	.setting-box {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		padding: 0 32rpx;
		background-color: #fff;
		box-sizing: border-box;
		font-size: 28rpx;
		color: #999;
	}
	.word-box {
		display: flex;
		align-items: center;
		height: 88rpx;
		line-height: 88rpx;
		border-bottom: 2rpx solid #E6E6E6;
		margin-bottom: 24rpx;
	}
	.word-box text {
		white-space: nowrap;
		margin-right: 16rpx;
	}
	.word-box input {
		flex: 1;
		color: #333;
	}
	.word-box image {
		flex-shrink: 0;
		width: 11rpx;
		height: 19rpx;
		margin-left: 16rpx;
	}
	.cover-box {
		white-space: nowrap;
		width: 100%;
		margin: 24rpx 0;
	}
	.cover-img {
		position: relative;
		display: inline-block;
		width: 200rpx;
		height: 142rpx;
		margin-right: 20rpx;
	}
	.cover-img image{
		width: 100%;
		height: 100%;
	}
	.checked-icon {
		display: none;
		position: absolute;
		right: 0;
		bottom: 0;
		width: 64rpx;
		height: 64rpx;
	}
	.checked-icon image {
		width: 100%;
		height: 100%;
	}
	.cover-img.active .checked-icon {
		display: block;
	}
	.save-box {
		display: flex;
		align-items: center;
		padding: 0 32rpx;
		height: 108rpx;
		margin-bottom: 24rpx;
	}
	.send-box {
		flex-shrink: 0;
		margin-right: 20rpx;
		font-size: 20rpx;
		color: #666;
		text-align: center;
	}
	.send-box image {
		width: 48rpx;
		height: 48rpx;
	}
	.save-btn {
		flex: 1;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		background: #0256FF;
		font-size: 28rpx;
		color: #fff;
		border-radius: 60rpx;
	}
</style>