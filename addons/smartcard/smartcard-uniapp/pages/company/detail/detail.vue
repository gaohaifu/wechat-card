<template>
	<view class="content">
		<view class="swiper">
			<swiper :indicator-dots="true" :autoplay="true" :interval="3000" :duration="1000">
				<swiper-item v-for="(item,index) in detailData.picimages" :key="index">
					<view class="swiper-item" @click="previewImg(item)"><image mode="aspectFill" :src="cdnUrl+item"></image></view>
				</swiper-item>
			</swiper>
		</view>
		<view class="detail_title">
			<view class="title">{{detailData.name}}</view>
			<view class="shortname_date flex_layout">
				<view :style="{color:color}">{{companyInfo.shortname}}</view>
				<text>{{detailData.updatetime}}</text>
			</view>
		</view>
		<view class="mainContent">
			<rich-text :nodes="detailData.maincontent"></rich-text>
		</view>
	</view>
</template>

<script>
	var _this;
	var page=1,reachbottom = false;
	import {cdnUrl} from '@/config/config.js';
	export default {
		data() {
			return {
				cdnUrl:cdnUrl,
				detailData:'',
				companyInfo:'',
				color:''
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.departInfo(options.company_id,options.id,options.type);
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
			previewImg(url){
				let urlArray=this.detailData.picimages.map(item=>{
					return this.cdnUrl+item
				});
				uni.previewImage({
					current:this.cdnUrl+url,
					urls: urlArray
				});
			},
			departInfo(company_id,id,type){
				const parm={
					company_id:company_id,
					id:id,
					type:type
				};
				this.$api.departInfo(
					 parm,
					data => {
						if(data.code==1){ 
							this.detailData=data.data.Infos;
							if(data.data.Infos.title){
								this.detailData.name=data.data.Infos.title;
							}
							this.companyInfo=data.data.companyInfo;
							this.detailData.maincontent= this.detailData.maincontent.replace(/\<img/g, "<img style='width: 100%; padding:10px 0;box-sizing:border-box'");
						}
					})
			},
		}
	}	
</script>

<style>
	page{background: #fff;}
	.detail_title{padding: 30rpx;}
	.title{color: #333; font-size: 36rpx; line-height: 1.8;}
	.shortname_date{margin-top: 20rpx;}
	.shortname_date view{color: #0083bf; font-size: 24rpx;}
	.shortname_date text{color: #999; font-size: 24rpx; margin-left: 20rpx; display: block;}
	.mainContent{padding: 30rpx; background: #fff;}
	swiper{height: 450rpx;}
	.swiper-item image{display: block; width: 100%; height: 450rpx;}
</style>
