var app = new Vue({
    el: '#customerPage',
    data: {
        show:true,
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
            video_url:''
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
            video_url:''
        },
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
        loading:false

    },
    mounted: function(){

    },
    methods: {
        onClickLeft() {
            history.back();
        },
        onSubmit() {
            var self = this;
            if(!self.loading){
                self.loading = true;
                var params = self.searchForm;
                axios.post('addons/fastscrm/customermessage/add',
                    params
                ).then(function (response) {
                    self.loading = false;
                    if(response.data.code ==1){
                        self.shareToExternalChat(response.data.item);
                    }else{
                        vant.Toast.fail(response.data.msg);
                    }
                }).catch(function (error) {
                    self.loading = false;
                });
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
            axios.post('addons/fastscrm/customermessage/upload',
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
        shareToExternalChat(item) {
            var self = this;
            var attachments = new Object();
            switch (self.searchForm.typeData.value) {
                case 1:
                    attachments.msgtype = 'image';
                    let image = new Object();
                    image.mediaid = item.media_id;
                    attachments.image = image;
                    break;
                case 3:
                    attachments.msgtype = 'link';
                    let link = new Object();
                    link.title = item.title;
                    link.imgUrl = item.image;
                    link.desc = item.remark;
                    link.url = item.link_url;
                    attachments.link = link;
                    break;
                case 5:
                    attachments.msgtype = 'video';
                    let video = new Object();
                    video.mediaid = item.media_id;
                    attachments.video = video;
                    break;
                case 6:
                    attachments.msgtype = 'miniprogram';
                    let miniprogram = new Object();
                    miniprogram.appid = item.appid;
                    miniprogram.title = item.title;
                    miniprogram.imgUrl = item.image;
                    miniprogram.page = item.path;
                    attachments.miniprogram = miniprogram;
                    break;
                case 7:
                    attachments.msgtype = 'file';
                    let file = new Object();
                    file.mediaid = item.media_id;
                    attachments.file = file;
                    break;
            }
            wx.invoke("shareToExternalContact", {
                    text: {
                        content:self.searchForm.content,    // 文本内容
                    },
                    attachments: [attachments]},function(res) {
                    if (res.err_msg == "shareToExternalChat:ok") {
                        self.init();
                    }
                }
            );
        },
    },
});
