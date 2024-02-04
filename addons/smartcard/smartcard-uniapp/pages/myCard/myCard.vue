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
			<!-- 名片数据 -->
			<view class="visits">
				<view class="flex flex-hsb flex-vc title-bar">
					<view class="flex-1 title">我的名片数据</view>
					<view class="flex flex-vc more">
						<text>查看详情</text>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="flex flex-hc statistics">
					<view class="flex-1 flex flex-v flex-vc flex-hc" v-for="(item, inx) in visits" :key="inx">
						<!-- <text class="num">{{item.value}}</text> -->
						<uni-badge class="uni-badge-left-margin" :text="item.value" absolute="rightTop" size="small">
							<view class="box"><text class="box-text num">{{item.value}}</text></view>
						</uni-badge>
						<text class="label">{{item.label}}</text>
					</view>
				</view>
				<view class="title2">
					最近访问
				</view>
				<view class="flex visit-item">
					<image class="avatar" src="../../static/images/img.jpg" mode="scaleToFill"></image>
					<view class="flex-1 right-box">
						<view class="flex flex-hsb flex-vc">
							<text class="name">陈丽容</text>
							<text class="time">今天 3:00</text>
						</view>
						<view class="company">
							<!-- <text>it</text>
							<text>|</text> -->
							<text>it｜陈氏空间（厦门）设计装修工程</text>
						</view>
						<view class="counts">
							第 4 次查看了我的名片‘厦门八达尔科技有限公司—总经理’
						</view>
						<view class="from">
							来源：我向对方发出的名片
						</view>
					</view>
				</view>
				<view class="flex flex-hc flex-vc more">
					<text>更访客数据</text>
					<text class="iconfont icon-Rightyou"></text>
				</view>
			</view>
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
						icon: 'icon-ziliao',
						label: '资料'
					},
					{
						icon: 'icon-moban',
						label: '模板'
					},
					{
						icon: 'icon-mingpianma',
						label: '名片吗'
					},
					{
						icon: 'icon-fenxiangshezhi',
						label: '分享设置'
					},
				],
				visits: [{
						label: '访问量(次)',
						value: 324
					},
					{
						label: '今日访问量(次)',
						value: 324
					},
					{
						label: '发名片(次)',
						value: 324
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
	.contents .card-box{
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
	/* 快捷服务 */
	.services .service-item {
		width: 25%;
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
	/* 我的名片数据 */
	.visits .statistics { height: 116rpx;}
	.visits .statistics .num{
		font-size: 28rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .statistics .label {
		margin-top: 4rpx;
		font-size: 22rpx;
		font-weight: 400;
		color: #666;
	}
	.visits .title2 {
		padding: 16rpx 0;
		font-size: 24rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .visit-item {
		padding: 20rpx 0rpx;
	}
	.visits .visit-item .avatar {
		margin-right: 16rpx;
		width: 60rpx;
		height: 60rpx;
		border-radius: 50%;
	}
	.visits .visit-item .right-box {
		border-bottom: solid 1px #E6E6E6;
	}
	.visits .visit-item .right-box > view { margin: 4rpx 0; line-height: 40rpx;}
	.visits .visit-item .name {
		font-size: 28rpx;
		font-weight: 500;
		color: #333;
	}
	.visits .visit-item .time,
	.visits .visit-item .company,
	.visits .visit-item .from {
		font-size: 22rpx;
		font-weight: 400;
		color: #999;
	}
	.visits .more {
		padding: 16rpx 0;
		font-size: 24rpx;
		font-weight: 400;
		color: #999;
	}
	.visits .more .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
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
</style>