<template>
	<view style="width: 100%">
        <view :class="member.leadertype==1?'ld1':'ld0'"  class="fsc p30">
            <view class="df">
                <image src="/static/images/store.png" style="width: 50rpx" mode="widthFix"></image>
                <view class="mf15 store_font" >
                    {{store.store_name||'选择门店'}}
                </view>
            </view>
            <view @click="updatemember()" class="df ac">
                <view style="color: white;font-size: 28rpx">
                  {{  member.leadertype==1?'店长端':'导购端'}}
                </view>
                <view class="mf15">
                    <u-icon name="grid" size="28" color="white"></u-icon>
                </view>
            </view>
        </view>

        <view class="hlbblock df" style="padding:30rpx 20rpx ;">
                <view>
                    <u-avatar :src="member.avatar" shape="square" size="70"></u-avatar>
                </view>
                <view class="mf30">
                    <view>
                        <text style="font-size: 35rpx;font-weight: bold">{{member.name}}</text>
                    </view>
                    <view class="mt30 df">
                        <u-tag :text="store.store_name||'选择门店'"  color="white" type="success" mode="dark" shape="circle" bg-color="#ff2c5b" borderColor="#ff2c5b" />
                    </view>
                </view>
        </view>
        <view class="hlbblock30">
            <view class="section">
                我的业绩
            </view>
            <view class="fsc" style="width: 90%;margin:40rpx auto">
                <view  class="dfitem">
                    <view class="itemfont">
                       {{cus_data.cus_num?cus_data.cus_num:0}}
                    </view>
                    <view>
                        今日新加（人）
                    </view>
                </view>

                <view  class="dfitem">
                    <view class="itemfont">
                        {{cus_data.cus_lose?cus_data.cus_lose:0}}
                    </view>
                    <view>
                        今日流失（人）
                    </view>
                </view>
            </view>


        </view>

        <view class="hlbblock30" v-if="member.leadertype==1">
            <view class="section" style="margin-bottom: 20rpx">
                今日排名
            </view>
            <view  v-for="(item,index) in workers" :key="index">
                <view class="df ac mt15" >
                    <view>
                        <view>
                            名次：{{index+1}}
                        </view>
                        <view style="color: #afafaf">
                            {{' 招募'+item.cus_num}}人
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
        <u-popup :show="leadershow" @close="leadershow = false" mode="center" round="10"  >
            <view class="popbg">
                <view class="title" >
                    选择身份
                </view>
                <view class="p20">
                    <view  @click="setleadertype(0)" class="popitem" style="background-color: #0085f7">
                        导购
                    </view>
                    <view @click="setleadertype(1)" v-if="member.store_leader==1" class="popitem" style="background-color: #f8a436">
                        店长
                    </view>
                </view>

            </view>
        </u-popup>
    </view>
</template>

<script>
    var _self;
	export default {
		data() {
			return {
			    member:[],
                store:[],
                leadershow:false,
                cus_data:[],
                workers:[],
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

            if (_self.member.id>0){
                this.getdata();
                _self.getrank();
            }



        },
        onReady() {

        },
		methods: {
            getrank(){

                uni.showLoading({
                    'title':'加载中',
                });
                uni.$u.http.post('/addons/fastscrm/api.user/getrank', {
                    dat: '',
                    current:0
                }).then(res => {

                    uni.hideLoading()
                    _self.workers = res.data.data.workers
                }).catch(res => {
                    uni.$u.toast('网络错误');
                })
            },

            getdata(){
                uni.$u.http.post('/addons/fastscrm/api.user/customerdata', {
                    dat: ''
                }).then(res => {

                   _self.cus_data = res.data.data.cus_data
                }).catch(res => {
                    console.log(res)
                    uni.$u.toast('网络错误');
                })
            },


		    updatemember(){
                uni.$u.http.post('/addons/fastscrm/api.user/updatemember', {
                }).then(res => {
                    res.data.data.worker.leadertype = _self.member.leadertype;
                    uni.setStorageSync( 'userInfo',res.data.data.worker)
                    _self.member = res.data.data.worker
                    _self.leadershow=true
                }).catch(res => {
                    uni.$u.toast('网络错误');
                })
            },
            setleadertype(e){
		        console.log(e)
                _self.member.leadertype =e;
                uni.setStorageSync( 'userInfo',_self.member)
                e==1?uni.setNavigationBarColor({
                        frontColor: '#ffffff',
                        backgroundColor: '#f8a436',

                    }):uni.setNavigationBarColor({
                    frontColor: '#ffffff',
                    backgroundColor: '#0085f7',

                })
                _self.leadershow=false
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
    .store_font{
        font-size: 32rpx;
        font-weight: bold;
        color: white
    }

    .popbg{
        width: 540rpx;
        .title{
            margin-top: 30rpx;
            color: #b8bcc6;
            font-size: 36rpx;
            text-align: center;
        }
    }
    .popitem{
        text-align: center;
        border-radius: 10rpx;
        width: 300rpx;
        margin:30rpx auto;
        color: white;
        line-height: 80rpx;
    }

</style>
