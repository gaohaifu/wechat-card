<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<view class="attestation">
			<view class="change_tab flex_layout">
				<view class="tab_item" @click="titleChange(0)" :style="{color:tabIndex==0?color:''}" :class="tabIndex==0?'tab_active':''">全部</view>
				<view class="tab_item" @click="titleChange(1)" :style="{color:tabIndex==1?color:''}" :class="tabIndex==1?'tab_active':''">审核通过</view>
				<view class="tab_item" @click="titleChange(2)" :style="{color:tabIndex==2?color:''}" :class="tabIndex==2?'tab_active':''">待确认</view>
				<view class="tab_item" @click="titleChange(3)" :style="{color:tabIndex==3?color:''}" :class="tabIndex==3?'tab_active':''">审核失败</view>
				<view class="tab_item" @click="titleChange(4)" :style="{color:tabIndex==4?color:''}" :class="tabIndex==4?'tab_active':''">待同意</view>
			</view>
			<view class="Inside_content">
					<block v-if="essayArry.length>0">
						<view class="Inside_fast">
							<view class="ticket_item" v-for="(item,index) in essayArry" :key="index">
								<view class="ticket_header flex_layout">
									<view class="apply_date">创建时间：{{item.createtime}}</view>
									<block v-if="item.statusdata==1">
									  <view class="status_style status_color2">审核通过</view>
									</block>
									<block v-if="item.statusdata==2">
									  <view class="status_style status_color3">待确认</view>
									</block>
									<block v-if="item.statusdata==3">
									  <view class="status_style status_color1">审核失败</view>
									</block>
									<block v-if="item.statusdata==4">
									  <view class="status_style status_color1">待同意</view>
									</block>
								</view>
								<view class="ticket_body flex_layout">
									<view class="left_content"  v-if="item.user.avatar">
										<image :src="item.user.avatar" mode=""></image>
									</view>
									<view class="left_content" v-else>
										<image src="../../static/images/user.png" mode=""></image>
									</view>
									<view class="right_content">
										<view class="ticket_message flex_layout">
											<text>员工姓名：</text>
											<view class="return_message">
												<view>{{item.name}}</view>
											</view>
										</view>
										<view class="ticket_message flex_layout">
											<text>简介：</text>
											<view class="return_message">
												<view>{{item.introcontent}}</view>
											</view>
										</view>
										<view class="ticket_message flex_layout">
											<text>职位：</text>
											<view class="return_message">
												<view>{{item.position}}</view>
											</view>
										</view>
										<view class="ticket_message flex_layout">
											<text>邮箱：</text>
											<view class="return_message">
												<view>{{item.email}}</view>
											</view>
										</view>
									</view>
								</view>
								<view class="ticket_footer flex_layout"><button v-if="item.statusdata==2" @click.stop="companyStaffEditOp(item.id,index)" class="btnStyle2" :style="{color:color,border:`1px solid ${color}`}">审核</button><button @click.stop="companyStaffDeleteOp(item.id,index)" class="btnStyle2" :style="{color:color,border:`1px solid ${color}`}">删除</button>
								</view>
							</view>
						</view>
					</block>
					<block v-else>
						<noData v-if="complete"></noData>
					</block>
			</view>
		</view>
		<view style="height: 100rpx;"></view>
		<view class="add_btn" @click="addBtn"><button :style="{background:color}">+ 新增员工</button></view>
	</view>
</template>

<script>
	var _this;
	let page=1,reachbottom = false;var _this;
	import userInfo from '@/components/user-info/user-info.vue'
	import noData from '@/components/no-data/no-data.vue';
	export default {
		components: {
		  userInfo,
			noData
		},
		data() {
			return {
				tabIndex:0,
				userData:'',
				complete:false,
				essayArry:[],
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
		},
		mounted() {
			_this= this;
		},
		onShow() {
			reachbottom=false
			//监听当前网络连接状态
			page=1;
			this.essayArry=[];
			this.companyInfoData();
		},
		// 加载更多,触底加载
		onReachBottom: function() {
			if (!reachbottom) {
				this.companyInfoData();
			}
		},
		methods: {
			addBtn(){
				var type='staff';
				uni.navigateTo({
					url:'addColleague?status=1'+'&company_id='+this.userData.company_id+'&type='+type
				})
			},
			edit(id){
				var type='staff';
				uni.navigateTo({
					url:'addColleague?id='+id+'&status=2'+'&company_id='+this.userData.company_id+'&type='+type
				})
			},
			titleChange(index){
				this.tabIndex=index
				reachbottom=false
				//监听当前网络连接状态
				page=1;
				this.essayArry=[];
				this.companyInfoData();
			},
			companyStaffEditOp(id,index){
				var _this=this;
				this.$common.modelShow('提示','请确认并审核员工信息？',function(){
						_this.editstaff(id,index,1);
				},function(){
					_this.editstaff(id,index,3);
				},true,'审核失败','审核通过');
			},
			//编辑员工
			editstaff(id,index,status){
				this.$api.companyStaffEdit(
				 {
					 id:id,
					 company_id:this.userData.company_id,
					 statusdata:status
				 },
				data => {
					if(data.code==1){
						this.essayArry[index].statusdata=status
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
					}else{
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
					}
				})
			},
			companyStaffDeleteOp(id,index){
				var _this=this;
				this.$common.modelShow('提示','确认删除该员工，请谨慎操作？',function(){
						_this.deleteStaff(id,index);
				},function(){
				},true,'取消','确认删除');
			},
			//删除员工
			deleteStaff(id,index){
				this.$api.companyStaffDelete(
				 {
					 id:id,
					 company_id:this.userData.company_id,
				 },
				data => {
					if(data.code==1){
						this.essayArry.splice(index,1)
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
					}else{
						uni.showToast({
							title:data.msg,
							icon:'none'
						})
					}
				})
			},
			assignmentDetail(id){
				uni.navigateTo({
					url:'assignmentDetail?id='+id
				})
			},
			companyInfoData(){
				this.complete=false;
				const parm={
					company_id:this.userData.company_id,
					type:'staff',
					stafftype:this.tabIndex,
					//keywords:this.keywods,
					page:page,
					limit:5
				};
				this.$api.companyInfoData(
					 parm,
					data => {
						if(data.code==1){
							this.complete=true;
							var dataList=data.data.Infos;
							var number=dataList.length;
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
								this.essayArry = this.essayArry.concat(dataList);
							}
						}	
					})
			},
		}
	}	
