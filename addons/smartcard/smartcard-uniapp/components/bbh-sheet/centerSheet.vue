<template>
	<view>
		<view class="modelBox"  v-if="isShow"    @touchmove.stop.prevent="moveHandle"  @click="closeMysheet()">
			<scroll-view scroll-y  class="modelSheet" @click.stop="otherClick()">
				<view v-for="(item,index) in 50" :key="index">{{index}}</view>
			</scroll-view>
		</view>
	</view>
</template>
<script>
	export default {
		name : "centerSheet",
		props: {
			isShow : {
				type : Boolean
			}
		},
		methods:{
			moveHandle(){	//阻止蒙版底部的滑动
				return
			},	  
			otherClick(){	//弹窗的点击事件，阻止事件冒泡
				
			},
			closeMysheet(){
				this.$emit("closeCenter");
			}
		}
	}
</script>
<style>
	.modelBox{
		position: fixed;
		z-index: 999;
		width: 100%;
		height:100%;
		top: 0;
		left:0;
		background:rgba(0,0,0,0.3);
		display: flex;
		align-items: center;
		justify-content: center;
	}
	.modelSheet{
		width: 80%;
		height:400rpx;
		background-color:#FFF;
		text-align: center;
		border-radius: 8rpx;
		overflow: hidden;
	}
</style>
