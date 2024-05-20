define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/guide/channelcode/index' + location.search,
                    add_url: 'fastscrm/guide/channelcode/add',
                    edit_url: 'fastscrm/guide/channelcode/edit',
                    del_url: 'fastscrm/guide/channelcode/del',
                    multi_url: 'fastscrm/guide/channelcode/multi',
                    import_url: 'fastscrm/guide/channelcode/import',
                    table: 'fastscrm_channel_code',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'),  operate: 'LIKE'},
                        {field: 'config_id', title: __('Config_id'), operate: 'LIKE'},
                        {field: 'group_id', title: __('Group_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/guide/channelgroup/searchfind?field=row[id]"),formatter: Controller.api.formatter.grouptitle},
                        {field: 'qr_code', title: __('Qr_code'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'scan_total', title: __('Scan_total'), operate: false,formatter: Controller.api.formatter.scantotal},
                        {field: 'lose_total', title: __('Lose_total'), operate: false,formatter: Controller.api.formatter.losetotal},
                        {field: 'worker_id', title: __('Worker_id'), operate: false,formatter: Controller.api.formatter.workertotal},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'scene', title: __('Scene'), searchList: {"1":__('Scene 1'),"2":__('Scene 2')}, formatter: Table.api.formatter.normal},
                        {field: 'creater', title: __('Creater'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons:[
                                {
                                    name: 'click',
                                    title: __('下载'),
                                    text: '下载',
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-qrcode',
                                    click: function (data,i) {
                                        var url = Fast.api.fixurl('fastscrm/guide/channelcode/download?id='+i.id);
                                        window.location.href=url;
                                    }
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
            Controller.api.bindevent();
            new Vue({
                el: '#channelAdd',
                components:{  }, // 注册
                data() {
                    return {
                        tags: [],
                        workers:[],
                        groups:{},
                        ruleForm: {
                            name: '',
                            remark: '',
                            group_id: '',
                            type: '2',
                            scene: '2',
                            style: '1',
                            skip_verify: true,
                            is_exclusive: false,
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入活码名称', trigger: 'blur' },
                            ],
                            group_id: [
                                { required: true, message: '请选择所属分组', trigger: 'change' }
                            ],
                            remark: [
                                { required: true, message: '请输入备注信息', trigger: 'blur' },
                            ],
                        }
                    }
                },

                methods:{
                    addtags:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.tags,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/tags?ids='+ids;
                        Fast.api.open(url,'选择标签',{
                            callback:function(data){
                                _self.tags= data;
                            }
                        });
                    },
                    addworkers:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.workers,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/workers?ids='+ids;
                        Fast.api.open(url,'选择员工',{
                            callback:function(data){
                                _self.workers= data;
                            }
                        });
                    },
                    deltag:function (e) {
                        this.tags.splice(this.tags.indexOf(e), 1);
                    },
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var tags = '';
                        if(_self.workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用员工',
                                type: 'warning'
                            });
                            return;
                        }
                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.tags,function (index,value) {
                            tags+=value.tagid+',';
                        });
                        tags=tags.substring(0,tags.length-1);
                        var params = _self.ruleForm;
                        params.tags = tags;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/guide/channelcode/add',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                    getGroup:function () {
                        var _self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/guide/channelgroup/searchfind',
                            data: {},
                        }, function (res) {
                            _self.groups = res.searchlist;
                            return false;
                        })
                    },
                    submitForm(formName) {
                        var _self = this;
                        this.$refs[formName].validate((valid) => {
                            if (valid) {
                                _self.submit();
                            } else {
                                return false;
                            }
                        });
                    },
                    resetForm(formName) {
                        this.$refs[formName].resetFields();
                        this.tags=[];
                        this.workers=[];
                    }
                },
                mounted(){
                    this.getGroup();
                },
            });
            $('#channelAdd').show();


        },
        edit: function () {
            Controller.api.bindevent();
            new Vue({
                el: '#channelEdit',
                components:{  }, // 注册
                data() {
                    return {
                        id: Config.row.id,
                        tags: Config.row.tags,
                        workers:Config.row.workers,
                        groups:{},
                        ruleForm: {
                            name: Config.row.name,
                            remark: Config.row.remark,
                            group_id: Config.row.group_id,
                            type: Config.row.type,
                            scene: Config.row.scene,
                            style: Config.row.style,
                            skip_verify: Config.row.skip_verify==1?true:false,
                            is_exclusive: Config.row.is_exclusive==1?true:false,
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入活码名称', trigger: 'blur' },
                            ],
                            group_id: [
                                { required: true, message: '请选择所属分组', trigger: 'change' }
                            ],
                            remark: [
                                { required: true, message: '请输入备注信息', trigger: 'blur' },
                            ],
                        }
                    }
                },

                methods:{
                    addtags:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.tags,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/tags?ids='+ids;
                        Fast.api.open(url,'选择标签',{
                            callback:function(data){
                                _self.tags= data;
                            }
                        });
                    },
                    addworkers:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.workers,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/workers?ids='+ids;
                        Fast.api.open(url,'选择员工',{
                            callback:function(data){
                                _self.workers= data;
                            }
                        });
                    },
                    deltag:function (e) {
                        this.tags.splice(this.tags.indexOf(e), 1);
                    },
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var tags = '';
                        if(_self.workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用员工',
                                type: 'warning'
                            });
                            return;
                        }
                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.tags,function (index,value) {
                            tags+=value.tagid+',';
                        });
                        tags=tags.substring(0,tags.length-1);
                        var params = _self.ruleForm;
                        params.id = _self.id;
                        params.tags = tags;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/guide/channelcode/edit',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                    getGroup:function () {
                        var _self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/guide/channelgroup/searchfind',
                            data: {},
                        }, function (res) {
                            _self.groups = res.searchlist;
                            return false;
                        })
                    },
                    submitForm(formName) {
                        var _self = this;
                        this.$refs[formName].validate((valid) => {
                            if (valid) {
                                _self.submit();
                            } else {
                                return false;
                            }
                        });
                    },
                    resetForm(formName) {
                        this.$refs[formName].resetFields();
                        this.tags=[];
                        this.workers=[];
                    }
                },
                mounted(){
                    this.getGroup();
                },
            });
            $('#channelEdit').show();


        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },formatter: {//渲染的方法
                grouptitle: function (value, row, index) {
                    return row.channelgroup.title;
                },
                workertotal: function (value, row, index) {
                    return row.workertotal+'人';
                },
                scantotal: function (value, row, index) {
                    return row.scantotal;
                },
                losetotal: function (value, row, index) {
                    return row.losetotal;
                },
            },
        }
    };
    return Controller;
});
