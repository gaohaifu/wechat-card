<template>
    <view style="width: 100%">
        <u-datetime-picker  @cancel="timeshow=false" v-model="ctime" @confirm="timeconfirm" :show="timeshow" mode="year-month"></u-datetime-picker>
        <view :class="member.leadertype==1?'ld1':'ld0'" class="df ac p30" @click="timeshow=true">
            <view >
                <u-icon name="calendar" size="28" color="white"></u-icon>

            </view>
            <view class="df ac mf15" style="color: white;">
                {{ptime}} <u-icon name="arrow-down-fill" color="white"></u-icon>
            </view>

        </view>

        <view class="hlbblock30">
            <view class="section" >
                {{ptime}}数据统计
            </view>
            <view class="fsc" style="width: 90%;margin:40rpx auto">
                <view  class="dfitem">
                    <view class="itemfont">
                        {{cus_data.cus_num?cus_data.cus_num:0}}
                    </view>
                    <view>
                        招募（人）
                    </view>
                </view>
                <view  class="dfitem">
                    <view class="itemfont">
                        {{cus_data.cus_lose?cus_data.cus_lose:0}}
                    </view>
                    <view>
                        流失（人）
                    </view>
                </view>
            </view>
            <u-subsection @change="subchange" :list="tablist" :current="current" activeColor="#3492ff"></u-subsection>
        </view>
        <view class="hlbblock30" v-if="member.leadertype==1">
            <u-button @click="totask" type="warning"  text="营销任务"></u-button>
        </view>
        <view class="hlbblock30" v-for="(item,index) in workers" :key="index">
            <view class="df ac">
                <view>
                    <view>
                        名次：{{index+1}}
                    </view>
                    <view style="color: #afafaf" v-if="current==0">
                        {{' 招募'+item.cus_num}}人
                    </view>
                    <view style="color: #afafaf" v-else>
                        {{' 流失'+item.cus_lose}}人
                    </view>
                </view>
                <view class="mf30">
                    <u-avatar :src="item.avatar"  size="50"></u-avatar>
                </view>
                <view class="mf30">
                    {{item.name}}
                </view>
            </view>
        </view>
    </view>
</template>
<script>
    var _self;
    export default {
        data() {
            return {
                member:[],
                timeshow:false,
                choseyear:'',
                chosemonth:'',
                chosetime:0,
                tablist: ['招募榜', '流失榜'],
                current:0,
                ctime:'',
                ptime:'',
                cus_data:[],
                workers:[],


            }
        },
        onLoad() {
            _self = this;
            this.qylogin();
            let nowDate = new Date();
            _self.choseyear = nowDate.getFullYear();
            _self.chosemonth = nowDate.getMonth()+1;
            _self.chosemonth= _self.chosemonth < 10 ?  '0'  + _self.chosemonth: _self.chosemonth;
            _self.ctime =  _self.choseyear+'-'+_self.chosemonth;
            _self.ptime = _self.ctime;
        },
        onShow() {
            this.checkstore();
            _self.member = uni.getStorageSync('userInfo');
            _self.member.leadertype==1?uni.setNavigationBarColor({
                frontColor: '#ffffff',
                backgroundColor: '#f8a436',
            }):uni.setNavigationBarColor({
                frontColor: '#ffffff',
                backgroundColor: '#0085f7',
            });
            _self.getdata();
            _self.getrank();
        },
        onReady() {

        },
        methods: {
            totask(){
                uni.navigateTo({
                    url:'/pages/task/index'
                })

            },
            subchange(e){
                _self.current =e;
                _self.getrank();
            },

            timeconfirm(e) {
                let self = this;
                self.timeshow = false;

                if (!uni.$u.test.number(e.value)){
                    uni.$u.toast('未进行选择');
                    return;
                }
               _self.ptime =  uni.$u.timeFormat(e.value, 'yyyy-mm');
                _self.getdata();
                _self.getrank();
            },
            getdata(){
                uni.showLoading({
                        'title':'加载中',
                });
                uni.$u.http.post('/addons/fastscrm/api.user/customerdata', {
                    dat: _self.ptime
                }).then(res => {
                    uni.hideLoading()
                    _self.cus_data = res.data.data.cus_data
                }).catch(res => {
                    uni.$u.toast('网络错误');
                })
            },
            getrank(){
                uni.showLoading({
                    'title':'加载中',
                });
                uni.$u.http.post('/addons/fastscrm/api.user/getrank', {
                    dat: _self.ptime,
                    current:_self.current
                }).then(res => {
                    uni.hideLoading()
                    _self.workers = res.data.data.workers
                }).catch(res => {
                    uni.$u.toast('网络错误');

                })
            },
        }
    }
</script>
<style lang='scss'>
    .dfitem{
        width: 50%;
        text-align: center;
        position: relative;
        .itemfont{
            font-size: 55rpx;
            font-weight: bold;
        }
    }
</style>
