<template>
    <view class="content">
        <view style="background-color: white;padding: 30rpx;">
            <view>
                <u-search  margin="10rpx"  placeholder="搜索名称" :animation="true" shape="square" v-model="keyword" height="80" @change="change" @search="search" @custom="search"></u-search>
            </view>
            <view class="nav">
                <view class="nav_item"  v-for="(item, index) in list">
                    <view class="nav_info">
                        <view style="display: flex;align-items: center">
                            <view>
                                <u-avatar :src="item.avatar" shape="circle" size="70"></u-avatar>
                            </view>
                            <view style="margin-left: 15rpx">
                                <view style=" text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">{{item.name}}</view>
                                <!--<view style="font-size: 28rpx">联系电话：{{item.mobile}}</view>-->
                            </view>
                        </view>
                        <view style="margin-top: 30rpx;display: flex;align-items: center;justify-content: space-between;font-size: 25rpx">
                            <view style="color: #999;" >添加时间：{{item.fl_createtime | date}}</view>
                        </view>
                    </view>
                </view>
            </view>
            <u-loadmore :status="status" />
        </view>
    </view>
</template>

<script>
    export default {
        data() {
            return {
                keyword: '',
                status: 'loadmore',
                list: {},
                page: 1,
                total:0,
                lat:0,
                lng:0
            };
        },
        onLoad(e) {
            let self=this;
            this.qylogin();

            self.getlist();
        },
        onShow() {
            this.checkstore();
            let self=this;
        },
        onReachBottom() {
            if(this.status == "nomore"){
                return;
            }
            this.getlist();
        },
        methods: {
            search(){
                this.page=1;
                this.total=0;
                this.getlist();
            },
            change(){
                let self=this;
                if(self.keyword==''){
                    this.page=1;
                    this.total=0;
                    self.getlist();
                }
            },

            getlist(){
                let self=this;
                if(self.page == 1){
                    self.list = [];
                }
                this.status = 'loading';
                uni.$u.http.post('/addons/fastscrm/api.user/getcustomer', {
                    page:self.page,
                    keyword:self.keyword,
                }).then(res => {
                    console.log(123)
                    console.log(res)
                    this.status = 'loadmore';
                    if(res.data.data.total >0){
                        self.status = "loadmore";
                        if( res.data.data.total < res.data.data.pagesize){
                            self.status = "nomore";
                        }
                        self.list = self.list.concat(res.data.data.list);
                        self.page++;
                    }else{
                        self.status = "nomore";
                    }
                }).catch(res => {
                    self.$u.toast('网络错误');
                })
            }
        }
    }
</script>

<style lang="scss">
    page{
    }
    .content {
        position: relative;
        .head{
            width: 100%;
            position: absolute;
            top: 220rpx;
            z-index: 10;
            background: rgba(0, 0, 0, 0.5) none repeat scroll 0 0 !important;
            height: 140rpx;
            display: flex;
            align-items: center;
        }


        .head_btn{
            color: white;
            width: 30%;
            padding: 20rpx 50rpx;
            font-size: 40rpx;
            background-color: #f48f20;
            margin: 0 auto;
            text-align: center;
            margin-top: 100rpx;
        }
        .foot{
            position: fixed;
            z-index: 13;
            bottom: 0;
            width: 100%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25rpx 0;

            .foot_btn{
                font-size: 30rpx;
                width: 40%;
                /*padding: 20rpx 90rpx;*/
                /*margin: 25rpx 0rpx;*/
                line-height: 80rpx;
                border-radius: 10rpx;
                text-align: center;
            }
        }
        ::-webkit-scrollbar
        {
            display: block;
            width: 20rpx!important;
            height: 16upx!important;
        }


        ::-webkit-scrollbar-track
        {
            /*-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);*/
            border-radius: 10px;
            background-color: #f3f3f3;
        }
        /*定义滑块 内阴影+圆角*/
        ::-webkit-scrollbar-thumb
        {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #c1c1c1;
        }
        .nav{
            width: 97%;margin: 0 auto;
            .nav_item{
                border: 1px solid #f3f3f3;
                margin-top: 20rpx;
                border-radius: 5px;
                padding: 30rpx;
                .nav_info{

                }
            }
        }
    }
</style>