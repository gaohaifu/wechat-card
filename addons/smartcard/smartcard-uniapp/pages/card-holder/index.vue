<!-- 名片夹 -->
<template>
	<view class="page-wrap">
		<view class="section-one">
			<view class="search-box">
				<image class="search-icon" src="../../static/images/search.png" />
				<input placeholder="请输入姓名/公司/职位" @input="doSearch" />
			</view>
			<view class="wechart-card">
				<view class="wechart-left">
					<image src="../../static/images/wechart.png" />
					<text>微信名片组</text>
				</view>
				<view class="wechart-right">
					<text>建立自己的人脉圈</text>
					<image src="../../static/images/right.png" />
				</view>
			</view>
			<view class="wechart-card exchange-card" >
				<view class="wechart-left">
					<image src="../../static/images/exchange_icon.png" />
					<text>名片交换请求</text>
				</view>
				<view class="wechart-right">
					<text>全部请求</text>
					<text class="exchange-num">{{exchangeCardNum}}</text>
					<image src="../../static/images/right.png" />
				</view>
			</view>
			<view v-for="item in exchangeCards" :key="item">
				<view class="card-item">
					<image :src="item.avatar"></image>
					<view class="card-content">
						<view class="card-user">
							<view>{{item.name}}</view>
							<view class="exchange-btn" @click="agreeExchange(item)">同意</view>
						</view>
						<view>{{item.position}}</view>
						<view>{{item.companyname}}</view>
					</view>
				</view>
				<view class="exchange-content">
					<view class="excontent-text">
						对方发起与名片 <text>{{item.myStaff.companyname}}｜{{item.myStaff.position}}</text> 的交换
					</view>
					<view class="excontent-text flex flex-hsb">
						<view>来源：{{item.origin}}</view>
						<view>{{item.createtime}}</view>
					</view>
				</view>
			</view>
			<no-data v-if="exchangeCards.length === 0" />
		</view>
		<view class="section-two" v-show="searchList.length === 0">
			<view class="section-title">所有名片（{{allCards.length}}）</view>
			<view class="card-item" v-for="item in allCards" :key="item">
				<image :src="item.avatar"></image>
				<view class="card-content">
					<view class="card-user">
						<view>{{item.name}}</view>
						<text>{{item.createtime}}</text>
					</view>
					<view>{{item.position}}</view>
					<view>{{item.companyname}}</view>
				</view>
			</view>
			<no-data v-if="allCards.length === 0" />
		</view>
		<view class="section-two" v-show="searchList.length > 0">
			<view class="section-title">搜索结果</view>
			<view class="card-item" v-for="item in searchList" :key="item">
				<image :src="item.avatar"></image>
				<view class="card-content">
					<view class="card-user">
						<view>{{item.name}}</view>
						<text>{{item.createtime}}</text>
					</view>
					<view>{{item.position}}</view>
					<view>{{item.companyname}}</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import NoData from '../../components/no-data/no-data.vue'
	export default {
		name: 'cardHolder',
		components: {
			NoData
		},
		data() {
			return {
				searchList: [],
				exchangeCardNum: 0,
				exchangeCards: [],
				allCards: []
			}
		},
		onShow() {
			this.getData()
		},
		methods: {
			getData() {
				this.$api.cardHolder({}, res => {
					(res.exchangeCards || []).forEach(it => {
						it.myStaff = it.myStaff || [] // 识别不了...
					})
					this.exchangeCardNum = res.exchangeCardNum || 0
					this.exchangeCards = res.exchangeCards || []
					this.allCards = res.allCards || []
				})
			},
			agreeExchange(row) {
				this.$api.agreeExchange({su_id: row.id}, res => {
					if(res.code === 1) {
						uni.showToast({
							icon: 'success',
							title: res.msg || '名片交换成功'
						})
					}else {
						uni.showToast({
							icon: 'none',
							title: res.msg || '名片交换失败'
						})
					}
				})
			},
			doSearch({detail}) {
				setTimeout(() => {
					if(detail.value) {
						this.$api.myCardSearch({
							keyword: detail.value
						}, res => {
							res.data = res.data || []
							if(res.data.length === 0) {
								uni.showToast({
									icon: 'none',
									title: '搜索不到数据'
								})
							} else {
								this.searchList = res.data
							}
						})
					} else {
						this.searchList = []
					}
				}, 500)
			}
		}
	}
