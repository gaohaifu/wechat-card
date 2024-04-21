<!-- 企业认证表单 -->
<template>
	<view class="page-wrap">
		<view class="company-label" v-if="is_authentication == 3">
			<text>认证失败情况</text>
			<view class="example-text" style="width: 480rpx; color: red;">
				{{reason}}
			</view>
		</view>
		<view class="company-label">企业名称</view>
		<view class="company-name">{{ companyname }}</view>
		<view class="company-label">
			<text>企业地址</text>
			<view class="flex flex-vc example-text" @click="addressClick">
				<input type="text" v-model="address" placeholder="请选择地址" :disabled="true" />
				<text class="iconfont icon-dizhi1"></text>
			</view>
		</view>
		<view class="company-label">
			<text>认证材料</text>
			<text class="example-text">上传材料示例</text>
		</view>
		<view class="up-box">
			<image :src="licenseImg? cdnUrl + licenseImg : require('../../static/images/attestation01.png')"
				@click="upLicense('licenseImg')" />
			<view>营业执照</view>
			<view class="up-text">与企业名称保持一致</view>
		</view>
		<view class="company-label">
			<text>认证材料</text>
			<text class="example-text">上传材料示例</text>
		</view>
		<view class="up-box">
			<image :src="letterImg? cdnUrl + letterImg : require('../../static/images/attestation02.png')"
				@click="upLicense('letterImg')" />
			<view style="font-size: 24rpx;">公函</view>
			<view class="up-text" style="color: #0256FF;">下载模板</view>
		</view>
		<view class="company-label">注明：</view>
		<view class="descript">
			<view>1、依据《公司法》相关规定，未经依法登记而冒用公司和分公司的名义将<text style="color: #EA6100;">被处以最高10万元的罚款，并追究相关刑事责任</text> </view>
			<view>2、上传图片必须为清晰完整的实物图片，请严格按照 <text style="color: #0256FF;">示例</text> 上传根据《中华人民共和国个人信息保护法》，上传材料仅作审核用</view>
			<view>3、不在页面公开展示</view>
		</view>
		<view class="aggrement-box" @click="aggreFn">
			<checkbox value="cb" :checked="aggre" style="transform:scale(0.7)" />
			<view>我已阅读并同意<text style="color: #0256FF;">《企业认证服务协议》</text></view>
		</view>
		<view class="attestation-btn">
			<view @click="submitFn">提交</view>
		</view>
	</view>
</template>

<script>
	import { normalToShow, errorToShow, successToShow } from '@/config/common.js'
	import {cdnUrl} from '@/config/config.js'
	export default {
		name: 'coporationAttestation',
		data() {
			return {
				address: '',
				latitude: '',
				longitude: '',
				cdnUrl: cdnUrl,
				companyname: '',
				licenseImg: '',
				letterImg: '',
				aggre: false,
				reason: '',
				is_authentication: ''
			}
		},
		onLoad(options) {
			const userData = uni.getStorageSync('userData') || {}
			// console.info(userData, '=====>>>')
			this.companyname = options.companyname || userData.companyname || '企业认证'
			this.getEnterpriseInfo()
		},
		methods: {
			// 获取实名认证信息
			getEnterpriseInfo() {
				this.$api.getEnterpriseInfo({}, res => {
					console.info(res, '===========>>>')
					this.reason = res.reason || ''
					this.address = res.address
					this.latitude = res.latitude
					this.longitude = res.longitude
					this.is_authentication = res.is_authentication // 认证状态:0=未认证,1=待审核,2=已审核,3=审核失败
					this.licenseImg = res.licenseImg
					this.letterImg = res.letterImg
				})
			},
			// 选择地址
			addressClick() {
				console.log('address');
				uni.chooseLocation({
					latitude: this.latitude,
					longitude:this.longitude,
					success: (res) => {
						console.log(res);
						this.address = res.address
						this.latitude = res.latitude
						this.longitude = res.longitude
					}
				});
			},
			aggreFn() {
				this.aggre = !this.aggre
			},
			upLicense (key) {
				this.$api.uploadImage('common/upload', {}, dataimg => {
					this[key] = dataimg.data.url // 相对路径 fullurl绝对路径
				}, 1)
			},
			submitFn() {
				console.log('submit')
				if (!(this.latitude && this.longitude)) {
					return normalToShow('请选择公司地址')
				} else if (!this.licenseImg) {
					return normalToShow('请上传营业执照')
				} else if (!this.letterImg) {
					return normalToShow('请上传公函')
				} else if (!this.aggre) {
					return normalToShow('请阅读并同意《企业认证服务协议》')
				}
				this.$api.enterpriseCertified({
					address: this.address,
					latitude: this.latitude,
					longitude: this.longitude,
					licenseimage: this.licenseImg,
					official_letter: this.letterImg
				}, (res) =>{
					console.log(res, '企业认证')
					if (res.code == 1) {
						successToShow(res.msg)
					} else errorToShow(res.msg)
				})
			}
		}
	}
</script>

<style>
	.page-wrap {
		padding-bottom: 128rpx;
	}
	.company-label {
		display: flex;
		align-items: center;
		justify-content: space-between;
		margin: 48rpx 32rpx 16rpx;
		font-size: 30rpx;
		color: #333;
		font-weight: 500;
	}
	.example-text {
		font-size: 24rpx;
		color: #0256FF;
	}
	.company-name {
		margin: 0 32rpx;
		padding: 0 20px;
		height: 88rpx;
		line-height: 88rpx;
		background-color: #F6F7F9;
		border-radius: 8rpx;
	}
	.company-name input {
		display: inline-block;
		width: 100%;
		height: 40rpx;
		line-height: 40rpx;
		margin-top: 24rpx;
		font-size: 28rpx;
		color: #333;
	}
	.up-box {
		display: inline-block;
		padding: 24rpx 32rpx;
		margin: 0 32rpx;
		background-color: #FAFAFA;
		border-radius: 16rpx;
		font-size: 32rpx;
		color: #333;
		text-align: center;
	}
	.up-box image {
		width: 288rpx;
		height: 200rpx;
		margin-bottom: 16rpx;
	}
	.up-text {
		font-size: 24rpx;
		color: #999;
		line-height: 32rpx;
		margin-top: 8rpx;
	}
	.descript {
		margin: 0 32rpx 40rpx;
		font-size: 20rpx;
		color: #666;
		line-height: 40rpx;
	}
	.aggrement-box {
		display: flex;
		align-items: center;
		margin: 0 32rpx;
		font-size: 24rpx;
		color: #333;
	}
	.attestation-btn {
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
		height: 108rpx;
		background-color: #fff;
	}
	.attestation-btn view {
		margin: 14rpx 32rpx 0;
		height: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		text-align: center;
		color: #fff;
		background-color: #0256FF;
		border-radius: 60rpx;
	}
	.page-wrap .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
		line-height: 32rpx;
		text-align: center;
		color: #999;
	}
</style>