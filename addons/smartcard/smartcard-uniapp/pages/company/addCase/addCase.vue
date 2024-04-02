<template>
	<view>
		<view class="padding_content">
      <view class="Inside_fast Inside_fast_padding">
				<view class="infoItem flex_layout">
					<view class="left_title">案例名称</view>
					<view class="right_info flex_layout">
						<input type="text" v-model="title" placeholder="请填写案例名称" value="" />
					</view>
				</view>
				<view class="infoItem infoItem_textarea">
					<view class="left_title">案例详情</view>
					<view class="right_info flex_layout">
						<textarea v-model="maincontent" placeholder="请输入案例详情" />
					</view>
				</view>
				<view class="infoItem">
					<view class="left_title">案例图片</view>
					<view class="right_icon flex_layout">
						<view class="chatIcon">
							<uploadImg ref='gUpload' type='header' :mode="picimages" :maxCount="maxCount" @chooseFile='chooseFileTest'  @imgDelete="deleteFileTest"></uploadImg>
						</view>
					</view>
				</view>
			</view>
		</view>		
		<!--提交按钮-->
		<block v-if="status==1">
			<view class="sunmit_btn" @click="addSubmit">
				<button :style="{background:color}">新增案例</button>
			</view>
		</block>
		<block v-if="status==2">
			<view class="sunmit_btn" @click="editSubmit">
				<button :style="{background:color}">编辑案例</button>
			</view>
		</block>
	</view>
</template>

<script>
	import uploadImg from "@/components/uploadImg/uploadImg.vue"
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
				maxCount:5,      //最大上传数量
				color:''
			}
		},
		onShow() {
			
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			if(options.id){
				this.id=options.id;
				this.findCases();
			}
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor:'#ffffff',
				backgroundColor: this.color
			})
			this.type=options.type
			this.company_id=options.company_id;
			this.status=options.status;
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
			radioChange(e){
				this.gender=e.detail.value;
			},
			bindDateChange(e){
				this.birthday=e.detail.value
			},
			findCases(){
				this.$api.findCases(
				{id:this.id},
				data => {
					if(data.code){
						this.title=data.data.title;
						this.maincontent=data.data.maincontent;
						this.picimages=data.data.picimages.split(',');
					}
				})
			},
			//新增商品
			addSubmit(){
				var data={};
				var picimages=this.picimages.toString();
				data['company_id']=this.company_id;
				data['type']=this.type;
				data['title']=this.title      //商品名称
				data['maincontent']=this.maincontent       //详情
				data['picimages']=picimages     //商品图
				if(this.title==''){
					uni.showToast({
						title:'请填写案例名称',
						icon:'none'
					})
					return false;
				}	
				if(this.maincontent==''){
					uni.showToast({
						title:'请填写案例详情',
						icon:'none'
					})
					return false;
				}	
				if(this.picimages.length==0){
					uni.showToast({
						title:'请上传案例图',
						icon:'none'
					})
					return false;
				}
				this.$api.addCases(
				data,
				data => {
					if(data.code){
						uni.navigateBack({})
					} 
				})
			},
			//编辑商品
			editSubmit(){
				var data={};
				var picimages=this.picimages.toString();
				data['id']=this.id                //商品id
				data['company_id']=this.company_id;
				data['type']=this.type;
				data['title']=this.title      //商品名称
				data['maincontent']=this.maincontent       //详情
				data['picimages']=picimages     //商品图
				if(this.title==''){
					uni.showToast({
						title:'请填写案例名称',
						icon:'none'
					})
					return false;
				}	
				if(this.maincontent==''){
					uni.showToast({
						title:'请填写案例详情',
						icon:'none'
					})
					return false;
				}	
				if(this.picimages.length==0){
					uni.showToast({
						title:'请上传案例图',
						icon:'none'
					})
					return false;
				}
				this.$api.editCases(
				data,
				data => {
					if(data.code){
						uni.navigateBack({})
					} 
				})
			}
		}
	}
</script>

<style>
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

