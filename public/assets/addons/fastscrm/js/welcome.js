var app = new Vue({
    el: '#welcomePage',
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
            title:'',
            content:'',
            typeData:{
                text:'',value:0
            },
            image:[],
            video:[],
            file:[],
            link_url:'',
            remark:'',
            appid:'',
            path:'',
            image_url:'',
            file_url:'',
            video_url:'',
        },
        searchForm:{
            title:'',
            content:'',
            typeData:{
                text:'',value:0
            },
            image:[],
            video:[],
            file:[],
            link_url:'',
            remark:'',
            appid:'',
            path:'',
            image_url:'',
            file_url:'',
            video_url:'',
        },
        showView:false,
        showTypePicker:false,
        choseItem:{},
        types: [
           { text:'',value:0},
           { text:'图片',value:1},
           { text:'链接',value:3},
           { text:'视频',value:5},
           { text:'小程序',value:6},
           { text:'文件',value:7}
           ],
        imgLabel:'',
        titleLength:255,

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
            axios.post('addons/fastscrm/welcome/myCodes',{
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
            axios.post('addons/fastscrm/welcome/coCodes',{
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
        viewImage(item) {
            this.showView = true;
            this.choseItem = item;
        },
        add() {
            var self=this;
            self.addShow = true;
        },
        onSubmit() {
            var self = this;
            var params = self.searchForm;
            axios.post('addons/fastscrm/welcome/add',
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
        setWelcome(id) {
            var self = this;
            vant.Dialog.confirm({
                title: '提示',
                message: '是否设置为您的欢迎语？',
                confirmButtonColor:'#5771F9'
            }).then(() => {
                  axios.post('addons/fastscrm/welcome/setWelcome',
                      {id:id}
                  ).then(function (response) {
                      if(response.data.code ==1){
                          vant.Toast({
                              message: '设置成功',
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
              }).catch(() => {
                  // on cancel
              });

        },
        addContent(n) {
            var self=this;
            if(n==2){
                self.searchForm.content +="{{员工姓名}}";
            }else {
                self.searchForm.content +="{{客户昵称}}";
            }
        },
        onConfirm(n) {
            var self=this;
            self.showTypePicker = false;
            self.searchForm.typeData = n;
            switch (self.searchForm.typeData.value) {
                case 1:
                    self.imgLabel = '图片';
                    self.titleLength = 255;
                    break;
                case 3:
                    self.imgLabel = '封面';
                    self.titleLength = 64;
                    break;
                case 5:
                case 7:
                    self.titleLength = 255;
                    break;
                case 6:
                    self.imgLabel = '封面';
                    self.titleLength = 32;
                    break;
            }
        },
        init() {
            var self=this;
            self.searchForm = JSON.parse(JSON.stringify(self.initFrom));
            self.list = [];
            self.loading = false;
            self.finished = false;
            self.error = false;
            self.refreshing = false;
            self.page = 1;
            self.total = 0;
            self.listTotal = 0;

            self.listCo = [];
            self.loadingCo = false;
            self.finishedCo = false;
            self.errorCo = false;
            self.refreshingCo = false;
            self.pageCo = 1;
            self.totalCo = 0;
            self.listTotalCo = 0;

            self.getData();
            self.getCoData();
        },
        beforeRead(file) {
            var self = this;
            switch (self.searchForm.typeData.value) {
                case 1:
                case 3:
                case 6:
                    if (file.type == 'image/jpeg' || file.type == 'image/jpg' || file.type == 'image/png') {
                        return true;
                    }else{
                        vant.Toast('请上传 jpg或png 格式图片');
                        return false;
                    }
                    break;
                case 5:
                    if(file.type=='video/mp4'){
                        return true;
                    }else{
                        vant.Toast('请上传 mp4 格式视频');
                        return false;
                    }
                    break;
                default:
                    return true;
                    break

            }
        },
        afterRead(file) {
            var self = this;
            file.status = 'uploading';
            file.message = '上传中...';
            const formData = new FormData();
            formData.append("file",file.file);
            axios.post('addons/fastscrm/welcome/upload',
                formData,{
                    'Content-type' : 'multipart/form-data'
                }).then(function (response) {
                if(response.data.code ==1){
                    file.status = 'done';
                    file.message = '上传成功';
                    switch (self.searchForm.typeData.value) {
                        case 1:
                        case 3:
                        case 6:
                            self.searchForm.image_url = response.data.data.url;
                            break;
                        case 5:
                            self.searchForm.video_url = response.data.data.url;
                            break;
                        case 7:
                            self.searchForm.file_url = response.data.data.url;
                            break;
                    }
                }else{
                    file.status = 'failed';
                    file.message = '上传失败';
                    vant.Toast(response.data.msg);

                }
            }).catch(function (error) {
                file.status = 'failed';
                file.message = '上传失败';
            });
        },
        isOverSize(file) {
            var self = this;
            if(self.searchForm.typeData.value==7){
                if(file.size > 20971520){
                    vant.Toast(fileType+'不能超过'+maxSize/1024/1024+'MB');
                    return true;
                }
            }else{
                switch (file.type) {
                    case 'image/jpeg':
                    case 'image/jpg':
                    case 'image/png':
                        var maxSize =  10485760;
                        var fileType =  '图片';
                        break;
                    case 'video/mp4':
                        var maxSize =  10485760;
                        var fileType =  '视频';
                        break;
                }
                if(file.size > maxSize){
                    vant.Toast(fileType+'不能超过'+maxSize/1024/1024+'MB');
                    return true;
                }
            }

        },
        validator(val) {
            var self = this;
            if(/^\s*$/.test(val) && self.searchForm.typeData.value==0){
                return false;//校验失败
            }
        },
    },
});
