var app = new Vue({
        el: '#batchPage',
        data: {
            groupTab: 0,
            keyword:'',
            list: [],
            loading: false,
            finished: false,
            error : false,
            refreshing: false,
            page: 1,
            show:false,
            total:0,
        },
        mounted: function(){
            this.getData();
        },
        methods: {
            onClickLeft() {
                history.back();
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
            getData() {
                var self=this;
                self.loading=true;
                axios.post('addons/fastscrm/batch/getdata',{
                        page:self.page,
                        keyword:self.keyword,
                        groupTab:self.groupTab,
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
            toAdd(id) {
                vant.Toast.success('已复制手机号');
                wx.invoke('navigateToAddCustomer',
                    {},
                    function(res) {
                        axios.post('addons/fastscrm/batch/update',{
                            id:id
                        }).then(function (response) {
                        }).catch(function (error) {

                        });
                    });

            },
        },
    });
var clipboard = new ClipboardJS('.copy');
