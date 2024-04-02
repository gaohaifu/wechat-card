<template>
	<view class="content">
		<view class="common_header">
			<userInfo :userData="userData" :color="color"></userInfo>
		</view>
	   <block v-if="handbookData.length>0">
		<view class="brochure">
			<jing-swiper :brochureHeight="brochureHeight" :handbookData="handbookData" :color="color"></jing-swiper>
		</view>
		</block>
		<block v-else>
			<noData v-if="reveal"></noData>
		</block>
	</view>
</template>

<script>
	var _this;
	import userInfo from '@/components/user-info/user-info.vue'
	import jingSwiper from '@/components/jing-swiper/jing-swiper.vue'
	import noData from '@/components/no-data/no-data.vue';
	export default {
		components: {
		  userInfo,
			jingSwiper,
			noData
		},
		data() {
			return {
				windowHeight:'',
				brochureHeight:'',
				staff_id:0,
				userData:'',
				handbookData:'',
				color:'',
				reveal:false,
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
			uni.getSystemInfo({
			    success: (res) => {
			       var windowHeight=res.windowHeight;
						 this.brochureHeight=windowHeight-115+'px'
			    }
			});
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
			companyInfoData(){
				this.reveal=false
				const parm={
					company_id:this.userData.company_id,
					type:'/pages/company/design',
					page:1,
					limit:10
				};
				this.$api.companyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.reveal=true
							this.handbookData=data.data.Infos
						}
					})
			},
		}
	}	
</script>

<style>
	page{background: #f2f6f7;}
	.common_header{height: 100px;}
</style>
