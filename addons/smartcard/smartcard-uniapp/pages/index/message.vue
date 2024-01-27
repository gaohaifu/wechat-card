<template>
	<view class="content">
		<userInfo :userData="userData" :color="color"></userInfo>
		<view class="appraise">
			<!-- <view class="address flex_layout">
				<view class="left_address">
					<view>家乡</view>
					<text>江苏省 无锡市</text>
				</view>
				<view class="townee" :class="towneeStatus?'townee_active':''" @click="towneeTab"><button>{{towneeText}}</button></view>
			</view> -->
			<view class="label_content flex_layout">
				<view class="label_item" :style="{color:item.isFavor?color:'#333',border:`1px solid ${item.isFavor?color:'#d7d7d7'}`}" :class="item.isFavor?'label_active':''" v-for="(item,index) in labelList" :key="index" @click="collectTab(index,item.isFavor,item.id)">
					<block v-if="item.isFavor">
						<i :style="{color:color}" class="iconfont icon-thumbup-fill"></i>
					</block>
					<block v-else>
						<i class="iconfont icon-dianzan1"></i>
					</block>
					<text>{{item.name}} {{item.num}}</text>
					<!--删除按钮-->
					<block v-if="item.isdelAble">
						<view :style="{color:item.isFavor?color:'#333'}" @click.stop="tagDelData(item.id)" class="deleteIcon iconfont icon-shanchu"></view>
					</block>
				</view>
			</view>
			<view class="appraise_want flex_layout">
				<!-- <view class="appraise_img"><image src="../../static/images/user.png" mode=""></image></view> -->
				<view class="appraise_input flex_layout"><input @input="labelChange" maxlength="10" type="text" :value="labelText" placeholder="添加标签" /><text>{{maxlength}}/10</text></view>
				<view class="publish" @click="publish"><button :style="{background:color}">发表</button></view>
			</view>
			<view>
				<view class="intro-title">自我介绍</view>
				<block v-if="introcontent!=''">
					<view class='intro-con'>{{introcontent}}</view>
				</block>
				<block v-else>
						<view class="photo_album">
							<view class="none_thread flex_layout">
								<view class="thread_item"></view>
								<view class="text_item flex_layout"><text></text>暂无更多<text></text></view>
								<view class="thread_item"></view>
							</view>
						</view>
					
				</block>
			</view>
		</view>
		
	</view>
</template>

<script>
	var _this;
	import userInfo from '@/components/user-info/user-info.vue'
	export default {
		components: {
		  userInfo
		},
		data() {
			return {
				towneeText:'是同乡',
				towneeStatus:false,
				userData:'',
				staff_id:0,
				labelList:[],
				labelText:'',
				maxlength:0,
				color:'',
				reveal:'',
				introcontent:'',
				company_id:''
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
			this.staff_id=options.staff_id
			this.userData.nickname=options.nickname;
			this.tagsData();
			this.staffInfoData()
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
			//标签列表
			tagsData(){
				const parm={
					staff_id:this.staff_id
				};
				this.$api.tagsData(
					 parm,
					data => {
						if(data.code==1){
							this.labelList=data.data
						}
						
					})
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
							this.introcontent=data.data.introcontent
							this.company_id=data.data.company_id
						}
					})
			},
			collectTab(index,isFavor,id){
				const parm={
					staff_id:this.staff_id,
					tags_id:id
				};
				this.$api.tagsFavorOptionData(
					 parm,
					data => {
						if(data.code==1){
							this.labelList[index].num=data.data.favorNum
							if(isFavor==0){
								this.labelList[index].isFavor=1
							}else{
								this.labelList[index].isFavor=0
							}
						}	
				  }  
				)
			},
			labelChange(e){
				this.maxlength=e.detail.value.length
				this.labelText=e.detail.value;
			},
			//添加标签
			publish(){
				//判断输入框是否有值
				if(this.labelText){
					const parm={
						staff_id:this.staff_id,
						tags_name:this.labelText
					};
					this.$api.tagAddData(
						 parm,
						data => {
							if(data.code==1){
								this.labelText='';
								this.maxlength=0;
								this.tagsData();
								uni.showToast({
									title:'添加成功',
									icon:'none',
									duration:1000
								})
							}	
						}  
					)
				}else{
					uni.showToast({
						title:'请输入标签内容',
						icon:'none',
						duration:1000
					})
				}
				
			},
			//删除标签
			tagDelData(id){
				uni.showModal({
				    title: '提示',
				    content: '是否确定删除此标签？',
				    success: res=> {
				        if (res.confirm) {
				            const parm={
				            	tags_id:id
				            };
				            this.$api.tagDelData(
				            	 parm,
				            	data => {
				            		if(data.code==1){
				            			this.tagsData();
				            		}	
				            	}  
				            )
				        } else if (res.cancel) {
				            return false;
				        }
				    }
				});
				
			},
			// towneeTab(){
			// 	if(this.towneeStatus){
			// 		this.towneeStatus=false;
			// 		this.towneeText='是同乡'
			// 	}else{
			// 		this.towneeStatus=true;
			// 		this.towneeText='同乡'
			// 	}
			// },
			
		}
	}	
