var app = new Vue({
        el: '#replyPage',
        data: {
            groupTab: 0,
            groupList:[],
            keyword:'',
            list: [],
            loading: false,
            finished: false,
            error : false,
            refreshing: false,
            page: 1,
            show:false,
            total:0,
            entry:'',
            userId:'',
            chatId:''
        },
        mounted: function(){
            this.getCategory();
            this.getData();
        },
        methods: {
            onClickLeft() {
            },
            onSearch(){
                var self=this;
                self.page=1;
                self.total=0;
                self.list=[];
                self.finished=false;
                self.error=false;
                self.getData();
            },
            changeGroupTab(){
                var self=this;
                self.page=1;
                self.total=0;
                self.list=[];
                self.finished=false;
                self.error=false;
                self.getData();
            },
            getCategory() {
                var self=this;
                axios.post('addons/fastscrm/reply/getgrouplist',{

                }).then(function (response) {
                    self.groupList=response.data.list;
                }).catch(function (error) {

                });
            },
            getData() {
                var self=this;
                if(self.groupTab==0){
                    var groupId = 0;
                }else{
                    var groupId = self.groupList[self.groupTab-1].id;
                }
                self.loading=true;
                axios.post('addons/fastscrm/reply/getdata',{
                        page:self.page,
                        keyword:self.keyword,
                        groupId:groupId,
                    }).then(function (response) {
                        self.loading=false;
                        self.total = response.data.total;
                        self.list = self.list.concat(response.data.rows);
                        if(response.data.rows.length<response.data.limit){
                            self.finished=true;
                        }else{
                            self.page++;
                        }
                        self.show=true;
                    }).catch(function (error) {
                        self.error=true;
                        self.loading=false;
                        self.show=true;
                    });
            },
            checkMessage(msgtype,material) {
                var self=this;
                wx.invoke('getContext', {}, function(res){
                    if(res.err_msg == "getContext:ok"){
                         self.entry  = res.entry;
                        if (self.entry == 'single_chat_tools' ) {
                            wx.invoke('getCurExternalContact', {
                            }, function(res){
                                if(res.err_msg == "getCurExternalContact:ok"){
                                    self.userId  = res.userId //返回当前外部联系人userId
                                    self.sendMessage(msgtype,material);
                                }else {
                                    console.log('错误信息', res) //错误处理
                                }
                            });
                        }else if(self.entry == 'group_chat_tools'){
                            wx.invoke('getCurExternalChat', {
                            }, function(res){
                                if(res.err_msg == "getCurExternalChat:ok"){
                                    self.chatId  = res.chatId ; //返回当前客户群的群聊ID
                                    self.sendMessage(msgtype,material);
                                }else {
                                    console.log('错误信息', res) //错误处理
                                }
                            });

                        }else if(self.entry == 'chat_attachment'){

                        }else{
                            vant.Toast.fail('请从会话或附件窗口进入');
                            return;
                        }

                    }else {
                        //错误处理
                        vant.Toast.fail('请从企微客服端进入');
                    }
                });
            },
            sendMessage(item){
                var self=this;
                switch (item.msgtype) {
                    case 'text':
                        wx.invoke('sendChatMessage', {
                            msgtype:item.msgtype, //消息类型，必填
                            enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                            text: {
                                content:item.content, //文本内容
                            }
                        }, function(res) {
                            if (res.err_msg == 'sendChatMessage:ok') {
                                self.addShareLog(item.id);
                            }
                        });
                        break;
                    case 'image':
                        self.getMedia(item).then(function (mediaid) {
                            wx.invoke('sendChatMessage', {
                                msgtype:item.msgtype, //消息类型，必填
                                enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                                image: {
                                    mediaid: mediaid,
                                }
                            }, function(res) {
                                if (res.err_msg == 'sendChatMessage:ok') {
                                    self.addShareLog(item.id);
                                }
                            });

                        });
                        break;
                    case 'video':
                        self.getMedia(item).then(function (mediaid) {
                            wx.invoke('sendChatMessage', {
                                msgtype:item.msgtype, //消息类型，必填
                                enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                                video: {
                                    mediaid: mediaid,
                                }
                            }, function(res) {
                                if (res.err_msg == 'sendChatMessage:ok') {
                                    self.addShareLog(item.id);
                                }
                            });

                        });
                        break;
                    case 'file':
                        self.getMedia(item).then(function (mediaid) {
                            wx.invoke('sendChatMessage', {
                                msgtype:item.msgtype, //消息类型，必填
                                enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                                file: {
                                    mediaid: mediaid,
                                }
                            }, function(res) {
                                if (res.err_msg == 'sendChatMessage:ok') {
                                    self.addShareLog(item.id);
                                }
                            });

                        });
                        break;
                    case 'news':
                        wx.invoke('sendChatMessage', {
                            msgtype:item.msgtype, //消息类型，必填
                            enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                            news: {
                                link:   item.material.link_url, //H5消息页面url 必填
                                title:  item.material.title, //H5消息标题
                                desc:   item.material.remark, //H5消息摘要
                                imgUrl: item.material.image, //H5消息封面图片URL
                            }
                        }, function(res) {
                            if (res.err_msg == 'sendChatMessage:ok') {
                                self.addShareLog(item.id);
                            }
                        });
                        break;
                    case 'miniprogram':
                        wx.invoke('sendChatMessage', {
                            msgtype:item.msgtype, //消息类型，必填
                            enterChat: true, //为true时表示发送完成之后顺便进入会话，仅移动端3.1.10及以上版本支持该字段
                            miniprogram: {
                                    appid:  item.material.appid,//小程序的appid，企业已关联的任一个小程序
                                    title:  item.material.title, //小程序消息的title
                                    imgUrl: item.material.image,//小程序消息的封面图。必须带http或者https协议头，否则报错 $apiName$:fail invalid imgUrl
                                    page:   item.material.page, //小程序消息打开后的路径，注意要以.html作为后缀，否则在微信端打开会提示找不到页面
                                },
                        }, function(res) {
                            if (res.err_msg == 'sendChatMessage:ok') {
                                self.addShareLog(item.id);
                            }
                        });
                        break;
                }

            },
            async getMedia(item){
                return  await  axios.post('addons/fastscrm/reply/getMedia',{
                    id:item.material.id,
                }).then(function (response) {
                  return response.data.item.media_id;
                }).catch(function (error) {
                    vant.Toast.fail('素材不存在');
                });
            },
            addShareLog(id){
                var self=this;
                axios.post('addons/fastscrm/reply/addShareLog',{
                    id:id,
                    entry:self.entry,
                    userId:self.userId,
                    chatId:self.chatId
                }).then(function (response) {
                }).catch(function (error) {
                });
            }
        },
    });
