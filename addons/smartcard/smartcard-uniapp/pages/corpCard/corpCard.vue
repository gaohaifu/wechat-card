<template>
	<view>
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
</template>

<script>
	export default {
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
				companyInfo: {}
			}
		},
		onLoad() {
			this.getData()
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
			linkTo(row) {
				uni.navigateTo({
					url: '/pages/member/member?type=' + row.id
				})
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	page {
		background-color: #F6F7F9;
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