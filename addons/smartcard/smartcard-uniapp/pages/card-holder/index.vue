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
		<!--  #ifdef  MP-WEIXIN	 -->
		<bottomSheet :isShowBottom="isShowBottom" @closeBottom="closeBottom" @cancelBottom="cancelBottom"></bottomSheet>
		<!--  #endif -->
	</view>
</template>

<script>
	import NoData from '../../components/no-data/no-data.vue'
	import bottomSheet from '@/components/bbh-sheet/bottomSheet.vue';
	import {isLogin} from '@/config/common.js'
	export default {
		name: 'cardHolder',
		components: {
			NoData,
			bottomSheet
		},
		data() {
			return {
				isShowBottom: false,
				searchList: [],
				exchangeCardNum: 0,
				exchangeCards: [],
				allCards: []
			}
		},
		onShow() {
			
			if (!this.checkUser()) {
				this.refreshUser()
				// #ifdef MP-WEIXIN
				this.wxLogin();
				// #endif
			}else {
				this.getData()
			}
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
						this.getData()
						
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