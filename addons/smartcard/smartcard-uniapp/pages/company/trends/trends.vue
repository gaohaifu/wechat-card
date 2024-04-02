<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<view class="newTitle flex_layout">
			<view :style="{color:color,'border-color':color}" class="newTitle_item newTitle_active">最新</view>
		</view>
		<view class="trends_content">
			<block v-if="trendsList.length>0">
				<view class="trends_item flex_layout" v-for="(item,index) in trendsList" :key="index" @click="detail(item.id)">
					<view class="img_content">
						<image :src="cdnUrl+item.picimages[0]" mode="aspectFill"></image>
					</view>
					<view class="text_content">
						<view class="image_title">{{item.title}}</view>
						<view class="maincontent">{{item.intro}}</view>
						<view class="thumb_up flex_layout">
							<button open-type="share" :id="item.id" :data-title="item.title" :data-imgUrl="item.picimages[0]" class="right_thumb flex_layout"><view class="iconView iconfont icon-fenxiang"></view><text>分享</text></button>
							<view class="right_thumb flex_layout">
								<block v-if="item.isfavor">
									<view :style="{color:color}" @click.stop="helpBtn(index,item.id,item.isfavor)" class="iconView iconfont icon-thumbup-fill"></view>
								</block>
								<block v-else>
									<view @click.stop="helpBtn(index,item.id,item.isfavor)" class="iconView iconfont icon-dianzan1"></view>
								</block>
								<text :style="{color:item.isfavor?color:''}">{{item.favorNum}}</text>
							</view>
							<view class="edit_button" @click.stop="edit(item.id)" v-if="usertype==1">
								<button :style="{borderColor:color,color:color}">编辑信息</button>
							</view>
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
			<view class="add_btn" @click="addBtn"><button :style="{background:color}">+ 新增企业动态</button></view>
		</block>
	</view>
</template>

<script>
	var _this;
	var page=1,reachbottom = false;
	import {cdnUrl} from '@/config/config.js';
	import userInfo from '@/components/user-info/user-info.vue'
	import noData from '@/components/no-data/no-data.vue';
	export default {
		components: {
		  userInfo,
			noData
		},
		data() {
			return {
				cdnUrl:cdnUrl,
				trendsList:[],
				staff_id:1,
				userData:'',
				repeatTab:true,
				reveal:false,
				usertype:'',
				color:'',
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
			this.usertype=options.usertype
		},
		onShareAppMessage(res) {
			var type='news';
			var company_id=this.userData.company_id;
			var id=res.target.id;
			var title=res.target.dataset.title;
			var imgUrl=cdnUrl+res.target.dataset.imgurl;
			if (res.from === 'button') {// 来自页面内分享按钮
				
			}else{ //右上角按钮分享
				return false;
			}
			return {
					title: title,
					path: '/pages/company/detail?company_id='+company_id+'&id='+id+'&type='+type,
					imageUrl:imgUrl
				}
		},
		mounted() {
			_this= this;
		},
		onShow() {
			page=1;
			reachbottom = false;
			this.trendsList=[];
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
					url:'/pages/company/addTrends?status=1'+'&company_id='+this.userData.company_id+'&type=cases'
				})
			},
			edit(id){
				uni.navigateTo({
					url:'/pages/company/addTrends?id='+id+'&status=2'+'&company_id='+this.userData.company_id+'&type=cases'
				})
			},
			companyInfoData(){
				this.reveal=false;
				const parm={
					company_id:this.userData.company_id,
					type:'news',
					page:page,
					limit:10
				};
				this.$api.companyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.reveal=true;
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
								this.trendsList = this.trendsList.concat(dataList);
							}
						}
					})
			},
			//视频播放错误是触发
			videoErrorCallback(e){
				console.log(e);
			},
			helpBtn(index,id,isfavor){
				if(!this.repeatTab){
					return false;
				}
				this.repeatTab=false
				const parm={
					staff_id:1,
					typedata:8,
					company_id:this.userData.company_id,
					news_id:id
				};
				this.$api.visitorsOptionData(
					 parm,
					data => {
						if(data.code==1){
							this.repeatTab=true
							this.trendsList[index].favorNum=data.data.favorNum;
							if(isfavor==0){
								this.trendsList[index].isfavor=1
							}else{
								this.trendsList[index].isfavor=0
							}
						}
					})
			},
			detail(id){
				var type='news';
				var company_id=this.userData.company_id;
				uni.navigateTo({
					url:'/pages/company/detail?company_id='+company_id+'&id='+id+'&type='+type
				})
			}
		}
	}	
</script>

<style>
	page{background: #f2f6f7;}
	.newTitle{padding: 15rpx 30rpx;}
	.newTitle_item{padding: 15rpx 0; color: #333; font-size: 30rpx; border-bottom: 2px solid #f2f6f7; font-weight: bolder;}
	.newTitle_active{color: #0083bf; border-color: #0083bf;}
	.trends_content{justify-content: space-between; padding: 0 30rpx;}
	.trends_item{padding: 0; width: 100%; margin-bottom: 20rpx; background: #fff;}
	.userInfo image{width: 90rpx; height: 90rpx; display: block; border-radius: 50%;}
	.right_text{padding-left: 30rpx;}
	.right_text view{color: #333; font-size: 30rpx;}
	.right_text text{color: #999; font-size: 24rpx; margin-top: 10rpx; display: block;}
	.video_content{margin-top: 30rpx;}
	.video_title{color: #333; font-size: 28rpx; line-height: 1.5;}
	.video_content video{display: block; width: 100%; height: 390rpx; margin-top: 30rpx;}
	.img_content{width: 200rpx; height: 200rpx;}
	.img_content image{width: 200rpx; height: 200rpx; display: block;}
	.text_content{padding: 30rpx 0 30rpx 30rpx; width: 460rpx;}
	.image_title{color: #333; font-size: 30rpx;}
  .thumb_up{margin-top: 20rpx; justify-content: space-between;}
	.right_thumb{background: none; border: none; margin: 0; padding: 0; width: auto; height: auto; line-height: 1;}
	.right_thumb::after{display: none;}
	.right_thumb text{color: #333; font-size: 28rpx; display: block; margin-left: 10rpx;}
  .iconView{color: #333; font-size: 36rpx; margin-left: 10rpx;}
	.operate_flex{justify-content: space-between; padding-top: 30rpx;}
	.point_out{font-size: 24rpx; color: #999;}
	.left_img image{width: 70rpx; height: 70rpx; display: block;}
	.comment_on{justify-content: space-between; margin-top: 30rpx;}
	.right_input{width: 600rpx;}
	.right_input input{width: 100%; height: 56rpx; border-bottom: 1px solid #ececec; font-size: 24rpx; color: #333;}
	.add_btn{position: fixed; left: 0; bottom: 0; z-index: 100; height: 100rpx; width: 100%;}
	.add_btn button{width: 100%; height: 100rpx; background: #0084bf; color: #fff; font-size: 30rpx; border-radius: 0; line-height: 100rpx; margin: 0; padding: 0;}
	.edit_button button{width: 150rpx; height: 50rpx; background: #fff; border: 1px solid #474747; color: #474747; font-size: 24rpx; border-radius: 5px; line-height: 50rpx; margin: 0; padding: 0; display: block;} 
	.edit_button button::after{display: none;}
	.maincontent{color: #666; font-size: 24rpx; margin-top: 15rpx; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;}
</style>
