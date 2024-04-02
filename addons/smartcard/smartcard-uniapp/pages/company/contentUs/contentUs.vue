<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<view class="content_us">
			<view class="content_img" :class="userData?'':'margin-top'">
				<block v-if="companyData.picimages.length>0">
					<swiper :indicator-dots="true" :autoplay="true" :interval="3000" :duration="1000">
						<swiper-item v-for="(item,index) in companyData.picimages" :key="index">
							<view class="swiper-item" @click="previewImg(item)"><image mode="aspectFill" :src="cdnUrl+item"  @click="previewImg(index)"></image></view>
						</swiper-item>
					</swiper>
				</block>
				<block v-if="companyData.content"><view class="c-content"><rich-text :nodes="companyData.content"></rich-text></view></block>
			</view>
			<view class="information">
				<view class="title flex_layout" @click="trends">
					<view class="left_title flex_layout">
						<view class="ornament"><view></view></view>
						<text>企业资讯</text>
					</view>
					<view class="right_title">更多<image src="../../static/images/right.png" mode=""></image></view>
				</view>
				<view class="information_content">
					<view class="information_item flex_layout" v-for="(item,index) in trendsList" :key="index" @click="detail(item.id)">
						<view class="left_information">
							<view class="information_title">{{item.title}}</view>
							<view class="data_label flex_layout">
								<text>{{item.updatetime}}</text>
							</view>
						</view>
						<view class="right_information">
							<image :src="cdnUrl+item.picimages[0]" mode="aspectFill"></image>
						</view>
					</view>
				</view>
			</view>
			
			<view class="video">
				<view class="title flex_layout" @click="video">
					<view class="left_title flex_layout">
						<view class="ornament"><view></view></view>
						<text>企业视频</text>
					</view>
					<view class="right_title">更多<image src="../../static/images/right.png" mode=""></image></view>
				</view>
				<view class="video_content">
					<view class="video_item" v-for="(item,index) in companyData.videofiles" :key="index">
						<video :id="`video${index}`" @play="playVideo(`video${index}`)" :src="cdnUrl+item" @error="videoErrorCallback"></video>
					</view>
				</view>
			</view>
			
			<view class="partner">
				<view class="title flex_layout">
					<view class="left_title flex_layout">
						<view class="ornament"><view></view></view>
						<text>合作伙伴</text>
					</view>
				</view>
				<view class="partner_img flex_layout">
					<view class="partner_img_item" v-for="(item,index) in companyData.partner" :key="index">
					  <image :key="index" :src="cdnUrl+item" mode="widthFix"></image>
					</view>
				</view>
			</view>
						
			<view class="map">
				<view class="title flex_layout">
					<view class="left_title flex_layout">
						<view class="ornament"><view></view></view>
						<text>联系我们</text>
					</view>
				</view>
				<view class="map_content">
					<map style="width: 100%; height: 400rpx;" :latitude="latitude" :longitude="longitude" :markers="covers"></map>
				</view>
			</view>
			
		</view>
		
	</view>
</template>

