<template>
	<view >
        <view class="bgw p30">
            <u-search  margin="10rpx"  placeholder="搜索名称" :animation="true" shape="square" v-model="keyword" height="80" @change="change" @search="search" @custom="search"></u-search>
        </view>
		<view >
			<view >

                <view class="hlbblock30 p30" >
                    <view class="fsc">
                        <view @click="type=0,search()" :class="type==0?'check':''" class="fscitem">
                            消息推送
                        </view>
                        <view @click="type=1,search()" :class="type==1?'check':''" class="fscitem">
                            朋友圈
                        </view>
                    </view>
                </view>

				<view class="hlbblock30 df ac"  v-for="(item,index) in list" :key="index">
                    <view style="width: 80%" class="linedot">
                        {{item.title}}
                    </view>
                    <view style="width: 20%;text-align: right">
                        <view>
                            <u-button @click="posttask(item.id)" type="primary" size="mini"  text="发布"></u-button>

                        </view>
                        <view class="mt30">
                            <u-button @click="deltask(item.id)" type="error" size="mini"  text="删除" ></u-button>

                        </view>

                    </view>


				</view>
			</view>
		</view>

        <navigator url="/pages/task/add"   class="glbbtn" >
            新增任务
        </navigator>

	</view>
</template>

<script>
    var _self;
	export default {
		data() {
			return {
                status: 'loadmore',
                list: {},
                page: 1,
                total:0,
                keyword: '',
                type:0,
			}
		},
		onLoad: function() {
            _self = this;

		},
        onReachBottom() {
            if(this.status == "nomore"){
                return;
            }
            this.getlist();
        },
		onShow: function() {
            _self.getlist();
		},
		methods: {
			/*获取数据*/
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
                uni.$u.http.post('/addons/fastscrm/api.task/list', {
                    page:self.page,
                    keyword:self.keyword,
                    type:self.type
                }).then(res => {
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
            },
            posttask(e) {
                let self = this;


                uni.showModal({
                    title: "提示",
                    content: "您确定要发布吗?",
                    success: function(o) {
                        o.confirm &&  uni.$u.http.post('/addons/fastscrm/api.task/taskPost', {
                            id:e,
                            type:self.type
                        }).then(res => {
                            console.log(res)
                            self.$u.toast('发布成功');
                        }).catch(res => {
                            console.log(res)
                            self.$u.toast('网络错误');
                        })
                    }
                });

            },
            /*删除地址*/
			deltask(e) {
				let self = this;
				uni.showModal({
					title: "提示",
					content: "您确定要移除吗?",
					success: function(o) {
						o.confirm &&  uni.$u.http.post('/addons/fastscrm/api.task/taskDelete', {
                            id:e,
                            type:self.type
                        }).then(res => {
                            _self.$refs.uToast.show({
                                type: 'success',
                                message: "删除成功",
                                complete() {
                                    _self.page=1;
                                    _self.getlist();
                                }
                            })

                        }).catch(res => {
                            self.$u.toast('网络错误');
                        })
					}
				});

			}
		}
	}
</script>

<style>
   .fscitem{
       width: 45%;
       color: #afafaf;
       text-align: center;
       border-radius: 10rpx;
       line-height: 80rpx
   }
   .check{
       background-color: #3492ff;
       color: white !important;
   }
</style>
