<template>
	<view class="content">
		<view class="sheet" :class="{sheetShow:isShowBottom,sheeHide:!isShowBottom}" @touchmove.stop.prevent="moveHandle">
			<view class="sheetView" :class="{sheetView_active:isShowBottom}" @click.stop="stopEvent()">
				<view class="routine_title flex_layout"><image src="../../static/images/user.png" mode=""></image><text>智能名片</text></view>
				<view class="routine_message">
					<view class="content1">授权微信访问</view>
					<view class="content2">为方便永久保存名片，请授权微信权限</view>
					<view class="radio_content flex_layout">
						<checkbox-group @change="changeCheck">
						  <checkbox :value="1" style="transform:scale(0.7);" :checked="true"/>
						</checkbox-group>
						<view class="flex_layout">同意授权获取</view>你的微信昵称和头像
					</view>
					<view class="empower" @click="cancelTap"><button>微信授权</button></view>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	export default {
		name : "bottomSheet",
		props: {
			isShowBottom : Boolean
		},
		
		watch: {
		  
		},
		data() {
			return {
				checkArray:[]
			}
		},
		created() {
			let yy = new Date().getFullYear()
			let mm = (new Date().getMonth() + 1)>9?(new Date().getMonth() + 1):'0'+(new Date().getMonth() + 1)
			let dd = new Date().getDate()>9?new Date().getDate():'0'+new Date().getDate()
			this.dateTime = yy + '-' + mm + '-' + dd
		},
		methods: {
			changeCheck(e){
				this.checkArray=e.detail.value
			},
			
			bindDatesf(e){
				this.nextsfdateNew=e.detail.value;
			},
			closeSheet(){
				this.$emit('closeBottom');
			},
			//授权按钮
			cancelTap(){
				this.$emit('closeBottom');
			},
			stopEvent(){						//@click.stop防止事件冒泡
				
			},
			moveHandle(){						//不让页面滚动
				
			}
		}
	}
</script>

<style>
	.header_slither{padding: 0 30rpx; height: 100rpx; line-height: 100rpx; justify-content: space-between; border-bottom: 1px solid #f8f8f8;}
	.left_cancel{color: #999; font-size: 30rpx; width: 200rpx; text-align: left; height: 100rpx; line-height: 100rpx;}
	.right_confirm{color: #45cb8c; font-size: 30rpx; width: 200rpx; text-align: right; height: 100rpx; line-height: 100rpx;}
	/* sheet弹窗 */
	.sheet{
		width: 100%;
		height: 100%;
		position: fixed;
		top: 120%;
		left: 0upx;
		bottom: 0upx;
		right: 0upx;
		background:rgba(10,10,10,0.9);
		z-index: 10000;
	}
	
	/* 显示时候的动画默认0.5s */
	.sheetView{
		width: 100%;
		height: 0upx;
		position: absolute;
		bottom: 0upx;
		background: white;
		z-index: 10001;
		transition: all 0.5s;
		padding: 30rpx 45rpx;
		border-radius: 15px 15px 0 0;
	}
	.sheetShow{
		top:0upx !important;
	}
	/* 关闭时的动画，时间自己可以设置0.5s*/
	.sheeHide{
		top:120% !important;
		transition: all 0.5s;		
	}
	
	/* 项目需求根据设计稿要展示的高度,超出这个高度就自动滚动*/
	.sheetView_active{
		height: 500rpx;
	}
	
	.routine_title image{width: 50rpx; height: 50rpx; display: block; border-radius: 50%;}
	.routine_title text{display: block; color: #333; font-size: 28rpx; margin-left: 20rpx;}
	.content1{color: #333; font-size: 32rpx; margin-top: 50rpx;}
	.content2{color: #333; font-size: 24rpx; margin-top: 20rpx;}
	.empower{margin-top: 30rpx;}
	.empower button{color: #fff; font-size: 30rpx; background: #07c15e; height: 85rpx; line-height: 85rpx; width: 100%;}
	.radio_content{margin-top: 80rpx; color: #ccc;}
	.radio_content view{margin-left: 15rpx;}
	.radio_content text{display: flex; color: #999;}
</style>
