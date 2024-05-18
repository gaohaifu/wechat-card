<template>
    <view style="">
        <view class="hlbblock" @click="chose(item)" v-for="(item, index) in stores" style="margin: 15rpx auto;padding: 30rpx" >
                <view class="linedot" style="font-weight: bold">
                    {{item.store_name}}
                </view>
            <view style="font-size: 25rpx;color: #999999">
                {{item.area}}/{{item.address}}
            </view>
        </view>
    </view>
</template>

<script>
    var _self;
	export default {
		components: {},
		data() {
			return {
                member:[],
                stores:[],
			};
		},
		onShow() {
            _self = this;
            this.qylogin();
            uni.$u.http.post('/addons/fastscrm/api.user/getstore', {
            }).then(res => {
                if (res.data.code==0){
                    uni.showModal({
                        title: '提示',
                        content: res.data.msg,
                        showCancel:false,
                        success: function (res) {
                            uni.reLaunch({
                                'url':'/pages/member/needbind'
                            })
                        }
                    });
                }else{
                    _self.stores = res.data.data.stores;
                }
            }).catch(res => {
                uni.$u.toast('网络错误');
            })


		},
		methods: {
		    chose:function (item) {
                uni.setStorageSync( 'storeInfo',item);
                uni.reLaunch({
                    'url':'/pages/index/index'
                })
            }
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
