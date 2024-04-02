<template>
	<view class="page-wrap">
		<view class="flex menu-box">
			<view class="flex-1 flex flex-vc flex-hc menu-item" :class="{'active' : item.value === activeId}"
				v-for="(item, inx) in menu" :key="inx" @click="tabchange(inx)">
				{{item.label}}
			</view>
		</view>
		<view class="scene-box">
			<view class="scene-date">{{ `${systemDate.year}年${systemDate.month}月${systemDate.day}日 ${systemDate.time}`}}</view>
			<view class="scene">
				<image class="scene-photo" src="../../static/images/img.jpg" />
				<view class="scene-text">设置招呼语和分享封面后，您发给微信朋友的名片将展示如下</view>
			</view>
			<view class="scene">
				<image class="scene-photo photo-right" src="../../static/images/img.jpg" />
				<view class="scene-text text-right">
					<view>{{ shareCardInfo.greetings || '这是我的名片，请收下' }}</view>
					<image class="card-img" :src="shareCardInfo.backgroundimage || '../../static/images/mp.jpg'" />
				</view>
			</view>
		</view>
		<view class="setting-box">
			<view class="word-box" @click="openPopup">
				<text>招呼语</text>
				<!-- <input disabled v-model="word" placeholder="请输入招呼语" /> -->
				<view class="flex-1">{{ shareCardInfo.greetings || '请输入招呼语' }}</view>
				<image src="../../static/images/right.png" />
			</view>
			<view>分享封面</view>
			<scroll-view class="cover-box" scroll-x="true">
				<view class="cover-img" :class="{'active': item.active}"
					v-for="(item, index) in backgroundimageList" :key="item.id"
					@click="changeBackgroundImage(index, item)">
					<image :src="item.value" />
					<view class="checked-icon">
						<image src="../../static/images/cover_icon.png" />
					</view>
				</view>
			</scroll-view>
			<view class="save-box">
				<!-- <view class="send-box">
					<image src="../../static/images/send_icon.png" />
					<view>发送名片</view>
				</view> -->
				<button open-type="share" class="send-box">
					<image src="../../static/images/send_icon.png" />
					<view>发送名片</view>
				</button>
				<view class="save-btn" @click="saveShareInfo">保存</view>
			</view>
		</view>
		
		<uni-popup ref="popup" type="bottom">
			<view class="message_content">
				<view class="message_item">
					<view class="title">选择招呼语</view>
					<view class="scroll">
						<label class="flex flex-vc"
							style="margin: 20rpx 0;"
							v-for="(item, index) in greetingsList" :key="item.id"
							@click="changeGreeting(index, item)">
							<view style="margin-right: 20rpx;">
								<radio :value="item.id" :checked="item.active" />
							</view>
							<view v-if="item.id !== 'custom'">{{item.value}}</view>
							<view v-else>
								<textarea class="text-area" name="greeting" cols="30" rows="6"
									 v-model="greeting" placeholder="请输入自定义标题"
									 :maxlength="30"
									 @blur="greetingBlur"></textarea>
							</view>
						</label>
					</view>
				</view>
				<view class="flex">
					<view class="flex-1 cancel" @click="cancelGreeting">取消</view>
					<view class="flex-1 save" @click="cancelGreeting">保存</view>
				</view>
			</view>			
		</uni-popup>
	</view>
</template>

