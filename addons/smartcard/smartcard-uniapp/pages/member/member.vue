<template>
	<view>
		<view class="flex_between focus-box">
			<text>关注公众号，及时查收成员申请</text>
			<view class="primary-btn">立即关注</view>
		</view>
		<view class="flex menu-box">
			<view class="flex-1 flex flex-vc flex-hc menu-item" :class="{'active' : item.value === activeId}" v-for="(item, inx) in menu"
				:key="inx">
				{{item.label}}
			</view>
		</view>
		<view class="flex flex-vc search-box">
			<icon type="search" size="16" color="#858585" style="margin-right: 8rpx;" />
			<input v-model="keyword" class="uni-input" placeholder="请输入姓名/公司/职位" @input="doSearch" />
		</view>
		<view class="flex more-search-box">
			<view class="btn01">全部模板</view>
			<view class="flex flex-vc btn02">按模板筛选<text class="iconfont icon-down"></text></view>
		</view>
		<view class="flex flex-hsb member-list">
			<image class="avatar" src="../../static/images/img.jpg" mode=""></image>
			<view class="flex-1 flex flex-vc right-box">
				<view class="flex-1">
					<view class="flex flex-vc">
						<text class="name">陈丽容</text>
						<text class="tag">超级管理员</text>
					</view>
					<view class="position">总经理</view>
					<view class="muban">所属模板</view>
				</view>
				<text class="iconfont icon-gengduo"></text>
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
						label: '已加入成员',
						value: '01'
					},
					{
						label: '申请加入',
						value: '02'
					},
					{
						label: '等待激活',
						value: '03'
					},
				],
				keyword: ''
			}
		},
		methods: {
			doSearch() {

			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	.focus-box {
		height: 88rpx;
		padding: 0 32rpx;
		background-color: rgba(2, 86, 255, 0.10);
		font-size: 28rpx;
		font-weight: 400;
		color: #0256FF;
	}

	.focus-box .primary-btn {
		width: 160rpx;
		height: 48rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 48rpx;
		line-height: 48rpx;
		font-size: 24rpx;
		font-weight: 400;
		color: #fff;
		background-color: #0256FF;
		border: solid 2rpx #0256FF;
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
		width: 80%;
		height: 4rpx;
		background-color: #0256FF;
	}

	/* 搜索框 */
	.search-box {
		margin: 24rpx 32rpx 0;
		height: 64rpx;
		padding: 0 16rpx;
		border-radius: 64rpx;
		background-color: #F6F7F9;
	}

	/* 更多搜索条件 */
	.more-search-box {
		margin: 36rpx 32rpx 16rpx;
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

	/* 列表 */
	.member-list {
		margin: 0 32rpx;
	}

	.member-list .avatar {
		margin-right: 32rpx;
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
	}

	.member-list .right-box {
		padding-bottom: 24rpx;
		border-bottom: 2rpx solid #E6E6E6;
	}

	.member-list .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
		color: #666;
		line-height: 32rpx;
		text-align: center;
	}

	.member-list .name {
		margin-right: 6rpx;
		font-size: 32rpx;
		font-weight: 600;
		color: #333;
	}

	.member-list .tag {
		padding: 0 12rpx;
		height: 32rpx;
		line-height: 32rpx;
		font-size: 18rpx;
		color: #6C450E;
		border-radius: 8rpx;
		background-image: linear-gradient(to right, #FAD495, #E9B660);
	}

	.member-list .position,
	.member-list .muban {
		line-height: 40rpx;
		font-size: 24rpx;
		font-weight: 400;
		color: #666;
	}
</style>