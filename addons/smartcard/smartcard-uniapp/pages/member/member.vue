<!-- 成员列表 -->
<template>
	<view>
		<view class="flex_between focus-box">
			<text>关注公众号，及时查收成员申请</text>
			<view class="primary-btn">立即关注</view>
		</view>
		<view class="flex menu-box">
			<view class="flex-1 flex flex-vc flex-hc menu-item" :class="{'active' : item.value === activeId}"
				v-for="(item, inx) in menu" :key="inx" @click="tabChange(inx)">
				{{item.label}}
			</view>
		</view>
		<view class="flex flex-vc search-box">
			<icon type="search" size="16" color="#858585" style="margin-right: 8rpx;" />
			<input v-model="keyword" class="uni-input" placeholder="请输入姓名/公司/职位" @input="doSearch" />
		</view>
		<view class="flex more-search-box">
			<view class="btn01" @click="showAll">全部模板</view>
			<view class="flex flex-vc btn02" @click="openPopup">
				{{themename ? themename : '按模板筛选'}}
				<text class="iconfont icon-down" v-show="themename"></text>
			</view>
		</view>
		<view class="flex flex-hsb member-list" v-for="(item, index) in list" :key="index">
			<image class="avatar" :src="item.avatar" mode=""></image>
			<view class="flex-1 flex flex-vc right-box">
				<view class="flex-1">
					<view class="flex flex-vc">
						<text class="name">{{ item.name }}</text>
						<text class="tag" v-if="item.is_owner === 1">{{ item.is_owner === 1 ? '超级管理员' : '' }}</text>
					</view>
					<view class="position">{{ item.position }}</view>
					<view class="muban">{{ item.companyname }}</view>
				</view>
				<view class="more-box">
					<text class="iconfont icon-gengduo" @click="showOperate(item)"></text>
					<view class="more-text" v-if="activeId === '2' && item.show">
						<text class="iconfont icon-sanjiaoxing"></text>
						<text @click="agree(item)">通过</text>
						<text @click="reject(item)">拒绝</text>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="status"></uni-load-more>
		
		
		<uni-popup ref="popup" type="bottom">
			<view class="message_content">
				<view class="message_item">
					<view class="title">选择主题</view>
					<view class="scroll">
						<scroll-view class="scroll_view" scroll-y="true">
							<view class="flex flex-vc" style="margin-bottom: 20rpx;"
								v-for="(item,index) in templet1" :key="index" @click="activeTab(index,item)">
								<view class="scroll-item">
									<image :src="cdnUrl+item.cardimage" mode=""></image>
									<view class="zzc" v-if="item.active"></view>
									<view class="active" v-if="item.active">
										<view :style="{color:color}" class="iconfont icon-dui"></view>
									</view>
								</view>
								<text class="flex-1">{{item.name}}</text>
							</view>
						</scroll-view>
					</view>
				</view>
			</view>
			<view class="cancel">取消</view>
		</uni-popup>
	</view>
</template>

<script>
	import { debounce } from '@/config/common.js'
	import {
		cdnUrl
	} from '@/config/config.js'
	export default {
		data() {
			return {
				cdnUrl: cdnUrl,
				activeId: '1',
				menu: [{
						label: '已加入成员',
						value: '1'
					},
					{
						label: '申请加入',
						value: '2'
					},
					{
						label: '等待激活',
						value: '3'
					},
				],
				list: [],
				keyword: '',
				page: 1,
				status: 'more', // 值还有loading，noMore
				templet1:[],
				themename: ''
			}
		},
		onLoad(options) {
			if(options.type) this.activeId = options.type
			this.getMembers()
			this.themeList()
		},
		onReachBottom() {
			++this.page
			this.getMembers()
		},
		methods: {
			// 接口还不支持搜索，参数不够
			doSearch: debounce(() => {
				console.log('搜索')
				this.page = 1
				this.getMembers()
			}, 500),
			getMembers() {
				this.status = 'loading'
				this.$api.getMemberList({
					page: this.page, 
					type: this.activeId,
					theme_id: this.themeid || '',
					keyword: this.keyword
				}, 
				res => {
					console.log(res, '成员')
					if(res.code === 1) {
						res.data = (res.data || []).map(i => {
							i.show = false
							return i
						})
						this.status = res.data.length < 10 ? 'noMore' : 'more' // nomore待处理
						this.list = this.list.concat(res.data)
					}else {
						this.status = 'more'
					}
				},
				err => {
					this.status = 'more'
				})
			},
			showOperate(row) {
				const temp = row.show
				this.list.forEach(it => {
					it.show = false
				})
				row.show = !temp
				console.info(row)
			},
			agree(row) {
				row.show = false
				this.doOperate(row, '1')
			},
			reject(row) {
				row.show = false
				this.doOperate(row, '2')
			},
			doOperate(row, type) {
				uni.showModal({
					title: '提示',
					content: `请确认是否${type == '1' ? '通过' : '拒绝'}申请`,
					showCancel:true,
					success: (res) => {
						console.info(res)
						if (res.confirm) {
							this.$api.agreeApply({
								staff_id: row.staff_id,
								type: type
							}, res => {
								if (res.code == '1') {
									uni.showToast({
										icon: 'none', 
										text: type == '1' ? '同意申请通过' : '拒绝申请'
									})
									this.getMembers()
								}
							})
						}
					}
				})
			},
			resetData() {
				const ops = this.$options.data()
				this.status = ops.status
				this.list = ops.list
				this.page = ops.page
			},
			tabChange(index) {
				const tab = this.menu[index]
				if (this.activeId === tab.value) return
				this.activeId = tab.value
				this.page = 1
				this.themeid = ''
				this.themename = ''
				this.keyword = ''
				this.resetData()
				this.getMembers()
			},
			showAll() {
				this.themename = ''
				this.activeTab(-1, {})
				this.getMembers()
			},
			// 点击模板
			activeTab(index,row){
				this.themeid = row.id
				this.themename = row.name
				this.templet1 = this.templet1.map((i, inx) => {
					i.active = false
					if(inx === index) i.active = true
					return i
				})
				this.$refs.popup.close()
				// 发起搜索
				this.page = 1
				this.getMembers()
			},
			openPopup() {
				this.$refs.popup.open('bottom')
			},
			//主题列表
			themeList(){
				const parm={
					
				};
				this.$api.themeList(
				parm,
				data => {
					if(data.code==1){
						this.templet1=data.data.map(item=>{
							return{
								active: false,
								...item
							}
						})
					}else{
						this.$common.errorToShow(data.msg,function(){
						})
					}
				})
			}
		}
	}
