<!-- 我的名片数据 -->
<template>
	<view>
		<view class="visit-header" @click="test()">
			<view class="card-count">
				<view class="card-count-content">
					<view>名片访问总量(次)</view>
					<view class="card-count-num">{{allVisitNum}}</view>
				</view>
				<view class="card-count-content">
					<view>今日访问总量(次)</view>
					<view class="card-count-num">{{todayVisitNum}}</view>
				</view>
			</view>
			<view class="send-count">
				<view class="send-count-item">
					<text>发出名片总量(次):</text>
					<text>{{allSendNum}}</text>
				</view>
				<view class="send-count-item">
					<text>我的靠谱总量:</text>
					<text>{{allExchangeNum}}</text>
				</view>
			</view>
		</view>
		<view class="visit-section">
			<view class="tab-box">
				<view v-for="item in menus" :key="item.value"
					:class="{ active: current === item.value }"
					@click="tabHandle(item.value)"
					v-if="!item.hidden">{{item.name}}</view>
			</view>
			<!-- <view class="flex more-search-box" v-if="current === 1">
				<view class="btn01">全部</view>
				<view class="flex flex-vc btn02">按名片查看<text class="iconfont icon-down"></text></view>
			</view> -->
			<!-- <view class="flex more-search-box" v-if="current === 3">
				<view class="btn01">待更新(0)</view>
				<view class="flex flex-vc btn02">已更新(0)</view>
			</view> -->
			<view v-for="(it, inx) in myCardList" :key="inx">
				<view class="card-date">{{it.date}}</view>
				<view class="card-item" v-for="(sit, sinx) in it.data" :key="sinx" @click="linkTo(sit)">
					<view class="photo-box">
						<image :src="sit.avatar"></image>
						<!-- <view class="visit-first" v-if="it===3">首次访问</view> -->
						<!-- <view class="visit-first lively" v-else>活跃</view> -->
					</view>
					<view class="card-content">
						<view class="flex-1">
							<view class="card-user">{{sit.name}}</view>
							<view v-if="sit.position">{{sit.position}}</view>
							<view v-if="sit.companyname">{{sit.companyname}}</view>
							<view class="card-source">来源： {{smartcardObj.origin[`${sit.origin}`] || ''}} {{sit.time || ''}}</view>
						</view>
						<view :class="{'card-exchange' : sit.status === 1, 'await-text': sit.status !== 1}"
							 @click.stop="resendCard(sit)">{{smartcardObj.status[`${sit.status}`] || ''}}</view>
					</view>
				</view>
			</view>
			<uni-load-more :status="status"></uni-load-more>
			<!--  #ifdef  MP-WEIXIN	 -->
			<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom" @cancelBottom="cancelBottom"></bottomSheet>
			<!--  #endif -->
		</view>
	</view>
</template>

