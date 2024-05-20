<template>
	<view class="overall_content">
		<!-- <cu-custom :bgColor="bgColor" :isBack="isBack" :backGround="backGround">
			<block slot="backText">返回</block>
			<block slot="content">首页</block>
		</cu-custom> -->
		<web-view :src="url" :webview-styles="webviewStyles" @error="handleOnError"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				url: '',
				webviewStyles: {
					height: '1300rpx'
				},
				bgColor:'bg-gradual-custom',
				backGround:'',
				isBack: true
			}
		},
		onLoad(e) {
			this.url = decodeURIComponent(e.url)
		},
		methods: {
			handleOnError() {
				uni.showToast({
					icon: 'error',
					title: '网页地址有误，请回退后重试'
				})
			}
		}
	}
</script>

<style>
.overall_padding {
	padding: 30rpx 0;
 }
</style>