</script>

<style>
	@import url('../../common/common.css');
	page{background: #f2f6f7;}
	.Inside_content{margin-top: 30rpx;}
	.ticket_item{margin-bottom: 20rpx; background: #fff;}
	.ticket_item:last-child{margin: none;}
	.photo_album{padding: 30rpx;}
	.noData_img image{width: 360rpx; height: 360rpx; display: block; margin: 150rpx auto 0;}
	.point_out{margin-top: 50rpx; color: #333; font-size: 36rpx; text-align: center;}
	.select_time{justify-content: space-between;}
	.dateTime{justify-content: space-between;}
	.attestation{padding: 15rpx 0;}
	.Inside_fast{border-radius: 10px; margin-top: 30rpx;}
	.Inside_content .Inside_fast:first-child{margin: 0;}
	.Inside_fast:first-child{margin: 0;}
	.change_tab{padding: 0 30rpx; background: #fff; justify-content: space-between;}
	.tab_item{padding: 30rpx 20rpx; color: #333; font-size: 30rpx;}
	.tab_active{color: #0084bf; background:#f2f6f7}
	.ticket_header{padding: 30rpx; justify-content: space-between; border-bottom: 1px solid #f2f2f2;}
	.apply_date{color: #999; font-size: 24rpx;}
	.status_style{font-size: 28rpx; color: #333;}
	.ticket_body{padding: 30rpx;}
	.ticket_message{padding: 8rpx 0; align-items: flex-start;}
	.ticket_message>text{color: #999; font-size: 24rpx;}
	.left_title{display: block; width: 200rpx; padding: 30rpx; color: #666; font-size: 28rpx;}
	.return_message{padding-left: 20rpx; justify-content: space-between;}
	.return_message view{color: #333; font-size: 24rpx; line-height: 1.5; margin-top: 10rpx; display: flex; align-items: center;}
	.return_message view:first-child{margin: 0;}
	.return_message view text{display: block; color: #0084bf; margin-right: 20rpx;}
	.return_message image{display: block; width: 28rpx; height: 28rpx;}
	.ticket_footer{justify-content: flex-end; padding: 30rpx; border-top: 1px solid #F2F2F2;}
	.ticket_footer button{width: 150rpx; height: 50rpx; line-height: 50rpx; border-radius: 5px; margin: 0 0 0 20rpx; padding: 0; font-size: 24rpx;}
	.btnStyle1{background: #0084bf; color: #fff;}
	.btnStyle2{background: #fff; color: #0084bf;}
	
	.left_content image{display: block; width: 120rpx; height: 120rpx; border-radius: 50%;}
	.right_content{padding-left: 30rpx;}
	
	.accomplish{color: #999;}
	.accomplish .apply_date{color: #999;}
	.accomplish .status_style{color: #999;}
	.accomplish .return_message view{color: #999;}
	
	.selectDate{color: #333; font-size: 24rpx; padding: 30rpx;}
	.selectDate image{width: 20rpx; height: 20rpx; margin-left: 10rpx;}
	
	.status_color1{color: #ff3e3e;}
	.status_color2{color: #00b386;}
	.status_color3{color: #333;}
	
	.add_btn{position: fixed; left: 0; bottom: 0; z-index: 100; height: 100rpx; width: 100%;}
	.add_btn button{width: 100%; height: 100rpx; background: #0084bf; color: #fff; font-size: 30rpx; border-radius: 0; line-height: 100rpx; margin: 0; padding: 0;}
	.edit_button button{width: 150rpx; height: 50rpx; background: #0084bf; color: #fff; font-size: 24rpx; border-radius: 0; line-height: 50rpx; margin: 0; padding: 0; display: block; margin: 30rpx auto 0;}
</style>
