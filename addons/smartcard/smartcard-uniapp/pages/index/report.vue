<template>
	<view class="content">
		<view ><web-view :src="websrc"></web-view></view>
	</view>
</template>

<script>
	import {cdnUrl} from '../../config/config.js';
	export default {
		data() {
			return {
				websrc: '',
				appUrl:'/hybrid/html/readPdf/index/index.html',//app内的web地址
				webUrl:cdnUrl+'assets/addons/smartcard/readPdf/index/index.html',//远程web地址
				pdfUrl:'',//pdf路径
				title:'企业宣传册预览',//pdf文件名称
			}
		},
		onLoad(e) {
			if(e.id){
				this.pdfUrl=e.url;
				this.title=decodeURIComponent(e.title);
				uni.setNavigationBarTitle({
					title:this.title
				})
				this.id=e.id
			}else{
				uni.showToast({
					title:"参数错误"
				})
			}
			
			//app 直接跳转到app内的web页面
			//#ifdef APP-PLUS
			this.websrc=this.appUrl+'?url='+encodeURIComponent(this.pdfUrl)+'&tname='+encodeURIComponent(this.title)
			//#endif
			
			//微信小程序跳转到https web页面
			//#ifdef MP-WEIXIN
			this.websrc=this.webUrl+'?url='+encodeURIComponent(this.pdfUrl)+'&tname='+encodeURIComponent(this.title)
			//#endif
			//微信小程序跳转到https web页面
			//#ifdef H5
			this.websrc=this.webUrl+'?url='+encodeURIComponent(this.pdfUrl)+'&tname='+encodeURIComponent(this.title)
			//#endif
			console.log("this.websrc: ",this.websrc);
		},
	}
</script>

<style>
</style>
