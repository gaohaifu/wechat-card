<template>
	<view>
		<view class="padding_content">
			<view class="Inside_fast">
				<view class="infoItem flex_layout">
					<view class="left_title">企业名称<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="text" v-model="name" placeholder="请填写企业名称" value="" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业网址</view>
					<view class="right_info flex_layout">
						<input type="text" v-model="url" placeholder="请填写企业网址" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业简称<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="text" v-model="shortname" placeholder="请填写企业简称" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">联系电话<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="number" v-model="phone" placeholder="请填写联系电话" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业简介</view>
					<view class="right_info flex_layout">
						<input type="text" v-model="intro" placeholder="请填写企业简介" value="" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业详细介绍</view>
					<view class="right_info flex_layout">
						<textarea v-model="content" placeholder="请填写企业详细介绍" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">联系地址<text style="color:#ff0000">*</text></view>
					<!--  #ifdef  MP-WEIXIN	 -->
					<view class="right_info flex_layout" @click="addressClick">
						<input type="text" v-model="address" placeholder="请选择地址" :disabled="true" />
						<image src="../../static/images/right.png" mode=""></image>
					</view>
					<!--  #endif -->
					<!--  #ifndef  MP-WEIXIN	 -->
					<view class="right_info flex_layout">
						<input type="text" v-model="address" placeholder="请填写具体地址" value='' />
						<image src="../../static/images/right.png" mode=""></image>
					</view>
					<!--  #endif -->
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业营业起始时间<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<picker mode="date" :value="begintime" :end="endtime" @change="startDate">
							<view :style="{color:begintime=='请选择企业营业起始时间'?'#888':'#333'}">{{begintime}}</view>
						</picker>
						<view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业营业结束时间<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<picker mode="date" :value="endtime" :start="begintime" @change="endDate">
							<view :style="{color:endtime=='请选择企业营业结束时间'?'#888':'#333'}">{{endtime}}</view>
						</picker>
						<view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业营业执照<text style="color:#ff0000">*</text></view>
					<view class="right_icon flex_layout">
						<view class="chatIcon">
							<uploadImg type="avatar" ref='gUpload' :mode="imgList" @chooseFile='chooseFileTest'></uploadImg>
						</view>
						<!-- <view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view> -->
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">企业营业执照号<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="text" v-model="licensenumber" placeholder="请填写企业营业执照号" value="" />
					</view>
				</view>
			</view>
			
			<view class="Inside_fast Inside_fast_padding">
				<view class="left_title" style="width: 100%;">企业照片集</view>
				<view class="right_icon flex_layout margin-top">
					<view class="chatIcon">
						<uploadImg ref='gUpload' type="header" :mode="pictureList" :maxCount="maxCount" @chooseFile='pictureTest'></uploadImg>
					</view>
					<!-- <view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view> -->
				</view>
			</view>
			<view class="Inside_fast Inside_fast_padding">
				<view class="left_title" style="width: 100%;">企业视频集</view>
				<view class="right_icon flex_layout margin-top">
					<block v-if="videoList.length>0">
						<view class="video_list" v-for="(item,index) in videoList" :key="index">
							<video :src="item"></video>
						</view>
					</block>
					<block v-if="videoList.length<5">
						<view class="addVideo" @click="addVideo">
							<image src="../../static/images/nodata1.png" mode=""></image>
						</view>
					</block>
					<!-- <view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view> -->
				</view>
			</view>
		</view>		
		<!--提交按钮-->
		<view class="sunmit_btn" @click="submit">
			<button :style="{background:color}">提交</button>
		</view>
	</view>
</template>

