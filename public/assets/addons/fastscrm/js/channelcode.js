var app = new Vue({
    el: '#codePage',
    data: {
        list: [],
        loading: false,
        finished: false,
        error : false,
        refreshing: false,
        page: 1,
        total:0,
        listTotal:0,

        listCo: [],
        loadingCo: false,
        finishedCo: false,
        errorCo : false,
        refreshingCo: false,
        pageCo: 1,
        totalCo:0,
        listTotalCo:0,

        show:false,
        addShow:false,

        initFrom:{
            name:'',
            remark:'',
            type:'2',
            skip_verify:true,
            is_exclusive:false,
            tagsText:'',
            workersText:'',
        },
        searchForm:{
            name:'',
            remark:'',
            type:'2',
            skip_verify:true,
            is_exclusive:false,
            tagsText:'',
            workersText:'',
        },
        tags:[],
        workers:[],
        verifyTip:false,
        exclusiveTip:false,
    },
    mounted: function(){
        this.getData();
        this.getCoData();
    },
    methods: {
        onClickLeft() {
            history.back();
        },
        getData() {
            var self=this;
            self.loading=true;
            axios.post('addons/fastscrm/channelcode/myCodes',{
                    page:self.page
            }).then(function (response) {
                    self.loading=false;
                    self.listTotal = response.data.total;
                    self.list = self.list.concat(response.data.rows);
                    if(response.data.rows.length<response.data.limit){
                        self.finished=true;
                    }else{
                        self.page++;
                    }
                    self.show = true;
                }).catch(function (error) {
                    self.error=true;
                    self.loading=false;
                    self.show = true;
            });
        },
        getCoData() {
            var self=this;
            self.loadingCo=true;
            axios.post('addons/fastscrm/channelcode/coCodes',{
                page:self.pageCo
            }).then(function (response) {
                self.loadingCo=false;
                self.listTotalCo = response.data.total;
                self.listCo = self.listCo.concat(response.data.rows);
                if(response.data.rows.length<response.data.limit){
                    self.finishedCo=true;
                }else{
                    self.pageCo++;
                }
                self.show = true;
            }).catch(function (error) {
                self.errorCo=true;
                self.loadingCo=false;
                self.show = true;
            });
        },
        viewImage(url) {
            wx.previewImage({
                current: url, // 第一张显示的图片链接
                urls: [url] // 需要预加载的图片http链接列表，预加载后，可以滑动浏览这些图片
            });
        },
        add() {
            var self=this;
            self.addShow = true;
        },
        onSubmit() {
            var self = this;
            var workers = '';
            var tags = '';

            $.each(self.workers,function (index,value) {
                workers+=value+',';
            });
            workers=workers.substring(0,workers.length-1);
            $.each(self.tags,function (index,value) {
                tags+=value.tagid+',';
            });
            tags=tags.substring(0,tags.length-1);
            var params = self.searchForm;
            params.tags = tags;
            params.workers = workers;
            axios.post('addons/fastscrm/channelcode/add',
                params
            ).then(function (response) {
                if(response.data.code ==1){
                    vant.Toast({
                        message: '创建成功',
                        type:'success',
                        onClose:function () {
                          self.init();
                        }
                    });
                }else{
                    vant.Toast.fail(response.data.msg);
                }
            }).catch(function (error) {

            });
        },
        choseTags() {
            this.$refs.tags.change();
        },
        returnTags(tags) {
            var self=this;
            if(tags.length>0){
                self.searchForm.tagsText = '已选择'+tags.length+'个标签';
            }else{
                self.searchForm.tagsText = '';
            }
            self.tags = tags;
        },
        choseWorkers() {
            var self=this;
            wx.invoke("selectEnterpriseContact", {
                    "fromDepartmentId": -1,// 必填，表示打开的通讯录从指定的部门开始展示，-1表示自己所在部门开始, 0表示从最上层开始
                    "mode": "multi",// 必填，选择模式，single表示单选，multi表示多选
                    "type": ["user"],// 必填，选择限制类型，指定department、user中的一个或者多个
                    "selectedDepartmentIds": [],// 非必填，已选部门ID列表。用于多次选人时可重入，single模式下请勿填入多个id
                    "selectedUserIds": self.workers// 非必填，已选用户ID列表。用于多次选人时可重入，single模式下请勿填入多个id
                },function(res){
                    if (res.err_msg == "selectEnterpriseContact:ok")
                    {
                        if(typeof res.result == 'string')
                        {
                            res.result = JSON.parse(res.result);
                        }
                        if(res.result.userList.length>0){
                            self.searchForm.workersText = '已选择'+res.result.userList.length+'位员工';
                            $.each(res.result.userList,function (index,value) {
                                self.workers.push(value.id);
                            });
                        }else{
                            self.searchForm.workersText = '';
                            self.workers = [];
                        }

                    }
                }
            );
        },
        init() {
            var self=this;
            self.searchForm = JSON.parse(JSON.stringify(self.initFrom));
            self.tags = [];
            self.workers = [];
            self.list = [];
            self.loading = false;
            self.finished = false;
            self.error = false;
            self.refreshing = false;
            self.page = 1;
            self.total = 0;
            self.listTotal = 0;
            self.getData();
        }
    },
});
