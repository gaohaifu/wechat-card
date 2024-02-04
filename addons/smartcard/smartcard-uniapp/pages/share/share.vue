<template>
	<view class="overall_content">
		<view class="header_background" :style="{'background-image':'url('+cdnUrl+backgroundImg+')'}">
			<cu-custom :bgColor="bgColor" :isBack="false" :backGround="backGround">
				<block slot="backText">返回</block>
				<block slot="content">首页</block>
			</cu-custom>
			</block>
			<view class="header_padding">
				<view class="header_message" :style="{'background-image':'url('+cdnUrl+cardimage+')'}">
					<view class="flex_layout userImg">
						<image :src="userData.avatar?userData.avatar:'../../static/images/user.png'" mode=""></image>
						<view class="name_position">
							<view :style="{color:fontcolor}">{{userData.name?userData.name:''}}</view>
							<text :style="{color:fontcolor}">{{userData.position}}</text>
							<text :style="{color:fontcolor}">{{companyInfo.name}}</text>
						</view>
					</view>
					<view class="waitOp" :style="{color:fontcolor}" v-if="!certificateStatus">未认证</view>
					<view class="extra">
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-dianhua"></i><text
								:style="{color:fontcolor}">{{userData.mobile?userData.mobile:'暂未填写'}}</text></view>
						<view class="flex_layout"><i :style="{color:fontcolor}" class="iconfont icon-youjian1"></i><text
								:style="{color:fontcolor}">{{userData.email?userData.email:'暂未填写'}}</text></view>
						<view class="flex_layout" v-if="companyInfo.address"><i :style="{color:fontcolor}"
								class="iconfont icon-weizhi"></i><text
								:style="{color:fontcolor}">{{companyInfo.address?companyInfo.address:'暂未填写'}}</text>
						</view>
					</view>
					<!--切换按钮-->
					<!-- <view class="change_tab flex_layout" v-if="user_id==userData.user_id" @click="changeTab">
						<image src="../../static/images/change.png" mode=""></image><text>切换</text>
					</view> -->
				</view>
			</view>
		</view>
		<view class="overall_padding contents">
			<!-- 快捷工具 -->
			<view class="flex_between tools">
				<view class="flex flex-v flex-vc flex-hc tool-item" v-for="(item, inx) in tools" :key="inx">
					<text class="iconfont" :class="item.icon" :style="{color: item.color}"></text>
					<text>{{item.label}}</text>
				</view>
			</view>
			<!-- 认证状态 -->
			<view class="flex flex-hsb flex-vc cert-box">
				<view class="flex flex-vc left-box">
					<image src="../../static/images/cert-status.png" mode=""></image>
					<text>Ta的认证</text>
				</view>
				<view class="flex right-box">
					<view class="flex flex-vc flex-hc enterprise-cert">
						<image src="../../static/images/enterprise-cert.png" mode=""></image>
						<text>企业认证</text>
					</view>
					<view class="flex flex-vc flex-hc personal-cert">
						<image src="../../static/images/personal-cert.png" mode=""></image>
						<text>个人认证</text>
					</view>
				</view>
			</view>
			<!-- 快捷服务 -->
			<view class="services">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">服务</view>
					<view class="flex flex-vc more">
						<text>查看详情</text>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="flex_between">
					<view class="flex flex-v flex-vc flex-hc flex-wrap service-item" v-for="(item, inx) in services" :key="inx">
						<text class="iconfont" :class="item.icon"></text>
						<text>{{item.label}}</text>
					</view>
				</view>
			</view>
			
			<!-- 企业视频 -->
			<view class="card-box">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">企业视频</view>
					<view class="flex flex-vc more" @click="toggleCardBox('showEnterpriseVideo')">
						<template v-if="!showEnterpriseVideo">
							<text>查看详情</text>
							<text class="iconfont icon-Rightyou"></text>
						</template>
						<text class="iconfont icon-down" v-else></text>
					</view>
				</view>
				<view class="video" v-show="showEnterpriseVideo">
					
				</view>
			</view>
			<!-- 企业简介 -->
			<view class="card-box">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">企业简介</view>
					<view class="flex flex-vc more" @click="toggleCardBox('showEnterpriseProfile')">
						<template v-if="!showEnterpriseProfile">
							<text>查看详情</text>
							<text class="iconfont icon-Rightyou"></text>
						</template>
						<text class="iconfont icon-down" v-else></text>
					</view>
				</view>
				<view class="" v-show="showEnterpriseProfile">
					<view class="desc-box">
						<view class="title">
							销售分析
						</view>
						<text class="desc">
							智能评定员工销售能力，销售排行情况一目了然
							总览每月销售数据，制定销售目标得心应手
						</text>
					</view>
				</view>
			</view>
		</view>
		
		<!-- 分享栏 -->
		<view class="flex flex-vc share-box">
			<view class="flex flex-v flex-vc left-box">
				<text class="iconfont icon-daorutongxunlu"></text>
				<text>导入通讯录</text>
			</view>
			<view class="flex-1 flex">
				<!-- <view class="flex-1 primary-btn">回递名片</view> -->
				<view class="flex-1 plain-btn" style="marign-right: 20rpx;">分享Ta的名片</view>
				<view class="flex-1 primary-btn">已回递</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				tools: [{
						icon: 'icon-dadianhua',
						color: '#0256FF',
						label: '打电话'
					},
					{
						icon: 'icon-jiaweixin',
						color: '#07C160',
						label: '加微信'
					},
					{
						icon: 'icon-fayoujian',
						color: '#FF4E20',
						label: '发邮箱'
					},
					{
						icon: 'icon-dingwei',
						color: '#02B7FF',
						label: '定位'
					},
					{
						icon: 'icon-famingpian',
						color: '#0256FF',
						label: '发名片'
					},
				],
				services: [{
						icon: 'icon-qiyejianjie',
						label: '企业简介'
					},
					{
						icon: 'icon-xiangce',
						label: '我的相册'
					},
					{
						icon: 'icon-qiyedongtai',
						label: '企业动态'
					},
					{
						icon: 'icon-qiyexuanchuance',
						label: '企业宣传册'
					},
					{
						icon: 'icon-chenggonganli',
						label: '成功案例'
					},
					{
						icon: 'icon-rexiaoshangpin',
						label: '热销商品'
					},
				],
				showEnterpriseVideo: true,
				showEnterpriseProfile: true
			}
		},
		mounted() {
			const innerAudioContext = uni.createInnerAudioContext();
			innerAudioContext.autoplay = true;
			innerAudioContext.src = 'https://bjetxgzv.cdn.bspapp.com/VKCEYUGU-hello-uniapp/2cc220e0-c27a-11ea-9dfb-6da8e309e0d8.mp3';
			innerAudioContext.onPlay(() => {
			  console.log('开始播放');
			});
			innerAudioContext.onError((res) => {
			  console.log(res.errMsg);
			  console.log(res.errCode);
			});
		},
		methods: {
			toggleCardBox(dataProp) {
				this[dataProp] = !this[dataProp]
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');

	page { background-color: #F6F7F9;}

	.header_background {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}

	.header_padding {
		padding: 0 30rpx;
	}

	.overall_padding {
		padding: 30rpx 15rpx;
	}

	.header_background {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}

	.header_padding {
		padding: 0 30rpx;
	}

	.overall_padding {
		padding: 30rpx 15rpx;
	}

	.grade {
		text-align: right;
		font-size: 26rpx;
		margin-top: 20rpx;
	}

	.header_message {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
		padding: 40rpx 40rpx;
		margin-top: 30rpx;
		border-radius: 15px;
		color: #e1d27e;
		box-shadow: 0 0 10px #999;
		position: relative;
	}

	.userImg image {
		display: block;
		width: 130rpx;
		height: 130rpx;
		border-radius: 50%;
	}

	.name_position {
		padding-left: 30rpx;
		width: 400rpx;
	}

	.name_position view {
		font-size: 42rpx;
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.name_position text {
		display: block;
		font-size: 26rpx;
		margin-top: 10rpx;
		width: 100%;
		overflow: hidden;
		white-space: nowrap;
		text-overflow: ellipsis;
	}

	.contents .tools,
	.contents .services,
	.contents .visits,
	.contents .card-box,
	.contents .cert-box{
		margin-top: 20rpx;
		padding: 0rpx 20rpx 20rpx;
		background-color: #fff;
		border-radius: 16rpx;
	}

	/* 工具列表 */
	.tools {
		background-color: transparent;
		padding: 20rpx 0;
	}
	.tools .tool-item {
		width: 125rpx;
		height: 136rpx;
		background-color: #fff;
		border-radius: 20rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #333;
	}

	.tools .tool-item .iconfont {
		width: 64rpx;
		height: 64rpx;
		font-size: 64rpx;
		text-align: center;
	}
	/* 认证状态 */
	.contents .cert-box {
		height: 84rpx;
		padding: 0 32rpx;
	}
	.cert-box .left-box {
		font-weight: 400;
		font-size: 28rpx;
		color: #333;
	}
	.cert-box .left-box image {
		width: 32rpx;
		height: 32rpx;
		margin-right: 8rpx;
	}
	.cert-box .right-box {
		font-weight: 400;
		font-size: 20rpx;
		color: #fff;
	}
	.cert-box .right-box .enterprise-cert,
	.cert-box .right-box .personal-cert {
		width: 132rpx;
		height: 34rpx;
		border-radius: 20px 20px 20px 20px;
	}
	.cert-box .right-box .enterprise-cert { background: #0256FF; margin-right: 8rpx;}
	.cert-box .right-box .personal-cert { background: #EAB863;}
	.cert-box .right-box image {
		width: 24rpx;
		height: 24rpx;
		margin-right: 4rpx;
	}
	/* 快捷服务 */
	.services .service-item {
		width: 33.33%;
		height: 136rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #333;
	}
	.services .service-item .iconfont {
		width: 48rpx;
		height: 48rpx;
		font-size: 48rpx;
		color: #0256FF;
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
	
	/* 企业视频 + 企业简介 */
	.card-box .desc-box {
		padding: 16rpx 0;
		text-align: center;
	}
	.card-box .desc-box .title {
		font-size: 32rpx;
		font-weight: 500;
		color: #333;
	}
	.card-box .desc-box .desc {
		margin-top: 16rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #666;
	}
	.card-box image {
		margin-top: 16rpx;
		width: 88%;
		height: auto;
		margin: 0 auto;
	}
	/* 底部栏 */
	.share-box {
		height: 108rpx;
		padding: 0 32rpx;
		position: fixed;
		bottom: 0;
		width: 100%;
		background-color: #fff;
	}
	.share-box .left-box { 
		margin-right: 20rpx; 
		text-align: center;
		font-size: 20rpx;
		color: #666;
		font-weight: 400;
		}
	.share-box .left-box .iconfont {
		width: 48rpx;
		height: 48rpx;
		font-size: 48rpx;
		text-align: center;
		color: #0256FF;
	}
	.share-box .primary-btn,
	.share-box .plain-btn{
		height: 80rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: 400;
	}
	.share-box .primary-btn {
		color: #fff;
		background-color: #0256FF;
		border: solid 1px #0256FF;
	}
	.share-box .plain-btn {
		color: #333;
		background-color: #fff;
		border: solid 1px #E6E6E6;
	}
</style>