<script>
	import uploadImg from "../../components/uploadImg/uploadImg.vue"
	import {cdnUrl,baseApiUrl} from '../../config/config.js';
	let page = 1,reachbottom=false;
	export default {
		components:{
			uploadImg
		},
		data() {
			return {
				cdnUrl:cdnUrl,
				userInfo:'',
				videoList:[],
				imgList:[],
				pictureList:[],
				begintime:'请选择企业营业起始时间',
				endtime:'请选择企业营业结束时间',
				videofiles:[],
				name:'',
				url:'',
				shortname:'',
				phone:'',
				intro:'',
				content:'',
				licensenumber:'',
				address:'',
				color:'',
				maxCount:5,
			}
		},
		onShow() {
			
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
			  frontColor:'#ffffff',
			  backgroundColor: this.color
		  })
			this.user_id=options.user_id;
			this.company_id=options.company_id;
		},
		methods: {
			//选择开始时间
			startDate(e){
				this.begintime=e.detail.value
			},
			//选择结束时间
			endDate(e){
				this.endtime=e.detail.value
			},
			//选择地址
			addressClick(){
				uni.chooseLocation({
					success: (res)=> {
						this.address=res.address
					}
				});
			},
			//上传营业执照
			chooseFileTest(list,ulist){
				this.imgList=ulist
				this.avatar=list[0]
			},
			//上传企业照片集
			pictureTest(list,ulist){
				this.pictureList=list
			},
			//上传视频
			addVideo(){
				let userToken = '';
				let auth = this.$db.get("auth");
				userToken = auth.token;
				uni.chooseVideo({
					sourceType: ['camera', 'album'],
					success: (res)=> {
						let tempFilePaths=res.tempFilePath;
						uni.uploadFile({
							url: baseApiUrl + 'common/upload?token='+userToken,
							filePath: tempFilePaths,
							fileType: 'video',
							name: 'file',
							headers: {
								'Accept': 'application/json',
								'Content-Type': 'multipart/form-data',
								'token': userToken
							},
							formData: {},
							success: (uploadFileRes) => {
								var datavideo=JSON.parse(uploadFileRes.data);
								this.videoList.push(datavideo.data.fullurl)
								this.videofiles.push(datavideo.data.url)
							},
							fail: (error) => {
								if (error && error.response) {
									this.$common.showError(error.response);
								}
							},
							complete: () => {
								setTimeout(function () {
									uni.hideLoading();
								}, 250);
							},
						});
							//
						
					}
				});
			},
			
			submit(){
				var data={};
				var that=this;
				data['name']=this.name      //真实姓名
				data['url']=this.url      //企业网址
				data['shortname']=this.shortname  //企业简称
				data['phone']=this.phone     //手机号
				data['intro']=this.intro     //企业简介
				data['content']=this.content  //企业详细介绍
				data['address']=this.address     //地址
				data['licenseimage']=this.avatar     //营业执照
				data['begintime']=this.begintime     //营业执照开始时间
				data['endtime']=this.endtime     //营业执照结束时间
				data['licensenumber']=this.licensenumber     //营业执照编号
				data['picimages']=this.pictureList.toString()     //照片集
				data['videofiles']=this.videofiles.toString()     //视频集
				if(this.name==''){
					uni.showToast({
						title:'请填写企业名称',
						icon:'none'
					})
					return false;
				}
				if(this.shortname==''){
					uni.showToast({
						title:'请填写企业简称',
						icon:'none'
					})
					return false;
				}
				if(this.phone==''){
					uni.showToast({
						title:'请填写联系电话',
						icon:'none'
					})
					return false;
				}
				if(this.address==''){
					uni.showToast({
						title:'请选择联系地址',
						icon:'none'
					})
					return false;
				}
				if(this.begintime=='请选择企业营业起始时间'){
					uni.showToast({
						title:'请选择企业营业起始时间',
						icon:'none'
					})
					return false;
				}
				if(this.endtime=='请选择企业营业结束时间'){
					uni.showToast({
						title:'请选择企业营业结束时间',
						icon:'none'
					})
					return false;
				}
				if(this.avatar==''){
					uni.showToast({
						title:'请上传营业执照',
						icon:'none'
					})
					return false;
				}
				if(this.licensenumber==''){
					uni.showToast({
						title:'请填写企业营业执照号',
						icon:'none'
					})
					return false;
				}
				// if(!this.$common.testString(this.mobile,'mobile')){
				// 	uni.showToast({
				// 		title:'请正确填写手机号',
				// 		icon:'none'
				// 	})
				// 	return false;
				// }
				this.$api.companyApply(
				data,
				data => {
					if(data.code==1){
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
						setTimeout(()=>{
							uni.navigateTo({
								url:'../index/index'
							})
						},1500)
					}else{
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
					}
				})
			}
		}
	}
</script>

<style>
	page{background: #fff;}
	.padding_content{padding: 30rpx;}
	.Inside_fast{background: #fff; border-radius: 10px; margin-top: 30rpx; border: 1px solid #eaeaea;}
	.Inside_fast_padding{padding: 30rpx; justify-content: space-between;}
	.chatIcon image{width: 85rpx; height: 85rpx; border-radius: 50%; display: block;}
	.rightIcon{margin-left: 30rpx;}
	.infoItem{justify-content: space-between; padding: 40rpx 30rpx; border-bottom: 1px solid #eaeaea;}
	.left_title{width: 120rpx;}
	.right_info{width: 500rpx; display: flex; justify-content: flex-end;}
	.infoItem:last-child{border: none;}
	.rightIcon image{width: 12rpx; height: 22rpx; display: block;}
	.right_info input{width: 468rpx; height: 50rpx; color: #333; font-size: 28rpx; text-align: right;}
	.right_info picker{width: 430rpx; height: 50rpx; color: #333; font-size: 28rpx; text-align: right;}
	.right_info picker view{height: 50rpx; line-height: 50rpx;}
	.right_info image{width: 12rpx; height: 22rpx; display: block; margin-left: 20rpx;}
	.right_info textarea{width: 520rpx; margin:10rpx; padding:15rpx; background:#f7f7f7; border:1px solid #f3f3f3; border-radius:20rpx;font-size: 28rpx;}
	.select_radio{width: 200rpx;}
	.inspect_name{font-size: 28rpx; color: #333; width: 150rpx;}
	
	.sunmit_btn{padding: 40rpx;}
	.sunmit_btn button{color: #fff; background: #0084bf; font-size: 36rpx; height: 74rpx; line-height: 74rpx; width: 360rpx; border-radius: 74rpx; margin: 0 auto; padding: 0; display: block;}
	.sunmit_btn button::after{display: none;}
	
	.companyContent{position: absolute; top: 100rpx; left: 0; width: 100%; z-index: 100; background: #fff; box-shadow: 0 0 10px #ccc;}
	.companyList{padding: 30rpx; color: #333; font-size: 28rpx; border-bottom: 1px solid #F2F2F2;}
	
	.video_list{width: 100%; padding: 15rpx 0;}
	.video_list video{display: block; width: 100%;}
	.addVideo{width: 100%; height: 300rpx; margin-top: 15rpx; display: flex; align-items: center; justify-content: center; background: #f0f0f0; border-radius: 10px;}
	.addVideo image{display: block; width: 120rpx; height: 120rpx;}
</style>