</script>

<style>
	@import url('../../common/common.css');
	page{background: #f2f6f7;}
	.appraise{background: #fff;}
	.address{justify-content: space-between; padding: 30rpx;}
	.left_address view{color: #333; font-size: 30rpx; font-weight: bolder;}
	.left_address text{color: #333; font-size: 30rpx; display: block; margin-top: 15rpx;}
	.townee button{padding: 15rpx 18rpx; line-height: 1; font-size: 28rpx; color: #333; background: #fff; border: 1px solid #eee;}
	.townee_active button{color: #0084bf; border-color: #0084bf;}
	.label_content{padding: 30rpx;}
	.label_item{color: #333; padding: 10rpx 12rpx; margin-bottom: 20rpx; border: 1px solid #d7d7d7; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 20rpx;}
	.label_active{color: #0084bf; border: 2px solid #0084bf;}
	.deleteIcon{margin-left: 10rpx; font-size: 24rpx;}
	.label_item i{font-size: 30rpx; margin-right: 5rpx;}
	.label_item text{display: block; font-size: 24rpx;}
	.appraise_want{padding: 30rpx; justify-content: space-between;}
	.appraise_img{width: 75rpx; height: 75rpx;}
	.appraise_img image{width: 75rpx; height: 75rpx; border-radius: 50%; display: block;}
	.appraise_input{background: #f5f5f5; color: #999; padding: 20rpx 25rpx; border-radius: 5px; width: 570rpx; justify-content: space-between;}
	.appraise_input input{font-size: 28rpx; width: 450rpx;}
	.appraise_input text{display: block; color: #999; font-size: 24rpx; margin-left: 10rpx; text-align: right;}
	.publish button{width: 100rpx; padding: 0; margin: 0; height: 75rpx; background: #0084bf; color: #fff; font-size: 28rpx; line-height: 75rpx; text-align: center; border-radius: 5px;}
	.photo_album{padding: 30rpx;}
	.noData_img image{width: 360rpx; height: 360rpx; display: block; margin: 150rpx auto 0;}
	.point_out{margin-top: 80rpx;}
	.point_out button{width: 340rpx; height: 90rpx; line-height: 90rpx; text-align: center; color: #fff; font-size: 30rpx; border-radius: 5px; background: #0084bf;}
	.none_thread{width: 440rpx; margin: 50rpx auto 0;}
	.thread_item{width: 140rpx; height: 1px; background: #cccccc;}
	.text_item{width: 160rpx; color: #999; font-size: 24rpx; justify-content: space-between;}
	.text_item text{display: block; width: 10rpx; height: 10rpx; border-radius: 10rpx; background: #ccc;}
	.intro-con{ padding:40rpx; font-size:28rpx; line-height:35rpx;}
	.intro-title{ padding:20rpx 40rpx; line-height:40rpx;font-size:32rpx; }
</style>