<script>
	import { formatDate } from "@/uni_modules/uni-dateformat/components/uni-dateformat/date-format.js";
	import {smartcardObj, isLogin, weixinShare} from '@/config/common.js'
	import bottomSheet from '../../components/bbh-sheet/bottomSheet.vue';
	export default {
		components: {
			bottomSheet
		},
		data() {
			return {
				isShowBottom: false,
				smartcardObj: smartcardObj,
				current: 1,
				allVisitNum: 0,
				todayVisitNum: 0,
				allSendNum: 0,
				allExchangeNum: 0,
				menus: [
					{
						name: '我的访客',
						value: 1
					},
					{
						name: '我看过的',
						value: 2
					},
					{
						name: '待更新名片',
						value: 3,
						hidden: true
					}
				],
				myCardList: [],
				status: 'more'
			}
		},
		onReachBottom() {
			++this.pageNum
			this.getMyCardList()
		},
		onShow() {
			
			if (!this.checkUser()) {
				this.refreshUser()
				// #ifdef MP-WEIXIN
				this.wxLogin();
				// #endif
			}else {
				this.getStatistics()
				this.getMyCardList()
			}
		},
		methods: {
			test() {
				// uni.navigateTo({
				// 	url: '/pages/company-attestation/attestation'
				// })
			},
			tabHandle(val) {
				if (this.current === val) return
				this.current = val
				this.myCardList = []
				this.getMyCardList()
			},
			getStatistics() {
				this.$api.myCardVisit({}, res => {
					if(res.code === 1) {
						const data = res.data || {}
						this.allVisitNum = data.allVisitNum || 0
						this.todayVisitNum = data.todayVisitNum || 0
						this.allSendNum = data.allSendNum || 0
						this.allExchangeNum = data.allExchangeNum || 0
					}
				})
			},
			getMyCardList() {
				this.status = 'loading'
				this.$api.myCardList({
					page: this.pageNum,
					type: this.current
				}, res => {
					if(res.code === 1) {
						res.data = res.data || []
						this.status = res.data.length < 10 ? 'noMore' : 'more' // nomore待处理
						let result = {}, temp = []
						res.data.forEach(it => {
							it.createtime = it.createtime ? new Date(it.createtime * 1000) : new Date();
							const date = formatDate(it.createtime,"yyyy/MM/dd");
							it.time = formatDate(it.createtime,"hh:mm");
							it.date = date;
							if(result[date]) {
								result[date].data.push(it)
							} else {
								result[date] = {
									date: date,
									data: [it]
								}
							}
						})
						for(let key in result) {
							temp.push(result[key])
						}
						// console.info('result....', result, '======>temp', temp)
						this.myCardList = this.myCardList.concat(temp)
					}else {
						this.status = 'more'
					}
				}, err => {
					this.status = 'more'
				})
			},
			resendCard(row) {
				if(row.status !== 1) return;
				if(!row.staff_id) {
					uni.showToast({
						title: '用户id有问题，请重试',
						icon: 'none'
					})
					return;
				}
				this.$api.resendCard({staff_id: row.staff_id}, res => {
					if(res.code === 1) {
						uni.showToast({
							icon: 'success',
							title: res.msg || '名片回递成功'
						})
					}else {
						uni.showToast({
							icon: 'none',
							title: res.msg || '名片回递失败'
						})
					}
				})
			},
			linkTo(row) {
				if(!row.staff_id) {
					uni.showToast({
						title: '用户id有问题，请重试',
						icon: 'none'
					})
					return;
				}
				uni.navigateTo({
					url: weixinShare.url + '?staff_id='+row.staff_id + '&user_id='+row.user_id+'&origin=2'
				})
			},
			// 以下都是检测登录逻辑===============参考首页
			checkUser() {
				const flag = isLogin()
				console.info(flag, '=========>>>>flag')
				if(!flag) {
					this.isShowBottom=true
					this.user_id='';
				}
				return flag
			},
			wxLogin(){
				uni.login({
					success:(res) => {
						this.code = res.code;
						console.log("res.code: ",res.code);
					},
					fail: function (error) {
						console.log('login failed ' + error);
					}
				});
			},
			refreshUser(){
				console.log('index调用onshow');
				this.$api.refreshUser(
				 {},
				data => {
					console.log(data);
					if(data.code==1){
						this.user_id=data.data.user.id;
						// 是否已填写个人资料
						if(data.isStaff === 0) {
							uni.navigateTo({
								url: '/pages/userInfo/userInfo?user_id=' + this.user_id
							})
							return
						}
						this.getIndex();
					}else{
						//微信小程序端
						// #ifdef MP-WEIXIN
						console.log("小程序: ",1);
						this.isShowBottom=true
						this.user_id='';
						// #endif						
					}					
				})
			},
			//改版后小程序登录规则
			//小程序登录
			onGetUserProfile() {
				var platform='wechat';
				var fid=uni.getStorageSync('parentid')?uni.getStorageSync('parentid'):''; 
				uni.getUserProfile({
					 desc: '用于完善会员资料', // 声明获取用户个人信息后的用途，后续会展示在弹窗中，请谨慎填写
					success: res => {
						console.log(res)
						this.$api.third(
							{
								code: this.code,
								platform:platform,
								encrypted_data: res.encryptedData,
								iv: res.iv,
								raw_data: res.rawData,
								signature: res.signature
							},
							data => {
								console.log(data);
								//console.log(data.data.userinfo) 
								var res=data.data;
								if (data.code == 1) {
									this.$common.successToShow('登录成功!');
									try {
										this.$db.set('upload',1)
										this.$db.set('login', 1)
										this.$db.set('auth',res.auth)
										this.$db.set('user', res.userinfo)
										this.user_id=res.userinfo.id
										this.getIndex()
									} catch (e) {
										console.log("e: ",e);
									}
								}else{
									this.wxLogin();
								}
							}
						);
					},
					fail: (res) => {
						console.log("res: ",res);
						this.wxLogin();//重新获取登录code
						uni.hideLoading()
						if (res.errMsg == "getUserInfo:cancel" || res.errMsg == "getUserInfo:fail auth deny") {
							uni.showModal({
								title: '用户授权失败',
								showCancel: false,
								content: '请点击重新授权，如果未弹出授权，请尝试长按删除小程序，重新进入!',
								success: function(res) {
									if (res.confirm) {
										console.log('用户点击确定')
										//uni.navigateBack()
										this.isShowBottom = true;
									}
								}
							})
						}	
					}
				})
			},
			//底部开关
			closeBottom(){
				this.isShowBottom = false;
				this.onGetUserProfile()
			},
			cancelBottom() {
				this.isShowBottom = false;
			},
			getIndex() {
				var staff_id_c=this.staff_id || uni.getStorageSync('staff_id') || 0 // 分享进来？分享要去share页面不公用
				let condition = {
					staff_id: this.staff_id,
					// user_id: this.user_id
				}
				let api = this.$api.doIndex
				if(this.isShare) {
					api = this.$api.doIndexShare
					condition.origin = this.origin
				}
				api(condition, (res) => {
					if(res.code === 1) {
						if(res.isStaff === 0) {
							uni.navigateTo({
								url: '/pages/userInfo/userInfo?user_id=' + this.user_id
							})
							return
						}
						this.allData=res.data
						console.log(this.allData);
						this.userData = this.staffInfo = this.allData.staffInfo || {};
						this.color=this.staffInfo?this.staffInfo.smartcardtheme.colour:''
						this.backgroundImg=this.staffInfo?this.staffInfo.smartcardtheme.backgroundimage:''
						this.cardimage=this.staffInfo?this.staffInfo.smartcardtheme.cardimage:''
						this.fontcolor=this.staffInfo?this.staffInfo.smartcardtheme.fontcolor:''          
						if(uni.getStorageSync('color')==this.color){
							console.log('已有color');
						}else{
							uni.setStorageSync('color',this.color)
						}
						if(uni.getStorageSync('backgroundImg')==this.backgroundImg){
							console.log('已有backgroundImg');
						}else{
							uni.setStorageSync('backgroundImg',this.backgroundImg)
						}
						this.staff_id=this.staffInfo.id
						this.nickname=this.userData.name
						this.userData.save_status = `${this.allData.save_status}`
						this.userData.usertype = `${this.allData.usertype}`
						uni.setStorage({
							key: 'userData',
							data: this.userData
						})
						this.theme = {
							color: this.color,
							backgroundimage: this.backgroundImg,
							cardimage: this.cardimage,
							fontcolor: this.fontcolor
						}
						uni.setStorage({
							key: 'themeData',
							data: this.theme
						})
						this.getStatistics()
						this.getMyCardList()
						console.info('首页请求的接口名称： ', api, 
							'allData', this.allData, 'isShare', this.isShare, 
							'services', this.services)
					}else {
						uni.navigateTo({
							url:'/pages/userInfo/userInfo?user_id=' + this.user_id
						})
					}
				})
			},
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