<script>
	export default {
		data() {
			const date = new Date()
			const month = date.getMonth()+ 1
			const day = date.getDay()
			const hour = date.getHours()
			const minutes = date.getMinutes()
			return {
				systemDate: {
					year: date.getFullYear(),
					month:  month > 9 ? month : '0' + month,
					day: day > 9 ? day : '0' + day,
					time: '' + (hour > 9 ? hour : '0' + hour) + ':' +(minutes > 9 ? minutes : '0' + minutes)
				},
				activeId: '01',
				menu: [
					{
						label: '分享卡片设置',
						value: '01'
					},
					{
						label: '分享隐私设置',
						value: '02'
					},
				],
				checkIndex: 1,
				word: '',
				shareCardInfo: {},
				backgroundimageList: [],
				greetingsList: [],
				greeting: '', // 
				userData: {}
			}
		},
		onLoad() {
			this.getShareCardInfo()
		},
		onShareAppMessage(res) {
			if (res.from === 'menu') {// 来自右上角分享按钮
			  console.log(res.target)
			}
			this.sendCard()
			console.info('this.companyInfo', this.companyInfo)
			this.userData = uni.getStorageSync('userData') || {}
			return {
			  title: this.shareCardInfo.greetings,
			  path: '/pages/myCard/myCard?origin=1&isShare=1&staff_id=' + this.userData.id,
			  imageUrl: this.shareCardInfo.backgroundimage,
			  type: 1, // 0正式版 2体验版 1开发板
			}
		},
		methods: {
			sendCard() {
				this.$api.sendCard({
					staff_id: this.userData.id
				}, res => {
					if(res.code === 1) {
						// uni.showToast({
						// 	icon: 'none',
						// 	title: res.msg
						// })
						// this.visits.find(i => i.id === 3).value++
					}
				})
			},
			tabchange (index) {
				this.activeId = this.menu[index].value
			},
			changeBackgroundImage(index, row) {
				this.shareCardInfo.backgroundimage = row.value
				this.backgroundimageList = this.backgroundimageList.map((i, inx) => {
					i.active = false
					if(inx === index) i.active = true
					return i
				})
			},
			greetingBlur() {
				this.shareCardInfo.greetings = this.greeting
			},
			changeGreeting(index,row){
				if(row.id === 'custom') {
					this.shareCardInfo.greetings = this.greeting
					this.shareCardInfo.isCustom = true
				} else {
					this.shareCardInfo.isCustom = false
					this.shareCardInfo.greetings = row.value
				}
				
				this.greetingsList = this.greetingsList.map((i, inx) => {
					i.active = false
					if(inx === index) i.active = true
					return i
				})
			},
			openPopup() {
				this.$refs.popup.open('bottom')
			},
			cancelGreeting() {
				this.$refs.popup.close()
			},
			saveGreeting(greetings) {
				this.$api.saveCustomGreetings({
					custom_greetings: this.greeting
				})
			},
			saveShareInfo() {
				if (this.shareCardInfo.isCustom) this.saveGreeting()
				this.$api.saveShareInfo({
					greetings: this.shareCardInfo.greetings,
					backgroundimage: this.shareCardInfo.backgroundimage
				}, res => {
					if(res.code === 1) {
						uni.showToast({
							icon: 'success',
							title: res.msg
						})
						uni.navigateBack()
					}
				})
			},
			getShareCardInfo() {
				this.$api.shareCardInfo({}, res => {
					if(res.code === 1) {
						const data = res.data || {}
						this.shareCardInfo = data.shareCardInfo || {}
						let greetingsList = []
						for(let key in (data.greetingsList || {})) {
							greetingsList.push({
								id: key,
								active: data.greetingsList[key] === this.shareCardInfo.greetings, 
								value: data.greetingsList[key]
							})
						}
						this.greetingsList = greetingsList
						this.backgroundimageList = (data.backgroundimageList || []).map((i, inx) => {
							return {
								id: inx,
								active: i === this.shareCardInfo.backgroundimage,
								value: i
							}
						})
						// console.info(this.backgroundimageList, this.greetings, this.shareCardInfo, '=======>', res)
					}
				})
			}
		}
	}
</script>

