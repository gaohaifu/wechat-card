<template>
	<view class="overall_content user-info">
		<smallUserInfo page-title="编辑资料" 
			:is-show="isShowSmallUserInfo"
			:bg-color="bgColor"
			:back-ground="backGround"
			:is-back="true"></smallUserInfo>
		<view class="padding_content">
			<div class="flex flex-hsb flex-vc Inside_fast change-card-box">
				<text class="title">名片样式</text>
				<view class="flex flex-vc" @click="linkToChange">
					<text class="title2">更换名片背景</text>
					<text class="iconfont icon-Rightyou"></text>
				</view>
			</div>
			<view class="Inside_fast">
				<view class="title">
					基本信息
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">
						头像
						<!-- <text style="color:#ff0000">*</text> -->
						<text class="iconfont icon-wenhao"></text>
					</view>
					<view class="right_info">
						<view class="chatIcon">
							<uploadImg ref='gUpload' type="avatar" :mode="imgList" :maxCount="maxCount"
								@chooseFile='chooseFileTest'></uploadImg>
						</view>
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">姓名<text style="color:#ff0000">*</text></view>
					<view class="right_info">
						<input type="text" v-model="name" placeholder="请填写真实姓名" value="" />
					</view>
				</view>
				<!--选择公司-->
				<view class="infoItem flex_layout">
					<view class="left_title">公司<text style="color:#ff0000">*</text></view>
					<view class="right_info" style="position: relative;">
						<input type="text" placeholder="选择公司"
							v-model="companyname" @input="companyInput"
							 @blur="handleOnCampanyBlur"/>
						<view class="companyContent" v-if="ifCompany">
							<scroll-view class="hospital_body" scroll-y="true" @scrolltolower="bottomHospital">
								<block v-if="companyList.length>0">
									<view class="hospital_item" v-for="(item,index) in companyList"
										@click="selectCompany(item.id,item.name)" :key="index">{{item.name}}</view>
								</block>
								<block v-else>
									<view class="hospital_item">没有找到</view>
								</block>
							</scroll-view>
						</view>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">职位<text style="color:#ff0000">*</text></view>
					<view class="right_info">
						<input type="text" v-model="position" placeholder="请填写职位" value="" />
						<text class="iconfont icon-zengjiatianjiajiahao"></text>
					</view>
				</view>
				<view class="infoItem flex_layout" style="flex-wrap: nowrap;">
					<view class="left_title">行业<text style="color:#ff0000">*</text></view>
					<view class="right_info">
						<!-- <input type="text" v-model="industry" placeholder="请填写行业" value="" /> -->
						<uni-data-picker v-model="industry_id" placeholder="请选择行业" popup-title="所属行业"
							:localdata="industryList" :map="{text: 'name', value: 'id'}"
							@change="handleOnIndustryChange"
							style="width: 100%;">
						</uni-data-picker>
						<!-- <text class="iconfont icon-Rightyou"></text> -->
					</view>
				</view>
			</view>

			<view class="Inside_fast">
				<view class="title">
					联系方式
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">手机<text style="color:#ff0000">*</text></view>
					<view class="right_info">
						<input type="number" v-model="mobile" placeholder="请填写手机号" value="" />
					</view>
				</view>
				<view class="infoItem flex_layout" v-if="platform_status==2">
					<view class="left_title">微信</view>
					<view class="right_info">
						<input type="text" v-model="wechat" placeholder="请填写微信号" value="" />
						<text class="iconfont icon-Rightyou"></text>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">邮箱</view>
					<view class="right_info">
						<input type="text" v-model="email" placeholder="请填写邮箱" value="" />
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">地址<text style="color:#ff0000">*</text></view>
					<view class="right_info" @click="addressClick">
						<input type="text" v-model="address" placeholder="请选择地址" :disabled="true" />
						<text class="iconfont icon-dizhi1"></text>
					</view>
				</view>
				<view class="infoItem flex_layout">
					<view class="left_title">QQ</view>
					<view class="right_info">
						<input type="text" v-model="qq" placeholder="请填写QQ" value="" />
						<text class="iconfont icon-jianhao"></text>
					</view>
				</view>
			</view>
		</view>
		<!--提交按钮-->
		<view style=" paddiing:20rpx; color:#ff0000;text-align:center">*如果修改了关联公司，账户将变为未认证状态，请谨慎操作</view>
		<view class="flex flex-vc footer">
			<view class="flex flex-v flex-vc left-box">
				<text class="iconfont icon-yulan"></text>
				<text>预览名片</text>
			</view>
			<view class="flex-1 primary-btn" @click="submit">保存</view>
		</view>
	</view>
