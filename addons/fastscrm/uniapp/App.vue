<script>
	export default {
		onLaunch: function() {
			// console.log('App Launch')
            uni.removeStorageSync('userInfo');
		},
		onShow: function() {
			// console.log('App Show')
		},
		onHide: function() {
			// console.log('App Hide')
		}
	}
</script>

<style>
	/*每个页面公共css */
    @import 'selfdiy.css';
    page{
        background-color: #F0F0F0;
    }
    view{
        box-sizing: border-box;
        font-size: 28rpx;
    }
    .ld0{
        background-color: #0085f7;
    }
    .ld1{
        background-color: #f8a436;
    }



</style>
<style lang="scss">
    @import "uni_modules/uview-ui/index.scss";
</style>