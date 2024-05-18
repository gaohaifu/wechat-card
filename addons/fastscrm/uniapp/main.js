/*jshint esversion: 6 */
import Vue from 'vue'
import App from './App'

import uView from 'uni_modules/uview-ui'
Vue.use(uView)
import * as wlk from 'common/wlkfunction'
Vue.use(wlk)
import share from './common/share.js'
Vue.mixin(share)
import Request from '@/uni_modules/uview-ui/libs/luch-request';
const http = new Request();
Vue.prototype.qylogin = function() {
    let value = uni.getStorageSync('userInfo');
    if (Object.keys(value).length>0) {
        return true;
    }else{
        wx.qy.login({
            success: function(res) {
                // console.log(res)

                if (res.code) {
                    //发起网络请求
                    uni.$u.http.post('/addons/fastscrm/api.user/login', {
                        code: res.code,
                    }).then(res => {
                        // console.log(res)


                        if (res.data.code==0){
                            uni.$u.toast(res.data.msg);
                            uni.reLaunch({
                                'url':'/pages/member/needlogin'
                            })
                        }else{
                            res.data.data.worker.leadertype =0
                            uni.setStorageSync( 'userInfo',res.data.data.worker)
                            uni.reLaunch({
                                'url':'/pages/index/index'
                            })
                        }
                    }).catch(res => {
                        uni.$u.toast('数据错误');
                        console.log(res)
                    })
                } else {
                    console.log('登录失败！' + res.errMsg)
                }
            }
        });
    }
};

Vue.prototype.checkstore = function() {
    let userInfo = uni.getStorageSync('userInfo');
    if (!Object.keys(userInfo).length>0) {
        return;
    }
    setTimeout(function () {
        let value = uni.getStorageSync('storeInfo');
        if (Object.keys(value).length>0) {
            return true;
        }else{
            uni.$u.http.post('/addons/fastscrm/api.user/getstore', {
            }).then(res => {
                console.log(res)
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
                    uni.reLaunch({
                        'url':'/pages/member/storebind'
                    })
                }

            }).catch(res => {
                uni.$u.toast('网络错误');
            })
        }
    },1000)

};



// #ifndef VUE3

Vue.config.productionTip = false
App.mpType = 'app'
const app = new Vue({
    ...App
})


// 引入请求封装
require('./util/request/index')(app)



app.$mount()
// #endif

// #ifdef VUE3
import { createSSRApp } from 'vue'
export function createApp() {
  const app = createSSRApp(App)
  return {
    app
  }
}
// #endif
