<template>
    <view style="text-align: center">
        <view style="margin-top: 300rpx;color: #b8bcc6;font-size: 36rpx">
            请先绑定门店再操作！
        </view>
        <u-empty
                mode="permission"
                icon="http://cdn.uviewui.com/uview/empty/permission.png"
        >
        </u-empty>
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
        onLoad() {
            _self = this;
        },
		onShow() {
            _self.qylogin();
            _self.member = uni.getStorageSync('userInfo');
		},
		methods: {
            submit() {
                uni.$u.http.post('/addons/fastscrm/api.user/changename', {
                    nickname: _self.member.name
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
</style>