</script>

<style>
	@import url('../../common/common.css');
/* 弹窗 */
	.cancel {
		border-top: solid 10rpx #e6e6e6;
		text-align: center;
		padding: 30rpx ;
		background-color: #fff;
	}
	.message_content{
		padding: 20rpx 20rpx;
		background: #fff;
	}
	.message_content .title {
		padding: 20rpx 0;
	}
	.scroll_view{ height: 600rpx;}
	.scroll-item{display: inline-block; margin: 0 12rpx; position: relative;}
	.scroll-item>image{width: 145rpx; height: 85rpx; display: block; border-radius: 10rpx;}
	.scroll_view .zzc{position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99; border-radius: 10rpx;}
	.scroll_view .active{position: absolute; left: 50%; top: 50%; width: 50rpx; height: 50rpx; border-radius: 50%; background: rgba(255,255,255,0.8); transform: translate(-50%,-50%); display: flex; align-items: center; justify-content: center; z-index: 100;}
	.scroll_view .active view{display: block; font-size: 30rpx;}
	
	.focus-box {
		height: 88rpx;
		padding: 0 32rpx;
		background-color: rgba(2, 86, 255, 0.10);
		font-size: 28rpx;
		font-weight: 400;
		color: #0256FF;
	}

	.focus-box .primary-btn {
		width: 160rpx;
		height: 48rpx;
		cursor: pointer;
		text-align: center;
		border-radius: 48rpx;
		line-height: 48rpx;
		font-size: 24rpx;
		font-weight: 400;
		color: #fff;
		background-color: #0256FF;
		border: solid 2rpx #0256FF;
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
		width: 80%;
		height: 4rpx;
		background-color: #0256FF;
	}

	/* 搜索框 */
	.search-box {
		margin: 24rpx 32rpx 0;
		height: 64rpx;
		padding: 0 16rpx;
		border-radius: 64rpx;
		background-color: #F6F7F9;
	}

	/* 更多搜索条件 */
	.more-search-box {
		margin: 36rpx 32rpx 16rpx;
	}

	.more-search-box .btn01,
	.more-search-box .btn02 {
		margin-right: 16rpx;
		height: 64rpx;
		padding: 0 32rpx;
		border-radius: 64rpx;
		font-size: 24rpx;
		font-weight: 400;
	}

	.more-search-box .btn01 {
		background-color: #E5EEFF;
		color: #0256FF;
		line-height: 64rpx;
	}

	.more-search-box .btn02 {
		background-color: #F2F2F2;
		color: #333;
	}

	.more-search-box .btn02 .iconfont {
		margin-left: 6rpx;
		width: 24rpx;
		height: 24rpx;
		line-height: 28rpx;
		font-size: 24rpx;
		text-align: center;
	}

	/* 列表 */
	.member-list {
		margin: 0 32rpx;
	}

	.member-list .avatar {
		margin-right: 32rpx;
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
	}

	.member-list .right-box {
		padding-bottom: 24rpx;
		border-bottom: 2rpx solid #E6E6E6;
	}

	.member-list .iconfont {
		width: 32rpx;
		height: 32rpx;
		font-size: 32rpx;
		color: #666;
		line-height: 32rpx;
		text-align: center;
	}

	.member-list .name {
		margin-right: 6rpx;
		font-size: 32rpx;
		font-weight: 600;
		color: #333;
	}

	.member-list .tag {
		padding: 0 12rpx;
		height: 32rpx;
		line-height: 32rpx;
		font-size: 18rpx;
		color: #6C450E;
		border-radius: 8rpx;
		background-image: linear-gradient(to right, #FAD495, #E9B660);
	}

	.member-list .position,
	.member-list .muban {
		line-height: 40rpx;
		font-size: 24rpx;
		font-weight: 400;
		color: #666;
	}
	.more-box {
		position: relative;
	}
	.more-box .more-text {
		position: absolute;
		top: 50rpx;
		width: 160rpx;
		padding: 4px 6px;
		border: solid 1px #eee;
		right: 0;
		display: flex;
		justify-content: space-between;
		font-size: 24rpx;
		color: red;
	}
	.more-box .icon-sanjiaoxing {
		position: absolute;
		right: 8rpx;
		top: -24rpx;
		width: 24rpx;
		height: 24rpx;
		color: #eee;
	}
	
</style>