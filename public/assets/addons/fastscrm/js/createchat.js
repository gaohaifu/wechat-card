var app = new Vue({
    el: '#chatPage',
    data: {
        item: {},
        tags:[],
        show:false,
        tagsText:'',
        timeShow:false,
        sexList: ['未知', '男', '女'],
        addList: [
            {id:0,text:'未知来源'},
            {id:1,text:'扫描二维码'},
            {id:2,text:'搜索手机号'},
            {id:3,text:'名片分享'},
            {id:4,text:'群聊'},
            {id:5,text:'手机通讯录'},
            {id:6,text:'微信联系人'},
            {id:8,text:'安装第三方应用时自动添加的客服人员'},
            {id:9,text:'搜索邮箱'},
            {id:10,text:'视频号添加'},
            {id:11,text:'通过日程参与人添加'},
            {id:12,text:'通过会议参与人添加'},
            {id:13,text:'添加微信好友对应的企业微信'},
            {id:14,text:'通过智慧硬件专属客服添加'},
            {id:15,text:'通过上门服务客服添加'},
            {id:201,text:'内部成员共享'},
            {id:202,text:'管理员/负责人分配'},
        ],
        minDate: new Date(2010, 0, 1),
        showSex:false,
        showAdd:false,
        total:'',
        searchInit:{
            sex:'',
            add:'',
            startTime:'',
            endTime:'',
            tags:'',
        },
        searchForm:{
            sex:'',
            add:'',
            startTime:'',
            endTime:'',
            tags:'',
        },
        next:1,
        result: [],
        list: [],
        loading: false,
        finished: false,
        error : false,
        refreshing: false,
        page: 1,
        listTotal:0,
        checkedAll:false,
        chatName:'',
        chatIndex:1,
        showPopover:false,

    },
    mounted: function(){
        this.getTotal();
    },
    methods: {
        onClickLeft() {
            if(this.next>1){
                this.next = this.next-1;
            }else{
                history.back();
            }
        },
        getTotal() {
            var self=this;
            let tags = '';
            $.each(self.tags,function (index,value) {
                tags+=value.tagid+',';
            });
            self.searchForm.tags=tags.substring(0,tags.length-1);
            axios.post('addons/fastscrm/createchat/getTotal',self.searchForm
            ).then(function (response) {
                self.total = response.data.total;
                self.show=true;
            }).catch(function (error) {
                self.show=true;
            });
        },
        clear() {
            let obj = {};
            obj.sex = '';
            obj.add = '';
            obj.startTime = '';
            obj.endTime = '';
            obj.tags = '';
          this.searchForm =  obj;
          this.tags =  [];
          this.tagsText = '';
          this.$refs.tags.clear();
          this.getTotal();
        },
        choseTags() {
            this.$refs.tags.change();
        },
        returnTags(tags) {
            if(tags.length>0){
                this.tagsText = '已选择'+tags.length+'个标签';
            }else{
                this.tagsText = '';
            }
            this.tags = tags;
            this.getTotal();
        },
        choseTime() {
            this.timeShow = true;
        },
        choseSex() {
            this.showSex = true;
        },
        choseAdd() {
            this.showAdd = true;
        },
        timeConfirm(t) {
            this.searchForm.startTime = this.GMTToStr(t[0]);
            this.searchForm.endTime = this.GMTToStr(t[1]);
            this.timeShow = false;
            this.getTotal();
        },
        GMTToStr(time){
            let date = new Date(time)
            let Str=date.getFullYear() + '-' +
                (date.getMonth() + 1) + '-' +
                date.getDate();
            return Str;
        },
        sexConfirm(value, index) {
            this.searchForm.sex = index;
            this.showSex = false;
            this.getTotal();
        },
        sexCancel() {
            this.showSex = false;
        },
        addConfirm(value, index) {
            this.searchForm.add = index;
            this.showAdd = false;
            this.getTotal();
        },
        addCancel() {
            this.showAdd = false;
        },
        setNext(n) {
            this.next = n;
            if(n===2){
                this.list = [];
                this.page = 1;
                this.listTotal = 0;
                this.finished = false;
                this.error = false;
                this.refreshing = false;
                this.result = [];
                this.checkedAll = false;
                this.getData();
            }
        },
        //detail
        getData() {
            var self=this;
            if(self.next != 2){
                return;
            }
            self.loading=true;
            self.searchForm.page=self.page;
            axios.post('addons/fastscrm/createchat/detail',self.searchForm)
                .then(function (response) {
                self.loading=false;
                self.listTotal = response.data.total;
                self.list = self.list.concat(response.data.rows);
                if(response.data.rows.length<response.data.limit){
                    self.finished=true;
                }else{
                    self.page++;
                }
            }).catch(function (error) {
                self.error=true;
                self.loading=false;
            });
        },
        toggle(index) {
            this.$refs.checkboxes[index].toggle();
        },
        checkAll() {
            this.$refs.checkboxGroup.toggleAll(this.checkedAll);
        },
        toggleAll() {
            this.$refs.checkboxGroup.toggleAll();
        },
        createChat() {
            var self=this;
            if(self.chatName==''){
                vant.Toast.fail('请填写群名称');
                return;
            }
            var tempArr = [];
            var readyArr = [];
            for(var i=0,len=self.result.length;i<len;i+=39){
                tempArr.push(self.result.slice(i,i+39));
            }
            $.each(tempArr,function (index,item) {
                var externalUserIds = [];
                $.each(item,function (k,v) {
                    externalUserIds.push(v.external_userid)
                });
                readyArr.push(externalUserIds);
            });
            var n = self.chatIndex>0?self.chatIndex:1;
            $.each(readyArr,function (x,r) {
                wx.openEnterpriseChat({
                    userIds: '',
                    externalUserIds: r.join(';'), // 参与会话的外部联系人列表，格式为userId1;userId2;…，用分号隔开。
                    groupName: self.chatName+n,
                    chatId: "",
                    success: function(res) {
                        var chatId = res.chatId; //返回当前群聊ID，仅当使用agentConfig注入该接口权限时才返回chatId
                        vant.Toast.success('创建成功');
                    },
                    fail: function(res) {
                        if(res.errMsg.indexOf('function not exist') > -1){
                            vant.Toast.fail('版本过低请升级');
                        }
                    }
                });
                n++
            });


        }
    },
});
