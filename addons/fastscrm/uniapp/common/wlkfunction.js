/*jshint esversion: 6 */
import Request from '../uni_modules/uview-ui/libs/luch-request';
const http = new Request();
exports.install = function (Vue, options) {
    Vue.prototype.isLogin=()=>{
        let _this = this;
        // 判断缓存中是否登录过，直接登录
        const value = uni.getStorageSync('userInfo');
        console.log('value',value)
            if (Object.keys(value).length>0) {
                return true;
                //有登录信息
                // console.log("已登录用户：",value);

            }else{
                http.post('/addons/wxcoupon/api.user/getmember', {
                }).then(function (res) {
                    // console.log(res)
                    // uni.hideLoading()
                    if (res.data.code==0){
                        uni.removeStorage({
                            key: 'userInfo'
                        })
                        uni.redirectTo({
                            url: '/pages/login/index'
                        })
                        return false;
                    }else{
                        return true;
                    }
                }).catch(function (error) {
                })
            }

    }


    
};

