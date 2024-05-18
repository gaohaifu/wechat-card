<template>
    <view>
        <view style="background-color: white;padding: 30rpx;display: flex;align-items: center">
            <view>
                姓名
            </view>
            <view style="margin-left: 15rpx;">
                <u-input v-model="member.name" ></u-input>
            </view>
        </view>
        <view class="setup-btn" @tap="submit()">保存</view>
    </view>
</template>

<script>
    var _self;
	export default {
		components: {},
		data() {
			return {
                member:[],
			};
		},
		onShow() {
            _self = this;
            this.qylogin();
            _self.member = uni.getStorageSync('userInfo');
		},
		methods: {
            submit() {
                let self = this;
                uni.$u.http.post('/addons/fastscrm/api.user/changename', {
                    nickname: self.member.name
                }).then(res => {
                    uni.$u.toast('操作成功');
                    uni.setStorageSync( 'userInfo',res.data.data.worker)
                }).catch(res => {
                    uni.$u.toast('网络错误');
                })
			},
		}
	};
</script>

<style>
    .tfont{
        font-size: 28rpx;
        color: #6E6564;
        
    }
	.address-form .key-name {
		width: 200rpx;
	}

	.address-form .btn-red {
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, .6);
	}

	.setup-btn {
		position: fixed;
		bottom: 20rpx;
		left: 5%;
		width: 90%;
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 80rpx;
		background-color: #fd3826;
		color: #fff;
		font-size: 30rpx;
		display: flex;
		justify-content: center;
		margin: 0 auto;
	}
</style>
