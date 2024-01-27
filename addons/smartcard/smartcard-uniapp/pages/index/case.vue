<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<view class="cast_content">
			<block v-if="caseData.length>0">
				<view class="cast_item" v-for="(item,index) in caseData" :key="index" @click="detail(item.id)">
					<image :src="cdnUrl+item.picimages[0]" mode="aspectFill"></image>
					<view class="cast_text">{{item.title}}</view>
					<view class="transverse_line"></view>
					<!-- <view class="cast_num flex_layout">
						<view class="castNum_item"><text>G7</text>上传</view>
						<view class="drop"></view>
						<view class="castNum_item">分享<text>103</text></view>
						<view class="drop"></view>
						<view class="castNum_item">浏览<text>163</text></view>
					</view> -->
					<view class="edit_button" @click.stop="edit(item.id)" v-if="usertype==1">
						<button :style="{background:color}">编辑信息</button>
					</view>
				</view>
			</block>
			<block v-else>
				<noData v-if="reveal"></noData>
			</block>
		</view>
		<block v-if="usertype==1">
			<view style="height: 100rpx;"></view>
			<view class="add_btn" @click="addBtn"><button :style="{background:color}">+ 新增成功案例</button></view>
		</block>
	</view>
</template>

<script>
	var _this;
	var page=1,reachbottom = false;
	import {cdnUrl} from '../../config/config.js';
	import userInfo from '@/components/user-info/user-info.vue'
	import noData from '@/components/no-data/no-data.vue';
	export default {
		components: {
		  userInfo,
			noData
		},
		data() {
			return {
				staff_id:0,
				userData:'',
				caseData:[],
				cdnUrl:cdnUrl,
				reveal:false,
				usertype:'',
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
			this.caseData=[];
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
				uni.navigateTo({
					url:'addCase?status=1'+'&company_id='+this.userData.company_id+'&type=cases'
				})
			},
			edit(id){
				uni.navigateTo({
					url:'addCase?id='+id+'&status=2'+'&company_id='+this.userData.company_id+'&type=cases'
				})
			},
			companyInfoData(){
				this.reveal=false
				const parm={
					company_id:this.userData.company_id,
					type:'cases',
					page:page,
					limit:10
				};
				this.$api.companyInfoData(
					parm,
					data => {
						if(data.code==1){
							this.reveal=true
							var dataList=data.data.Infos;
							var number=dataList.length;
							page++;
							uni.stopPullDownRefresh();
							if (number == 0) {
								reachbottom = true
								uni.showToast({
									"title": "已经加载全部",
									icon: "none",
									duration: 1000
								});
							} else {
								this.caseData = this.caseData.concat(dataList);
							}
						}
						
					})
			},
			detail(id){
				var type='cases';
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
	.cast_content{padding: 15rpx 30rpx;}
	.cast_item{padding: 30rpx; background: #fff; border-radius: 10px; margin-bottom: 30rpx;}
	.cast_item image{width: 300rpx; height: 200rpx; border-radius: 10px; display: block; margin: 0 auto;}
	.cast_text{font-size: 30rpx; color: #333; text-align: center; margin-top: 30rpx;}
	.transverse_line{height: 1px; width: 100rpx; margin: 40rpx auto 0; background: #d4d4d4;}
	.cast_num{justify-content: center; margin-top: 30px;}
	.drop{background: #888; width: 7px; height: 7px; border-radius: 50%; margin: 0 15px;}
	.castNum_item{color: #999; font-size: 24rpx;}
	.castNum_item text{color: #333; font-size: 24rpx;}
	.add_btn{position: fixed; left: 0; bottom: 0; z-index: 100; height: 100rpx; width: 100%;}
	.add_btn button{width: 100%; height: 100rpx; background: #0084bf; color: #fff; font-size: 30rpx; border-radius: 0; line-height: 100rpx; margin: 0; padding: 0;}
	.edit_button button{width: 150rpx; height: 50rpx; background: #0084bf; color: #fff; font-size: 24rpx; border-radius: 0; line-height: 50rpx; margin: 0; padding: 0; display: block; margin: 30rpx auto 0;}
</style>
