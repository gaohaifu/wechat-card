<template>
	<view>
		<view class="padding_content">
      <view class="Inside_fast Inside_fast_padding flex_layout">
				<view class="left_title">头像<text style="color:#ff0000">*</text></view>
				<view class="right_icon flex_layout">
					<view class="chatIcon">
						<uploadImg ref='gUpload' :mode="imgList" :maxCount="maxCount" @chooseFile='chooseFileTest'></uploadImg>
					</view>
					<view class="rightIcon"><image src="../../static/images/right.png" mode=""></image></view>
				</view>
			</view>
			<view class="Inside_fast">
				<view class="infoItem flex_layout">
					<view class="left_title">名称<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="text" v-model="name" placeholder="请填写真实姓名" value="" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">手机号<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="number" v-model="mobile" placeholder="请填写手机号" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				<!--选择公司-->
				<view class="infoItem flex_layout">
					<view class="left_title">选择公司<text style="color:#ff0000">*</text></view>
					<view class="right_info" style="position: relative;">
						<input type="text" :value="companyname" @input="companyInput" placeholder="选择公司" />
						<view class="companyContent" v-if="ifCompany">
							<scroll-view class="hospital_body" scroll-y="true" @scrolltolower="bottomHospital">
								<block v-if="companyList.length>0">
									<view class="hospital_item" v-for="(item,index) in companyList"
										@click="selectCompany(item.id,item.name)" :key="index">{{item.name}}</view>
								</block>
								<block v-else>
									<view class="hospital_item">没有找到</view>
									<!-- <view class="manual_input" @click="manualInput">手动输入</view> -->
								</block>
							</scroll-view>
						</view>
					</view>
				</view>
				
				<view class="infoItem flex_layout">
					<view class="left_title">职位<text style="color:#ff0000">*</text></view>
					<view class="right_info flex_layout">
						<input type="text" v-model="position" placeholder="请填写职位" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				<!--  #ifdef  MP-WEIXIN	 -->
					<view class="infoItem flex_layout" v-if="platform_status==2">
						<view class="left_title">微信号</view>
						<view class="right_info flex_layout">
							<input type="text" v-model="wechat" placeholder="请填写微信号" value="" />
						</view>
					</view>
				<!-- #endif -->
				<!--  #ifndef  MP-WEIXIN	 -->
					<view class="infoItem flex_layout">
						<view class="left_title">微信号</view>
						<view class="right_info flex_layout">
							<input type="text" v-model="wechat" placeholder="请填写微信号" value="" />
						</view>
					</view>
				<!-- #endif -->

				<view class="infoItem flex_layout">
					<view class="left_title">邮箱</view>
					<view class="right_info flex_layout">
						<input type="text" v-model="email" placeholder="请填写邮箱" value="" />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
				</view>
				
				<view class="infoItem flex_layout">
					<view class="left_title">介绍</view>
					<view class="right_info flex_layout">
						<textarea v-model="introcontent" placeholder="请输入个人介绍" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">地址</view>
					<!--  #ifdef  MP-WEIXIN	 -->
					<view class="right_info flex_layout" @click="addressClick">
						<input type="text" v-model="address" placeholder="请选择地址" :disabled="true" />
						<image src="../../static/images/right.png" mode=""></image>
					</view>
					<!--  #endif -->
					<!--  #ifndef  MP-WEIXIN	 -->
					<view class="right_info flex_layout">
						<input type="text" v-model="address" placeholder="请填写具体地址" value='' />
						<!-- <image src="../../static/images/right.png" mode=""></image> -->
					</view>
					<!--  #endif -->
				</view>
				<!-- <view class="infoItem flex_layout">
					<view class="left_title">性别</view>
					<view class="right_info flex_layout">
						<view class="inspect_message">
							<radio-group @change="radioChange">
								<view class="flex_layout">
									<label class="select_radio flex_layout">
										<view>
											<radio style="transform: scale(0.7);" :checked="gender==1" value="1" />
										</view>
										<view class="radioText">男</view>
									</label>
									<label class="select_radio flex_layout">
										<view>
											<radio style="transform: scale(0.7);" :checked="gender==0" value="0" />
										</view>
										<view class="radioText">女</view>
									</label>
								</view>
							</radio-group>
						</view>
					</view>
				</view> -->
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
	import {cdnUrl} from '../../config/config.js';
	let page = 1,reachbottom=false;
	export default {
		components:{
			uploadImg
		},
		data() {
			return {
				cdnUrl:cdnUrl,
				userInfo:'',
				imgList:[],
				avatar:'',
				name:'',
				wechat:'',
				address:'',
				birthday:'',
				gender:'1',
				mobile:'',
				idcard:'',
				email:'',
				titlesArray: [],
				titlesIdArray: [],
				departmentIdArray: [],
				//titlesId:'',
				departmentArray:[],
				department:'请选择科室',
				titles: '请选择职称',
				title_id: '', //关联医生职称
				department_id:'', //关联科室
				departmentIndex:0,
				titleIndex:0,
				user_id:'',
				company_id:'',
				position:'',
				color:'',
				maxCount:1,
				ifCompany:false,
				loadingStatus:true,
				companyname:'',
				company_id:'',
				companyList:[],
				keywords:'',
				introcontent:'',
				platform_status:1
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
			this.smartcardfind();
		},
		methods: {
			bindTitles(e) {
				var index = e.detail.value;
				this.title_id = this.titlesIdArray[index]
				this.titles = this.titlesArray[index]
			},
			bindDepartment(e){
				var index = e.detail.value;
				this.department_id = this.departmentIdArray[index]
				this.department = this.departmentArray[index]
			},
			addressClick(){
				uni.chooseLocation({
					success: (res)=> {
						this.address=res.address
					}
				});
			},
			chooseFileTest(list,ulist){
				this.imgList=ulist
				this.avatar=list[0]
			},
			radioChange(e){
				this.gender=e.detail.value;
			},
			bindDateChange(e){
				this.birthday=e.detail.value
			},
			companyInput(e){
				page=1;
				reachbottom=false;
				console.log(e.detail.value);
				if(e.detail.value.length>0){
					if (this.loadingStatus) {
						var keywords = e.detail.value;
						this.keywords = keywords
						this.companyList = []
						this.companylist(keywords);
					}
				}else{
					this.keywords='';
					this.ifCompany = false
				}
			},
			//医院列表
			companylist(keywords) {
				this.loadingStatus=false;
				this.$api.companylist(
				{
					companyname: keywords,
					page:page,
					limit:10
				},
				data => {
					if(data.code==1){
						this.ifCompany = true
						this.loadingStatus=true;
						var arr= data.data.map(item=>{
							return {
								...item
							}
						})
						if (data.data == ''||data.data.length == 0) {
							reachbottom = true
							uni.showToast({
								title: "已经加载全部",
								icon: "none",
								duration: 500
							});
						} else {
							page++;
							uni.stopPullDownRefresh();
							this.companyList= this.companyList.concat(arr);
						}
						//输入框字数为0时
						if (this.keywords.length == 0) {
							this.companyList = []
							page=1;
							reachbottom=false;
							this.ifCompany = false;
						}
					}
				})
			},
			bottomHospital(e){
				if (!reachbottom) {
					this.companylist(this.keywords);
				}
			},
			selectCompany(id, name) {
				this.company_id = id
				this.companyname = name
				this.companyList = []
				this.ifCompany = false;
			},
			smartcardfind(){
				this.$api.smartcardfind(
				{},
				data => {
					if(data.code){
						this.maxCount=1
						this.userInfo=data.data
						this.platform_status=data.data[0]?data.data[0].platform_status:2
						this.name=data.data[0]?data.data[0].name:'' //真实姓名
						this.mobile=data.data[0]?data.data[0].mobile:''//手机号
						this.email=data.data[0]?data.data[0].email:''  //邮箱
						this.address=data.data[0]?data.data[0].address:''//地址
						this.position=data.data[0]?data.data[0].position:''//职位
						if(data.data[0]){
							this.avatar=data.data[0].user.avatar;  //头像
							this.imgList[0]=this.avatar
						}else{
							let userInfo=this.$db.get('user')?this.$db.get('user'):'';
							console.log(userInfo);
							if(userInfo!=''){
								this.avatar=userInfo.avatar
								this.imgList[0]=this.avatar
							}else{
								this.avatar=''
								this.imgList=[];
							}
							
						}
					}
				})
			},
			submit(){
				var data={};
				var that=this;
				data['company_id']=this.company_id    //公司id
				data['name']=this.name      //真实姓名
				data['wechat']=this.wechat      //微信号
				data['mobile']=this.mobile     //手机号
				data['email']=this.email     //邮箱
				data['position']=this.position  //职位
				data['address']=this.address     //地址
				data['avatar']=this.avatar     //头像
				data['introcontent']=this.introcontent     //头像
				if(this.avatar==''){
					uni.showToast({
						title:'请上传头像',
						icon:'none'
					})
					return false;
				}
				if(this.name==''){
					uni.showToast({
						title:'请填写姓名',
						icon:'none'
					})
					return false;
				}
				// if(this.wechat==''){
				// 	uni.showToast({
				// 		title:'请填写微信号',
				// 		icon:'none'
				// 	})
				// 	return false;
				// }
				if(!this.$common.testString(this.mobile,'mobile')){
					uni.showToast({
						title:'请正确填写手机号',
						icon:'none'
					})
					return false;
				}
				// if (!this.$common.testString(this.email,'mail')) {
				// 	uni.showToast({
				// 		icon: 'none',
				// 		position: 'bottom',
				// 		title: '请正确填写邮箱'
				// 	});
				// 	return false;
				// }
				if(this.position==''){
					uni.showToast({
						title:'请填写职位',
						icon:'none'
					})
					return false;
				}	
				// if(this.address==''){
				// 	uni.showToast({
				// 		title:'请选择地址',
				// 		icon:'none'
				// 	})
				// 	return false;
				// }	
				this.$api.applyStaffAdd(
				data,
				data => {
					if(data.code==1){
						uni.setStorageSync('staff_id',data.data)
						that.$common.successToShow(data.msg,function(){
							uni.navigateBack()
						})
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
	.right_info image{width: 12rpx; height: 22rpx; display: block; margin-left: 20rpx;}
	.right_info textarea{width: 520rpx; margin:10rpx; padding:15rpx; background:#f7f7f7; border:1px solid #f3f3f3; border-radius:20rpx;font-size: 28rpx;}
	.select_radio{width: 200rpx;}
	.inspect_name{font-size: 28rpx; color: #333; width: 150rpx;}
	
	.sunmit_btn{padding: 40rpx;}
	.sunmit_btn button{color: #fff; background: #0084bf; font-size: 36rpx; height: 74rpx; line-height: 74rpx; width: 360rpx; border-radius: 74rpx; margin: 0 auto; padding: 0; display: block;}
	.sunmit_btn button::after{display: none;}
	
	.companyContent{position: absolute; top: 100rpx; left: 0; width: 100%; z-index: 100; background: #fff; box-shadow: 0 0 10px #ccc;}
	.companyList{padding: 30rpx; color: #333; font-size: 28rpx; border-bottom: 1px solid #F2F2F2;}
	
	.hospital_body{max-height: 400rpx; overflow-y: auto;}
	.manual_input{color: #45cb8c; font-size: 24rpx; padding: 10rpx 0; text-align: center;}
	.hospital_item {
		padding: 20rpx 30rpx;
		border-bottom: 1px solid #f2f2f2;
		font-size: 24rpx;
		color: #666;
		text-align: center;
	}
	
	.hospital_item:last-child {
		border: none;
	}
</style>