<style>
	.page-wrap {
		background-color: #F6F7F9;
		padding-bottom: 448rpx;
	}
	/* 导航 */
	.menu-box {
		height: 80rpx;
		background-color: #fff;
	}
	
	.menu-box .menu-item {
		position: relative;
		font-size: 28rpx;
		font-weight: 700;
		color: #333;
	}
	
	.menu-box .menu-item.active {
		color: #0256FF;
	}
	
	.menu-box .menu-item.active::after {
		content: '';
		position: absolute;
		bottom: 0;
		left: 50%;
		transform: translateX(-50%);
		width: 112rpx;
		height: 4rpx;
		background-color: #0256FF;
	}
	.scene-box {
		padding: 1rpx 32rpx;
		background-color: #F6F7F9;
	}
	.scene-date {
		text-align: center;
		font-size: 24rpx;
		line-height: 28rpx;
		color: #999;
		margin-top: 16rpx;
		margin-bottom: 24rpx;
	}
	.scene {
		display: flex;
		margin-bottom: 44rpx;
	}
	.scene-photo {
		flex-shrink: 0;
		width: 80rpx;
		height: 80rpx;
		border-radius: 40rpx;
		margin-right: 20rpx;
	}
	.scene-text {
		flex: 1;
		margin-right: 104rpx;
		padding: 16rpx;
		background-color: #fff;
		margin-top: 8rpx;
		font-size: 28rpx;
		color: #333;
		line-height: 40rpx;
		border-radius: 16rpx;
		overflow: hidden;
	}
	.photo-right {
		margin-right: 0;
		margin-left: 20rpx;
		order: 2;
	}
	.text-right {
		margin-right: 0;
		margin-left: 104rpx;
		text-align: right;
		padding-top: 20rpx;
	}
	.card-img {
		max-width: 100%;
		height: 340rpx;
		margin-top: 16rpx;
	}
	.setting-box {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		padding: 0 32rpx;
		background-color: #fff;
		box-sizing: border-box;
		font-size: 28rpx;
		color: #999;
	}
	.word-box {
		display: flex;
		align-items: center;
		height: 88rpx;
		line-height: 88rpx;
		border-bottom: 2rpx solid #E6E6E6;
		margin-bottom: 24rpx;
	}
	.word-box text {
		white-space: nowrap;
		margin-right: 16rpx;
	}
	.word-box input {
		flex: 1;
		color: #333;
	}
	.word-box image {
		flex-shrink: 0;
		width: 11rpx;
		height: 19rpx;
		margin-left: 16rpx;
	}
	.cover-box {
		white-space: nowrap;
		width: 100%;
		margin: 24rpx 0;
	}
	.cover-img {
		position: relative;
		display: inline-block;
		width: 200rpx;
		height: 142rpx;
		margin-right: 20rpx;
	}
	.cover-img image{
		width: 100%;
		height: 100%;
	}
	.checked-icon {
		display: none;
		position: absolute;
		right: 0;
		bottom: 0;
		width: 64rpx;
		height: 64rpx;
	}
	.checked-icon image {
		width: 100%;
		height: 100%;
	}
	.cover-img.active .checked-icon {
		display: block;
	}
	.save-box {
		display: flex;
		align-items: center;
		padding: 0 32rpx;
		height: 108rpx;
		margin-bottom: 24rpx;
	}
	.send-box {
		flex-shrink: 0;
		margin-right: 20rpx;
		font-size: 20rpx;
		color: #666;
		text-align: center;
		padding: 0;
		background-color: transparent;
		line-height: normal;
	}
	.send-box image {
		width: 48rpx;
		height: 48rpx;
	}
	.save-btn {
		flex: 1;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		background: #0256FF;
		font-size: 28rpx;
		color: #fff;
		border-radius: 60rpx;
	}
	
	/* 弹窗 */
	.message_content .cancel,
	.message_content .save{
		border-top: solid 2rpx #e6e6e6;
		text-align: center;
		padding: 30rpx ;
		background-color: #fff;
	}
	.message_content .save {
		background-color: #0256FF;
		color: #fff;
	}
	.message_content{
		background: #fff;
	}
	.message_content .message_item {
		padding: 20rpx 20rpx;
	}
	.message_content .title {
		padding: 10rpx 0 30rpx;
		font-size: 36rpx;
		text-align: center;
		font-weight: 700;
	}
	.message_content .text-area{
		border: solid 1px #ddd;
		border-radius: 8rpx;
	}
	.scroll_view{ height: 600rpx;}

</style>