</script>

<style>
	.page-wrap {
		background-color: #F6F7F9;
		border-bottom: 1px solid #F6F7F9;
	}
	.section-one {
		background-color: #fff;
		padding: 1rpx 32rpx;
		margin-bottom: 16rpx;
	}
	.search-box {
		display: flex;
		align-items: center;
		height: 64rpx;
		line-height: 64rpx;
		margin: 8rpx 0 32rpx;
		background-color: #F6F7F9;
		border-radius: 40rpx;
	}
	.search-icon {
		flex-shrink: 0;
		width: 32rpx;
		height: 32rpx;
		margin-right: 16rpx;
		margin-left: 16rpx;
	}
	.search-box input {
		flex: 1;
		font-size: 24rpx;
		margin-right: 16rpx;
	}
	.wechart-card {
		height: 100rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;
		font-size: 24rpx;
		color: #999;
		border-bottom: 2rpx solid #E6E6E6;
	}
	.exchange-card {
		border-bottom: 0;
		margin-bottom: 16rpx;
	}
	.exchange-num {
		padding: 4rpx 8rpx;
		height: 26rpx;
		line-height: 26rpx;
		background-color: #EA0000;
		border-radius: 16rpx;
		font-size: 18rpx;
		color: #fff;
		margin-left: 8rpx;
	}
	.exchange-btn {
		width: 112rpx;
		height: 56rpx;
		background: #0256FF;
		border-radius: 60rpx;
		line-height: 56rpx;
		text-align: center;
		font-size: 24rpx;
		color: #fff;
	}
	.exchange-content {
		padding: 0 28rpx;
		margin-bottom: 24rpx;
		background-color: #FAFAFA;
		border-radius: 16rpx;
		line-height: 40rpx;
		font-size: 22rpx;
		color: #999;
	}
	.excontent-text {
		padding: 16rpx 0;
		border-bottom: 2rpx solid #E6E6E6;
	}
	.excontent-text:nth-last-child(1) {
		border: 0;
	}
	.excontent-text text {
		display: inline-block;
		padding: 0 8rpx;
		color: #333;
	}
	.wechart-left {
		font-size: 28rpx;
		color: #333;
		display: flex;
		align-items: center;
	}
	.wechart-left image {
		width: 48rpx;
		height: 48rpx;
		margin-right: 16rpx;
	}
	.wechart-right image {
		width: 11rpx;
		height: 19rpx;
		margin-left: 20rpx;
	}
	.section-two {
		background-color: #fff;
		margin-bottom: 48rpx;
		padding: 0 32rpx;
	}
	.section-title {
		height: 80rpx;
		font-size: 32rpx;
		line-height: 80rpx;
		color: #333;
		font-weight: 700;
		margin-bottom: 16rpx;
	}
	.card-item {
		display: flex;
		margin-bottom: 20rpx;
	}
	.card-item image {
		flex-shrink: 0;
		width: 80rpx;
		height: 80rpx;
		border-radius: 40rpx;
		margin-right: 32rpx;
	}
	.card-content {
		flex: 1;
		border-bottom: 1px solid #F6F7F9;
		line-height: 48rpx;
		font-size: 24rpx;
		color: #666;
		padding-bottom: 24rpx;
	}
	.card-user {
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 32rpx;
		color: #333;
		font-weight: 500;
	}
	.card-user text{
		font-size: 22rpx;
		color: #999;
		font-weight: 400;
	}
</style>