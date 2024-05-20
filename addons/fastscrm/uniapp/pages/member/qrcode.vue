<template>
	<view style="width: 100%">
        <view style="padding: 30rpx" class="fsc">
            <image :src="member.qr_code" style="width: 100%" mode="widthFix"></image>
        </view>
    </view>
</template>

<script>
    var _self;
	export default {
		data() {
			return {
			    member:[],

			}
		},
		onLoad() {
		    _self = this;
		    this.qylogin();
		},
        onShow() {
            _self.member = uni.getStorageSync('userInfo');
        },
        onReady() {

        },
		methods: {
		}
	}
</script>

<style lang='scss'>

</style>
