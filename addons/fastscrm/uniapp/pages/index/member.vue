<template>
    <view >
        <view :class="member.leadertype==1?'ld1':'ld0'" class="p30 df ac" >
            <view>
                <u-avatar :src="member.avatar" shape="square" size="70"></u-avatar>
            </view>
            <view class="mf30">
                <view>
                    <text class="membertitle">{{member.name}}</text>
                </view>
            </view>
        </view>
        <view class="hlbblock30">
            <navigator url="/pages/member/storebind" class="fsc" style="margin: 20rpx 0">
                <view class="df ac">
                    <view>
                        <u-icon name="map-fill" size="25"></u-icon>
                    </view>
                    <view class="mf15">
                        {{store.store_name}}
                    </view>
                </view>
                <view>
                    <u-icon name="arrow-right"></u-icon>
                </view>
            </navigator>
            <navigator url="/pages/member/qrcode" class="fsc"  style="margin: 40rpx 0">
                <view class="df ac">
                    <view>
                        <u-icon name="scan" size="25"></u-icon>
                    </view>
                    <view class="mf15">
                        导购二维码
                    </view>
                </view>
                <view>
                    <u-icon name="arrow-right"></u-icon>
                </view>
            </navigator>
            <navigator url="/pages/member/set" class="fsc"  style="margin: 20rpx 0">
                <view class="df ac">
                    <view>
                        <u-icon name="setting" size="25"></u-icon>
                    </view>
                    <view class="mf15">
                        设置
                    </view>
                </view>
                <view>
                    <u-icon name="arrow-right"></u-icon>
                </view>
            </navigator>
        </view>
    </view>
</template>
<script>
    var _self;
    export default {
        data() {
            return {
                member:[],
                store:[],
            }
        },
        onLoad() {
            _self = this;
            this.qylogin();
        },
        onShow() {
            this.checkstore();
            _self.member = uni.getStorageSync('userInfo');
            _self.store = uni.getStorageSync('storeInfo');
            _self.member.leadertype==1?uni.setNavigationBarColor({
                frontColor: '#ffffff',
                backgroundColor: '#f8a436',
            }):uni.setNavigationBarColor({
                frontColor: '#ffffff',
                backgroundColor: '#0085f7',
            })

        },
        methods: {
        }
    }
</script>
<style lang='scss'>
    .membertitle{
        font-size: 35rpx;
        font-weight: bold;
        color: white
    }

</style>
