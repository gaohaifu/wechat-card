<template>
	<view class="address-form">
        <view class="fui-cell-group hlbblock30" style="padding: 30rpx;">
            <view class="fsc">
                <view @click="type=0" :class="type==0?'check':''" style="width: 45%;color: #afafaf;text-align: center;border-radius: 10rpx;line-height: 80rpx">
                    消息推送
                </view>
                <view @click="type=1" :class="type==1?'check':''" style="width: 45%;color: #afafaf;text-align: center;border-radius: 10rpx;line-height: 80rpx">
                    朋友圈
                </view>
            </view>
            <view class="help-block" style="padding: 20rpx 0 0;font-size: 22rpx;color:#f53630 " v-if="type==1">
                朋友圈类型素材不支持 小程序/文件 类型
            </view>
            <view class="fui-cell " style="margin-top: 20rpx">
                <view class="fui-cell-label ">
                    名称
                </view>
                <view class="fui-cell-info">
                    <input type="text" placeholder="请输入任务名称"   v-model="title"/>
                </view>
            </view>
            <view class="fui-cell " style="margin-top: 20rpx">
                <view class="fui-cell-label ">
                    内容
                </view>
                <view class="fui-cell-info">
                    <input type="text" placeholder="请输入任务名称"   v-model="content"/>
                </view>
            </view>
            <view class="fui-cell " style="margin-top: 20rpx">
                <view class="fui-cell-label ">
                    选择素材
                </view>
                <view class="fui-cell-info">
                    <input @click="itempop=true" type="text" disabled placeholder="请选择素材" name="name"  v-model="choseitem.title"/>
                </view>
                <u-picker :columns="items" keyName="title" :show="itempop" @confirm="itemconfirm" @cancel="itempop = false" @close="itempop = false" :closeOnClickOverlay="true" ></u-picker>
            </view>
            <view class="fui-cell " style="margin-top: 20rpx">
                <view class="fui-cell-label ">
                    选择员工
                </view>
                <view class="fui-cell-info" @click="workershow=true">
                    <view style="display: flex"  >
                        <block v-for="(item,index) in workers">
                            <view @click="selectAttr(index)" style="margin: 20rpx">
                                <view v-if="item.check" style="padding: 10rpx;background-color: #F63E36;color: white;text-align: center;border-radius: 10rpx">
                                    {{item.name}}
                                </view>
                            </view>
                        </block>
                    </view>
                </view>
                <u-popup :show="workershow" @close="workershow = false" mode="center" round="10"  >
                    <view style="width: 540rpx">
                        <view style="margin-top: 30rpx;color: #b8bcc6;font-size: 36rpx;text-align: center">
                            选择员工
                        </view>
                        <view>
                            <view style="display: flex"  >
                                <block v-for="(item,index) in workers">
                                    <view @click="selectAttr(index)" style="margin: 20rpx">
                                        <view v-if="item.check" style="padding: 10rpx;background-color: #F63E36;color: white;text-align: center;border-radius: 10rpx">
                                            {{item.name}}
                                        </view>
                                        <view v-else="item.check" style="padding: 10rpx;background-color: #f1f1f1;color: #505050;text-align: center;border-radius: 10rpx">
                                            {{item.name}}
                                        </view>
                                    </view>
                                </block>
                            </view>
                        </view>
                    </view>
                </u-popup>
            </view>

        </view>

            <navigator @click="submit"  style="width: 85%;position: fixed;bottom: 20rpx;left: 50rpx; text-align: center;color: #FFFFFF;line-height: 70rpx;border-radius: 35rpx;background-color: #F63E36;" >
                提交
            </navigator>


    </view>
</template>

<script>
    var _self;
	export default {

		data() {
			return {
                type:0,
                itempop:false,
                workershow:false,
                choseitem:[],
                items:[],
                workers:[],
                title:'',
                content:'',
			};
		},
		onLoad: function() {
		    _self = this;

		},
        onShow: function() {
            _self.getData();
        },
		methods: {
            submit(){

                if (_self.$u.test.isEmpty(_self.title)){
                    _self.$u.toast('请输入名称');
                    return
                }
                if (_self.$u.test.isEmpty(_self.choseitem)){
                    _self.$u.toast('请选择素材');
                    return
                }
                if (_self.type==1){
                    if (_self.choseitem.type==6||_self.choseitem.type==7){
                        _self.$u.toast('请核对素材类型');
                        return
                    }
                }
                uni.$u.http.post('/addons/fastscrm/api.task/submit', {
                    workers:JSON.stringify(_self.workers),
                    title:_self.title,
                    content:_self.content,
                    type:_self.type,
                    choseitem:_self.choseitem.id,
                }).then(res => {

                    _self.$u.toast('提交成功');

                    setTimeout(function () {
                        uni.redirectTo({
                            url: '/pages/task/index'
                        })
                    },1000)


                }).catch(res => {
                    _self.$u.toast('网络错误');
                })

            },
            selectAttr(index){
                _self.workers[index].check=!_self.workers[index].check;
            },
			// 获取
			getData(){
                uni.$u.http.post('/addons/fastscrm/api.task/addinfo', {
                }).then(res => {
                    _self.items = res.data.data.items;
                    _self.workers = res.data.data.workers;

                }).catch(res => {
                    _self.$u.toast('网络错误');
                })

			},
            itemconfirm(e){
                _self.choseitem = e.value[0];
                _self.itempop = false
            },
		}
	};
</script>

<style>
    page{background-color:#F2F2F2}


    .check{
        background-color: #3492ff;
        color: white !important;
    }


	.address-form {
		/* border-top: 16rpx solid #f2f2f2; */
	}

	.address-form .key-name {
		width: 140rpx;
		font-size: 32rpx
	}

	.address-form .btn-red {
		height: 88rpx;
		line-height: 88rpx;
		border-radius: 44rpx;
		box-shadow: 0 8rpx 16rpx 0 rgba(226, 35, 26, .6);
	}

	.addBtn {
		height: 80rpx;
		line-height: 80rpx;
		border-radius: 40rpx;
	}
</style>
