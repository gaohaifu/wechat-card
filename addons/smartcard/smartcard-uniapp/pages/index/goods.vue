<template>
	<view class="content">
		<view class="search_input flex_layout">
			<input type="text" @input="searchChange" value="" placeholder="输入名称搜索" />
			<button :style="{background:color}" @click="searchBtn">搜索</button>
		</view>
		<!-- <view class="swiper_img">
			<swiper :indicator-dots="true" indicator-color="#ccc" indicator-active-color="#888" :duration="1000">
				<swiper-item>
					<view class="swiper-item"><image src="../../static/images/swiper.jpg" mode="aspectFill"></image></view>
				</swiper-item>
				<swiper-item>
					<view class="swiper-item"><image src="../../static/images/swiper.jpg" mode="aspectFill"></image></view>
				</swiper-item>
			</swiper>
		</view> -->
		<view class="newTitle flex_layout">
			<view class="newTitle_item newTitle_active" :style="{color:color,'border-color':color}">推荐商品</view>
		</view>
		<view class="goods_content">
			<block v-if="goodsData.length>0">
				<view class="goods_list flex_layout">
					<view class="goods_item" v-for="(item,index) in goodsData" :key="index" @click="detail(item.id)">
						<image :src="cdnUrl+item.picimages[0]" mode="aspectFill"></image>
						<view>{{item.name}}</view>
						<view class="edit_button" @click.stop="edit(item.id)" v-if="usertype==1">
							<button :style="{background:color}">编辑信息</button>
						</view>
					</view>
				</view>	
			</block>
			<block v-else>
				<noData v-if="reveal"></noData>
			</block>
		</view>
		<block v-if="usertype==1">
			<view style="height: 100rpx;"></view>
			<view class="add_btn" @click="addBtn"><button :style="{background:color}">+ 新增商品</button></view>
		</block>
	</view>
</template>

<script>
	var _this;
	var page=1,reachbottom = false;
	import {cdnUrl} from '../../config/config.js';
	import noData from '@/components/no-data/no-data.vue';
	export default {
		components: {
		  noData
		},
		data() {
			return {
				color:'',
				keywods:'',
				staff_id:0,
				goodsData:[],
				cdnUrl:cdnUrl,
				userData:'',
				reveal:false,
				usertype:''
			}
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.userData = JSON.parse(options.transmit);
			this.userData.nickname=options.nickname;
			this.staff_id=options.staff_id
			this.usertype=options.usertype
		},
		mounted() {
			_this= this;
		},
		onShow() {
			page=1;
			reachbottom = false;
			this.goodsData=[]
			this.companyInfoData();
		},
		// 加载更多
		onReachBottom: function() {
			if (!reachbottom) {
				this.companyInfoData();
			}
		},
		methods: {
			addBtn(){
				var type='goods';
				uni.navigateTo({
					url:'addGoods?status=1'+'&company_id='+this.userData.company_id+'&type='+type
				})
			},
			edit(id){
				var type='goods';
				uni.navigateTo({
					url:'addGoods?id='+id+'&status=2'+'&company_id='+this.userData.company_id+'&type='+type
				})
			},
			companyInfoData(){
				this.reveal=false;
				const parm={
					company_id:this.userData.company_id,
					type:'goods',
					keywords:this.keywods,
					page:page,
					limit:5
				};
				this.$api.companyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.reveal=true;
							var dataList=data.data.Infos;
							var number=dataList.length;
							console.log(number);
							page++;
							uni.stopPullDownRefresh();
							if (number == 0) {
								reachbottom = true
								uni.showToast({
									title: "已经加载全部",
									icon: "none",
									duration: 3000
								});
							} else {
								this.goodsData = this.goodsData.concat(dataList);
							}
						}	
					})
			},
			searchChange(e){
				this.keywods=e.detail.value;
				
			},
			searchBtn(){
				page=1;
				reachbottom = false;
				this.goodsData=[];
				this.companyInfoData();
			},
			detail(id){
				var type='goods';
				var company_id=this.userData.company_id;
				uni.navigateTo({
					url:'detail?company_id='+company_id+'&id='+id+'&type='+type
				})
			}
		}
	}	
</script>

<style>
	@import url('../../common/common.css');
	page{background: #f2f6f7;}
	.newTitle{padding: 15rpx 30rpx;}
	.newTitle_item{padding: 15rpx 0; color: #333; font-size: 30rpx; border-bottom: 2px solid #f2f6f7; font-weight: bolder;}
	.newTitle_active{color: #0083bf; border-color: #0083bf;}
	.search_input{padding: 15rpx 30rpx; border-bottom: 1px solid #ededed; justify-content: space-between; background: #fff;}
	.search_input input{width: 600rpx; background: #f0f0f0; height: 64rpx; border: none; text-align: center; color: #333; font-size: 28rpx;}
	.search_input button{display: block; color: #333; font-size: 28rpx; height: 64rpx; width: 90rpx; background: #0084bf; color: #fff; font-size: 28rpx; padding: 0; margin: 0; border-radius: 0; line-height: 64rpx;}
	.swiper_img swiper{height: 400rpx;}
	.swiper-item image{width: 100%; height: 400rpx; display: block;}
	.goods_content{padding: 30rpx;}
	.allTitle{color: #333; font-size: 36rpx;}
	.goods_list{justify-content: space-between;}
	.goods_item{width:-webkit-calc(50% - 10rpx); width:-moz-calc(50% - 10rpx); width:calc(50% - 10rpx); text-align: center; margin-bottom: 30rpx; background: #fff; padding-bottom: 30rpx;}
	.goods_item image{width: 100%; height: 335rpx; display: block;}
	.goods_item view{color: #333; font-size: 28rpx; margin-top: 20rpx; padding: 0 20rpx; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
	.goods_item text{color: #999; font-size: 24rpx; display: block; margin-top: 10rpx;}
	
	.noData_img image{width: 360rpx; height: 360rpx; display: block; margin: 150rpx auto 0;}
	.none_thread{width: 440rpx; margin: 50rpx auto 0;}
	.thread_item{width: 140rpx; height: 1px; background: #cccccc;}
	.text_item{width: 160rpx; color: #999; font-size: 24rpx; justify-content: space-between;}
	.text_item text{display: block; width: 10rpx; height: 10rpx; border-radius: 10rpx; background: #ccc;}
	.add_btn{position: fixed; left: 0; bottom: 0; z-index: 100; height: 100rpx; width: 100%;}
	.add_btn button{width: 100%; height: 100rpx; background: #0084bf; color: #fff; font-size: 30rpx; border-radius: 0; line-height: 100rpx; margin: 0; padding: 0;}
	.edit_button button{width: 150rpx; height: 50rpx; background: #0084bf; color: #fff; font-size: 24rpx; border-radius: 0; line-height: 50rpx; margin: 0; padding: 0; display: block; margin: 30rpx auto 0;}
</style>
