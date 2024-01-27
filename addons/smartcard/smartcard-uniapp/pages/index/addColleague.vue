<template>
	<view>
		<view class="padding_content">
      <view class="Inside_fast Inside_fast_padding">
				<view class="infoItem flex_layout">
					<view class="left_title">员工名称</view>
					<view class="right_info">
						<!-- <input type="text" v-model="name" placeholder="请填写员工名称" value="" /> -->
						<view class="login-input login-margin-b" style="position: relative;">
							<input type="text" :value="name" @input="companyInput" placeholder="录入员工手机号或姓名关键字" :disabled="status==2" />
							<view class="companyContent" v-if="ifUsers">
								<scroll-view class="hospital_body" scroll-y="true" @scrolltolower="bottomHospital">
									<block v-if="userList.length>0">
										<view class="hospital_item" v-for="(item,index) in userList"
											@click="selectUser(item.id,item.nickname)" :key="index">手机:{{item.mobile}},昵称：{{item.nickname}}</view>
									</block>
									<block v-else>
										<view class="hospital_item">没有找到</view>
									</block>
								</scroll-view>
							</view>
						</view>
					</view>
				</view>
				<block>
					<view class="infoItem flex_layout">
						<view class="left_title">职位</view>
						<view class="right_info">
							<input type="text" v-model="position" placeholder="请输入员工职位/经理/主管/职员等" />
						</view>
					</view>
				</block>
				
				<!-- <view class="infoItem flex_layout">
					<view class="left_title">员工邮箱</view>
					<view class="right_info">
						<input type="text" v-model="email" placeholder="请输入员工邮箱" />
					</view>
				</view>
				<view class="infoItem infoItem_textarea">
					<view class="left_title">员工简介</view>
					<view class="right_info flex_layout">
						<textarea v-model="introcontent" placeholder="请输入员工简介" />
					</view>
				</view>
				<view class="infoItem">
					<view class="left_title">员工头像</view>
					<view class="right_icon flex_layout">
						<view class="chatIcon">
							<uploadImg ref='gUpload' type='header' :mode="picimages" :maxCount="maxCount" @chooseFile='chooseFileTest'></uploadImg>
						</view>
					</view>
				</view> -->
			</view>
		</view>		
		<!--提交按钮-->
		<block v-if="status==1">
			<view class="sunmit_btn" @click="addSubmit">
				<button :style="{background:color}">添加员工</button>
			</view>
		</block>
		<block v-if="status==2">
			<view class="sunmit_btn" @click="editSubmit">
				<button :style="{background:color}">编辑员工</button>
			</view>
		</block>
	</view>
</template>

<script>
	import uploadImg from "../../components/uploadImg/uploadImg.vue"
	let page = 1,reachbottom=false;
	export default {
		components:{
			uploadImg
		},
		data() {
			return {
				picimages:[],
				name:'',
				introcontent:'',
				email:'',
				id:'',
				type:'',
				status:1,
				maxCount:5,      //最大上传数量
				ifUsers:false,
				loadingStatus:true,
				user_id:'',
				userList:[],
				keywords:'',
				position:'',
				color:''
			}
		},
		onShow() {
			
		},
		onLoad(options) {
			this.color=uni.getStorageSync('color')
			if(options.id){
				this.id=options.id;
				this.stafffind();
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
			companyInput(e){
				page=1;
				reachbottom=false;
				if(e.detail.value.length>0){
					if (this.loadingStatus) {
						var keywords = e.detail.value;
						this.keywords = keywords
						this.userList = []
						this.userListOp(keywords);
					}
				}else{
					this.keywords='';
					this.ifUsers = false
				}
			},
			stafffind(){
				this.$api.stafffind(
				{
					staff_id: this.id
				},
				data => {
					if(data.code==1){
						this.name=data.data[0].name;
						this.user_id=data.data[0].user_id;
						this.position=data.data[0].position;
					}
				})
			},
			//用户列表
			userListOp(keywords) {
				this.loadingStatus=false;
				this.$api.userlist(
				{
					username: keywords,
					page:page,
					limit:10
				},
				data => {
					if(data.code==1){
						this.ifUsers = true
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
							this.userList= this.userList.concat(arr);
						}
						//输入框字数为0时
						if (this.keywords.length == 0) {
							this.userList = []
							page=1;
							reachbottom=false;
							this.ifUsers = false;
						}
					}
				})
			},
			bottomHospital(e){
				if (!reachbottom) {
					this.userListOp(this.keywords);
				}
			},
			selectUser(id, name) {
				this.user_id = id
				this.name = name
				this.userList = []
				this.ifUsers = false;
			},
			chooseFileTest(list){
				this.picimages=list
			},
			radioChange(e){
				this.gender=e.detail.value;
			},
			bindDateChange(e){
				this.birthday=e.detail.value
			},
			//新增
			addSubmit(){
				var _this=this;
				var data={};
				var picimages=this.picimages.toString();
				data['company_id']=this.company_id;
				data['name']=this.name      //员工名称
				data['user_id']=this.user_id      //员工id
				data['position']=this.position      //员工职位
				if(this.name==''){
					uni.showToast({
						title:'请填写员工信息',
						icon:'none'
					})
					return false;
				}
				if(this.position==''){
					uni.showToast({
						title:'请填写员工职位',
						icon:'none'
					})
					return false;
				}
				this.$api.companyStaffAdd(
				data,
				data => {
					if(data.code){
						
						uni.showToast({
								title:data.msg,
								icon:'success'
							});
						setTimeout(function(){
							_this.$common.navigateBack()
						},1000)
					} else{
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
						return false;
					}
				})
			},
			//编辑商品
			editSubmit(){
				var data={};
				var picimages=this.picimages.toString();
				data['id']=this.id                //商品id
				data['company_id']=this.company_id;
				data['position']=this.position
				if(this.name==''){
					uni.showToast({
						title:'请填写员工名称',
						icon:'none'
					})
					return false;
				}	
				
				this.$api.companyStaffEdit(
				data,
				data => {
					if(data.code){
						uni.navigateBack({})
						// uni.switchTab({
						// 	url:'user'
						// })
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
	.right_info input{width: 500rpx; height: 50rpx; color: #333; font-size: 28rpx; text-align: right;}
	.right_info image{width: 30rpx; height: 30rpx; display: block; margin-left: 20rpx;}
	.infoItem_textarea .right_info{margin-top: 30rpx;}
	.infoItem_textarea .right_info textarea{width: 100%; min-height: 200rpx; color: #333; font-size: 28rpx; border-radius: 10px; background: #F8F8F8; padding: 30rpx;}
	
	.select_radio{width: 200rpx;}
	.inspect_name{font-size: 28rpx; color: #333; width: 150rpx;}
	
	.sunmit_btn{padding: 40rpx;}
	.sunmit_btn button{color: #fff; background: #0084bf; font-size: 36rpx; height: 74rpx; line-height: 74rpx; width: 360rpx; border-radius: 74rpx; margin: 0 auto; padding: 0; display: block;}
	.sunmit_btn button::after{display: none;}
	
	.companyContent{position: absolute; top: 72rpx; left: 0; width: 100%; z-index: 100; background: #fff; box-shadow: 0 0 10px #ccc;}
	.userList{padding: 30rpx; color: #333; font-size: 28rpx; border-bottom: 1px solid #F2F2F2;}
	
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

