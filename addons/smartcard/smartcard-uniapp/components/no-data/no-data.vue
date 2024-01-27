<template>
	<view class="photo_album">
		<view class="noData_img"><image src="../../static/images/empty.png" mode=""></image></view>
		<view class="point_out" @click="pointOut"></view>
		<view class="none_thread flex_layout">
			<view class="thread_item"></view>
			<view class="text_item flex_layout"><text></text>什么都没有<text></text></view>
			<view class="thread_item"></view>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			
		},
		mounted() {
			
		},
		methods: {
			
		},
	};
</script>

<style>
	.noData_img image{width: 360rpx; height: 360rpx; display: block; margin: 150rpx auto 0;}
	.none_thread{width: 440rpx; margin: 50rpx auto 0;}
	.thread_item{width: 140rpx; height: 1px; background: #cccccc;}
	.text_item{width: 160rpx; color: #999; font-size: 24rpx; justify-content: space-between;}
	.text_item text{display: block; width: 10rpx; height: 10rpx; border-radius: 10rpx; background: #ccc;}
</style>
