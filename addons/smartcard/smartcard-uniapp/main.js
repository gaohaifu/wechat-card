import Vue from 'vue'
import App from './App'

/* 挂载原型 */
Vue.config.productionTip = false
App.mpType = 'app'
 
import cuCustom from './colorui/components/cu-custom.vue'
Vue.component('cu-custom',cuCustom)

import * as Api from './config/api.js' 
import * as Common from './config/common.js' 
import * as Db from './config/db.js'
import * as Config from './config/config.js'


Vue.prototype.$api = Api;
Vue.prototype.$common = Common;
Vue.prototype.$db = Db;
Vue.prototype.$config = Config;


const app = new Vue({
    ...App
})
app.$mount()