<script>
	var _this;
	import userInfo from '@/components/user-info/user-info.vue'
	import {cdnUrl} from '@/config/config.js';
	export default {
		components: {
		  userInfo
		},
		data() {
			return {
				color:'',
				userData:'',
				companyData:'',
				cdnUrl:cdnUrl,
				trendsList:'',
				userDataString:'',
				// latitude:31.191658,
				// longitude:121.315304,
				latitude:'',
				longitude:'',
				covers: [{
						latitude: 31.191658,
						longitude: 121.315304,
						iconPath: '../../static/images/location.png',
						title:'冠捷大厦',
						callout:{//自定义标记点上方的气泡窗口 点击有效  
							content:'冠捷大厦',//文本
							color:'#ffffff',//文字颜色
							fontSize:14,//文本大小
							borderRadius:2,//边框圆角
							bgColor:'#0084bf',//背景颜色
							display:'ALWAYS',//常显
							padding:'5'
					  },
				}]
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.userDataString=options.transmit;
			this.userData = JSON.parse(options.transmit);
			this.userData.nickname=options.nickname;
			this.staff_id=options.staff_id
			this.mycompanyInfoData();
			this.companyInfoData();
		},
		mounted() {
			_this= this;
		},
		onShow() {
			
		},
		// 加载更多
		onReachBottom: function() {
			
		},
		methods: {
			//预览图片
			previewImg(index){
				var array=this.companyData.picimages.map(item=>{
					return this.cdnUrl+item
				})
				uni.previewImage({
					current:array[index], //预览图片的下标
					urls: array//预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
				})
			},
			//企业动态
			trends(){
				uni.navigateTo({
					url:'trends?transmit='+this.userDataString+'&nickname='+this.userData.nickname
				})
			},
			//视频
			video(){
				uni.navigateTo({
					url:'video?transmit='+this.userDataString+'&nickname='+this.userData.nickname
				})
			},
			mycompanyInfoData(){
				const parm={
					cid:this.userData.company_id
				};
				this.$api.mycompanyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.companyData=data.data;
						  var latlng=data.data.latlng.split(",")
							this.latitude=latlng[0];           //纬度
							this.longitude=latlng[1];          //经度
							this.covers=this.covers.map(item=>{
								return{
									...item,
									latitude:latlng[0],
									longitude:latlng[1],
									callout:{
										content:data.data.shortname+'<br/>'+data.data.address,//文本
										color:'#ffffff',//文字颜色
										fontSize:14,//文本大小
										borderRadius:2,//边框圆角
										bgColor:'#0084bf',//背景颜色
										display:'ALWAYS',//常显
										padding:'5'
									}
								}
							})
						}
					}
				)
			},
			companyInfoData(){
				const parm={
					company_id:this.userData.company_id,
					type:'news',
					page:1,
					limit:5
				};
				this.$api.companyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.trendsList=data.data.Infos;
						}
					})
			},
			//预览图片
			previewImg(index){
				var array=this.companyData.picimages.map(item=>{
					return this.cdnUrl+item
				})
				uni.previewImage({
					current:array[index], //预览图片的下标
					urls: array//预览图片的地址，必须要数组形式，如果不是数组形式就转换成数组形式就可以
				})
			},
			detail(id){
				var type='news';
				var company_id=this.userData.company_id;
				uni.navigateTo({
					url:'detail?company_id='+company_id+'&id='+id+'&type='+type
				})
			},
			playVideo(currentId){
				let _this = this;
				this.videoContent = uni.createVideoContext(currentId,_this).play();
				// 获取视频列表
				let trailer = this.companyData.videofiles;
				trailer.forEach((item, index) =>{	// 获取json对象并遍历, 停止非当前视频
					if (item != null && item != "") {
						let temp = 'video' + index;
						if (temp != currentId) {
							// 暂停其余视频
							uni.createVideoContext(temp,_this).pause(); //暂停视频播放事件
						}
					}
	 
				})
			},
			//视频播放错误时触发
			videoErrorCallback(e){
				console.log(e);
			},
		}
	}	
</script>

<style>
	page{background: #f0eff4;}
	video{width: 100%;}
	.content_us{padding: 0rpx 30rpx 30rpx;}
	.content_img image{display: block; width: 100%; margin: 10rpx 0;}
	.title{justify-content: space-between; padding: 30rpx 0;}
	.left_title text{color: #333; font-size: 32rpx;}
	.ornament{width: 32rpx; height: 32rpx; background: #323231; border-radius: 50%; position: relative; margin-right: 10rpx;}
	.ornament view{width: 10rpx; height: 10rpx; background: #fff; border-radius: 50%; position: absolute; left: 11rpx; top: 11rpx;}
	.information_content{margin-top: 10rpx;}
	.information_item{padding: 30rpx; margin-bottom: 30rpx; background: #fff; border-radius: 10px; box-shadow: 0 0 10px #ccc;}
	.information_item:last-child{margin-bottom: 10rpx;}
	.left_information{width: 440rpx; padding-right: 30rpx;}
	.information_title{color: #333; font-size: 30rpx;}
	.data_label{margin-top: 30rpx; color: #999; font-size: 24rpx;}
	.data_label view{background: #f4f4f4; line-height: 1; padding: 5rpx 10rpx; margin-left: 30rpx;}
	.right_information image{width: 190rpx; height: 150rpx; display: block; border-radius: 5px;}
	.video_content{margin-top: 10rpx;}
	.video_item{margin-bottom: 30rpx;}
	.video_item:last-child{margin-bottom: 10rpx;}
	.video_text{background: #fff; padding: 30rpx; font-size: 32rpx; color: #333; margin-top: -3rpx; font-weight: bolder; border-radius: 0 0 10px 10px;}
	.partner_img{margin-top: 10rpx; justify-content: space-between;}
	.partner_img_item{background: #fff; width: 48%; height: 150rpx; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 15rpx; margin: 10rpx 0;}
	.partner_img image{display: block; width: 100%;}
	.manage_item{margin-bottom: 30rpx;}
	.manage_item:last-child{margin: 0;}
	.manage_item image{display: block; width: 100%;}
	.map_content image{width: 48rpx; height: 48rpx; display: block;}
	.right_title{color: #999; font-size: 24rpx; display: flex; align-items: center;}
	.right_title image{display: block; width: 12rpx; height: 22rpx; margin-left: 15rpx;}
	swiper{height: 450rpx;}
	.swiper-item image{display: block; width: 100%; height: 450rpx;}
	.c-content{padding:20rpx 0; line-height:1.3}
</style>