</template>

<script>
	import uploadImg from "../../components/uploadImg/uploadImg.vue"
	import smallUserInfo from "../../components/small-user-info/small-user-info.vue"
	import {
		smartcardBG
	} from '../../config/common.js'
	let page = 1,
		reachbottom = false;
	export default {
		components: {
			uploadImg,
			smallUserInfo
		},
		data() {
			return {
				bgColor:'bg-gradual-custom',
				backGround:'',
				isShowSmallUserInfo: true,
				smartcardBG: smartcardBG,
				userInfo: {},
				imgList: [],
				avatar: '',
				name: '',
				wechat: '',
				address: '',
				birthday: '',
				gender: '1',
				mobile: '',
				idcard: '',
				email: '',
				qq: '',
				user_id: '',
				company_id: 0,
				industry_id: '',
				position: '',
				introcontent: '',
				color: '',
				maxCount: 1,
				ifCompany: false,
				loadingStatus: true,
				companyname: '',
				companyList: [],
				industryList: [{name: '全部', id: -1}], // 没有初始化好像会报name错误问题
				ids: 0,
				platform_status: 2 // 小程序？
			}
		},
		onShow() {
			this.isShowSmallUserInfo = true
		},
		onHide() {
			this.isShowSmallUserInfo = false
		},
		onLoad(options) {
			console.info(smartcardBG, '====smartcardBG', options)
			this.color = uni.getStorageSync('color')
			//修改导航条背景颜色
			uni.setNavigationBarColor({
				frontColor: '#ffffff',
				backgroundColor: this.color
			})
			this.user_id = options.user_id;
			this.staff_id = options.staff_id
			this.company_id_s = this.company_id = options.company_id;
			// this.company_id_s = options.company_id;
			this.smartcardfind();
			this.getIndustryData();			
		},
		onPageScroll(e){
			if(e.scrollTop>0){
				this.bgColor='bg-gradual-white';
				this.backGround=this.color
			}else{
				this.bgColor='bg-gradual-custom';
				this.backGround='transparent'
			}
		},
		methods: {
			getAllUserData(allData) {
				console.info('@getAllUserData', allData)
			},
			// 输入公司名称
			companyInput(e) {
				page = 1;
				reachbottom = false;
				console.log(e.detail.value);
				if (e.detail.value.length > 0) {
					if (this.loadingStatus) {
						var keywords = e.detail.value;
						this.keywords = keywords
						this.companyList = []
						this.companylist(keywords);
					}
				} else {
					this.keywords = '';
					this.ifCompany = false
				}
			},
			//企业列表
			companylist(keywords) {
				this.loadingStatus = false;
				this.$api.companylist({
						companyname: keywords,
						page: page,
						limit: 10
					},
					data => {
						if (data.code == 1) {
							this.ifCompany = true
							this.loadingStatus = true;
							var arr = data.data.map(item => {
								return {
									...item
								}
							})
							if (data.data == '' || data.data.length == 0) {
								reachbottom = true
								uni.showToast({
									title: "已经加载全部",
									icon: "none",
									duration: 500
								});
							} else {
								page++;
								uni.stopPullDownRefresh();
								this.companyList = this.companyList.concat(arr);
							}
							//输入框字数为0时
							if (this.keywords.length == 0) {
								this.companyList = []
								page = 1;
								reachbottom = false;
								this.ifCompany = false;
							}
						}
					})
			},
			// 分页加载公司列表
			bottomHospital(e) {
				if (!reachbottom) {
					this.companylist(this.keywords);
				}
			},
			// 点击选择公司
			selectCompany(id, name) {
				this.company_id = id
				this.companyname = name
				console.log('名称：' + name);
				this.companyList = []
				this.ifCompany = false;
			},
			handleOnCampanyBlur() {
				if(this.companyList.length === 0) this.ifCompany = false // 没有查询到公司列表也取消列表弹窗
			},
			// 获取行业列表
			getIndustryData() {
				this.$api.industryCategoryList({}, res => {
					if(res.code === 1) {
						this.industryList = res.data || []
						// console.info(this.industryList, '===========1111')
					}
				})
			},
			// 选择行业后触发事件回调
			handleOnIndustryChange({detail}) {
				console.info('handleOnIndustryChange: ', detail, this.industry_id)
				// this.industry_id = detail.value
			},
			// 选择地址
			addressClick() {
				console.log('address');
				uni.chooseLocation({
					success: (res) => {
						console.log(res);
						this.address = res.address
					}
				});
			},
			// 选择头像
			chooseFileTest(list, ulist) {
				this.imgList = ulist
				console.log(list)
				this.avatar = list[0]
			},
			// 性别
			// radioChange(e) {
			// 	this.gender = e.detail.value;
			// },
			// 出生日期
			// bindDateChange(e) {
			// 	this.birthday = e.detail.value
			// },
			smartcardfind() {
				this.$api.smartcardfind({},
					data => {
						if (data.code) {
							this.maxCount = 1
							this.userInfo = data.data && data.data.length ? data.data[0] : []
							this.platform_status = this.userInfo.platform_status || 2
							this.name = this.userInfo.name || '' //真实姓名
							this.mobile = this.userInfo.mobile || '' //手机号
							this.email = this.userInfo.email || '' //邮箱
							this.address = this.userInfo.address || '' //地址
							this.wechat = this.userInfo.wechat || '' //微信
							this.position = this.userInfo.position || '' //职位
							this.companyname = this.userInfo.smartcardcompany.name || this.userInfo.companyname || ''
							this.company_id = this.userInfo.company_id || 0
							this.introcontent = this.userInfo.introcontent ||''
							this.ids = this.userInfo.id || 0
							this.industry_id = this.userInfo.industry_id
							this.qq = this.userInfo.qq
							if (this.userInfo) {
								this.imgList[0] = this.userInfo.avatar; //头像
								this.avatar = this.userInfo.avatar;
							} else {
								this.imgList = []
							}
						} else {
							uni.navigateTo({
								url: '../index/index'
							})
						}
					})
			},
			submit() {
				var data = {};
				data['user_id'] = this.user_id
				data['company_id'] = this.company_id || 0 // 企业id 不存在的企业由后端自由生成新企业保存
				data['companyname'] = this.companyname // 公司名称
				data['name'] = this.name //真实姓名
				data['wechat'] = this.wechat //微信号
				data['mobile'] = this.mobile //手机号
				data['email'] = this.email //邮箱
				data['position'] = this.position //职位
				data['address'] = this.address //地址
				data['avatar'] = this.avatar //头像
				data['introcontent'] = this.introcontent;
				data['qq'] = this.qq
				data['industry_id'] = this.industry_id
				
				// data['company_id_s'] = this.company_id_s
				if (this.ids != 0) {
					data['id'] = this.ids
				}
				// if (this.avatar == '') {
				// 	uni.showToast({
				// 		title: '请上传头像',
				// 		icon: 'none'
				// 	})
				// 	return false;
				// }
				if (this.name == '') {
					uni.showToast({
						title: '请填写姓名',
						icon: 'none'
					})
					return false;
				}
				// if (this.company_id == '' || this.company_id == 0) {
				// 	uni.showToast({
				// 		title: '请选择所在公司',
				// 		icon: 'none'
				// 	})
				// 	return false;
				// }
				if(!this.companyname) {
					uni.showToast({
						title: '请填写所在公司',
						icon: 'none'
					})
					return false;
				}
				if (this.position == '') {
					uni.showToast({
						title: '请填写职位',
						icon: 'none'
					})
					return false;
				}
				if (this.industry == '') {
					uni.showToast({
						title: '请填写所在行业',
						icon: 'none'
					})
					return false;
				}
				if (!this.$common.testString(this.mobile, 'mobile')) {
					uni.showToast({
						title: '请正确填写手机号',
						icon: 'none'
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
				if (this.email && !this.$common.testString(this.email, 'mail')) {
					uni.showToast({
						icon: 'none',
						position: 'bottom',
						title: '请正确填写邮箱'
					});
					return false;
				}
				if(this.address==''){
					uni.showToast({
						title:'请选择地址',
						icon:'none'
					})
					return false;
				}	
				this.$api.applyStaffAdd(
					data,
					data => {
						if (data.code) {
							this.$common.successToShow(data.msg, function() {
								uni.navigateBack()
							})
						}
					})
			},
			linkToChange() {
				uni.navigateTo({
					url: '/pages/change/change'
				})
			}
		}
	}
</script>

<style>
	page {
		background: #F6F7F9;
	}

	.header_padding {
		padding: 0 30rpx;
	}

	.overall_padding {
		padding: 30rpx 15rpx;
	}

	.header_background {
		background-repeat: no-repeat;
		background-position: left top;
		background-size: cover;
	}

	/* 实体内容 */
	.padding_content {
		padding: 0rpx 30rpx 108rpx;
	}

	.padding_content .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
		line-height: 32rpx;
		text-align: center;
		color: #999;
	}

	.padding_content .icon-jianhao {
		font-size: 28rpx;
	}

	.change-card-box {
		padding-right: 30rpx;
		height: 104rpx;
	}

	.change-card-box .title2 {
		font-size: 28rpx;
		font-weight: 400;
		color: #333;
	}

	.Inside_fast {
		background: #fff;
		border-radius: 10px;
		margin-top: 30rpx;
		border: 1px solid #eaeaea;
	}

	.change-card-box .title,
	.Inside_fast .title {
		padding: 16rpx 30rpx;
		font-size: 30rpx;
		font-weight: 700;
		color: #333;
	}

	.Inside_fast_padding {
		padding: 30rpx;
		justify-content: space-between;
	}

	.chatIcon image {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		display: block;
	}

	>>>.chatIcon .uploadImg_btn {
		width: 80rpx;
		height: 80rpx;
	}

	.rightIcon {
		margin-left: 30rpx;
	}

	.infoItem {
		justify-content: space-between;
		padding: 40rpx 30rpx;
		border-bottom: 1px solid #eaeaea;
	}

	.infoItem:last-child {
		border: none;
	}

	.rightIcon image {
		width: 12rpx;
		height: 22rpx;
		display: block;
	}

	.left_title {
		width: 140rpx;
	}

	.right_info {
		flex: 1;
		margin-left: 60rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.right_info input {
		width: 100%;
		height: 50rpx;
		color: #333;
		font-size: 28rpx;
		/* text-align: right; */
	}

	.right_info image {
		width: 12rpx;
		height: 22rpx;
		display: block;
		margin-left: 20rpx;
	}

	.right_info textarea {
		width: 100%;
		margin: 10rpx;
		padding: 15rpx;
		background: #f7f7f7;
		border: 1px solid #f3f3f3;
		border-radius: 20rpx;
		font-size: 28rpx;
	}

	.select_radio {
		width: 200rpx;
	}

	.inspect_name {
		font-size: 28rpx;
		color: #333;
		width: 150rpx;
	}

	.sunmit_btn {
		padding: 40rpx;
	}

	.sunmit_btn button {
		color: #fff;
		background: #0084bf;
		font-size: 36rpx;
		height: 74rpx;
		line-height: 74rpx;
		width: 360rpx;
		border-radius: 74rpx;
		margin: 0 auto;
		padding: 0;
		display: block;
	}

	.sunmit_btn button::after {
		display: none;
	}

	.companyContent {
		position: absolute;
		top: 100rpx;
		left: 0;
		width: 100%;
		z-index: 100;
		background: #fff;
		box-shadow: 0 0 10px #ccc;
	}

	.companyList {
		padding: 30rpx;
		color: #333;
		font-size: 28rpx;
		border-bottom: 1px solid #F2F2F2;
	}

	.hospital_body {
		max-height: 400rpx;
		overflow-y: auto;
	}

	.manual_input {
		color: #45cb8c;
		font-size: 24rpx;
		padding: 10rpx 0;
		text-align: center;
	}

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

	.footer {
		height: 108rpx;
		padding: 0 32rpx;
		position: fixed;
		bottom: 0;
		width: 100%;
		background-color: #fff;
		z-index: 999;
	}

	.footer .left-box {
		font-size: 20rpx;
		font-weight: 400;
		color: #666;
	}

	.footer .left-box .iconfont {
		width: 48rpx;
		height: 48rpx;
		font-size: 48rpx;
		color: #0256FF;
	}

	.footer .primary-btn {
		margin-left: 48rpx;
		height: 80rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 80rpx;
		line-height: 80rpx;
		font-size: 28rpx;
		font-weight: 400;
		color: #fff;
		background-color: #0256FF;
		border: solid 1px #0256FF;
	}
	>>> .user-info .input-value-border { border: 0;}
	>>> .user-info .arrow-area {
		transform: rotate(-135deg);
	}
</style>