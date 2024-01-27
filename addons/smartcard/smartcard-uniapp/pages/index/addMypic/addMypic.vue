<template>
	<view>
		<view  class="content">
      <view class="Inside_fast Inside_fast_padding">

				<view class="infoItem">
					<view class="left_title">我的图片</view>
					<view class="right_icon flex_layout">
						<view class="chatIcon">
							<uploadImg ref='gUpload' type='header' :mode="picimages" :maxCount="maxCount" @chooseFile='chooseFileTest' @imgDelete="deleteFileTest"></uploadImg>
						</view>
					</view>
				</view>
			</view>
		</view>		
		<!--提交按钮-->
		<block v-if="status==1">
			<view class="sunmit_btn" @click="addSubmit">
				<button :style="{background:color}">保存我的相册</button>
			</view>
		</block>
	</view>
</template>

<script>
	import uploadImg from "../../../components/uploadImg/uploadImg.vue"
	export default {
		components:{
			uploadImg
		},
		data() {
			return {
				picimages:[],
				title:'',
				maincontent:'',
				id:'',
				type:'',
				status:1,
				maxCount:30,      //最大上传数量
				color:'',
				staff_id:0,
				company_id:0,
				params:''
			}
		},
		onShow() {
			
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			this.params=options.transmit;
			this.staff_id=options.staff_id;
			this.staffInfoData();
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
		},
		methods: {
			chooseFileTest(list){
				console.log(list);
				this.picimages=list
			},
			//删除
			deleteFileTest(list,index){
				this.picimages=list
				this.maxCount=this.maxCount-list.length;
			},
			//个人信息接口
			staffInfoData(){
				this.reveal=false
				const parm={
					staff_id:this.staff_id
				};
				this.$api.staffInfoData(
					 parm,
					data => {
						if(data.code==1){
							console.log(data)
							this.reveal=true
							this.picimages=data.data.picimages
							this.company_id=data.data.company_id
							console.log(this.picimages)
						}
					})
			},
			//新增我的照片
			addSubmit(){
				var _this=this;
				var data={};
				var picimages=this.picimages.toString();
				data['company_id']=this.company_id;
				if(data['company_id']==0){
					uni.showToast({
						title:'公司id不能为空',
						icon:'none'
					})
					return false;
				}
				data['picimages']=picimages     //商品图
				
				if(this.picimages.length==0){
					uni.showToast({
						title:'请上传照片',
						icon:'none'
					})
					return false;
				}
				this.$api.staffEdit(
				data,
				data => {
					if(data.code){
						uni.showToast({
							title:data.msg,
							icon:'success'
						})
						setTimeout(function(){
							_this.$common.navigateTo('/pages/index/img?transmit='+_this.params+'&staff_id='+_this.staff_id)
						},500)
						
					} 
				})
			}
		}
	}
</script>

<style>
	@import url('../../../common/common.css');
	page{background: #fff;}
	.right_icon{width: 100%; margin-top: 30rpx;}
	.chatIcon image{width: 85rpx; height: 85rpx; border-radius: 50%; display: block;}
	.rightIcon{margin-left: 30rpx;}
	.infoItem{justify-content: space-between; padding: 30rpx; border-bottom: 1px solid #f8f8f8;}
	.infoItem:last-child{border: none;}
	.rightIcon image{width: 30rpx; height: 30rpx; display: block;}
	.right_info input{width: 300rpx; height: 50rpx; color: #333; font-size: 28rpx; text-align: right;}
	.right_info image{width: 30rpx; height: 30rpx; display: block; margin-left: 20rpx;}
	.infoItem_textarea .right_info{margin-top: 30rpx;}
	.infoItem_textarea .right_info textarea{width: 100%; min-height: 200rpx; color: #333; font-size: 28rpx; border-radius: 10px; background: #F8F8F8; padding: 30rpx;}
	
	.select_radio{width: 200rpx;}
	.inspect_name{font-size: 28rpx; color: #333; width: 150rpx;}
	
	.sunmit_btn{padding: 40rpx;}
	.sunmit_btn button{color: #fff; background: #0084bf; font-size: 36rpx; height: 74rpx; line-height: 74rpx; width: 360rpx; border-radius: 74rpx; margin: 0 auto; padding: 0; display: block;}
	.sunmit_btn button::after{display: none;}
</style>

