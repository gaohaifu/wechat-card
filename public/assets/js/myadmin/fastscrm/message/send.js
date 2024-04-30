define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/message/send/index' + location.search,
                    add_url: 'fastscrm/message/send/add',
                    edit_url: 'fastscrm/message/send/edit',
                    del_url: 'fastscrm/message/send/del',
                    multi_url: 'fastscrm/message/send/multi',
                    import_url: 'fastscrm/message/send/import',
                    table: 'fastscrm_webhook_send',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'template.name', title: __('Template_id'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'fixedtime', title: __('Fixedtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'creater', title: __('Creater'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('执行'),
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    icon: 'fa fa-send-o',
                                    confirm: '确认发送机器人通知？',
                                    url: 'fastscrm/message/send/execute',
                                    success: function (data, ret) {
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        return false;
                                    }
                                },
                                {
                                    name: 'sendlogtabs',
                                    title: __('日志'),
                                    classname: 'btn btn-xs btn-warning btn-sendlog',
                                    icon: 'fa fa-file-sound-o',
                                }
                            ],
                            formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            $(document).on("click", ".btn-sendlog", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/message/sendlog?send_id='+row.id;
                Backend.api.addtabs(url, '通知日志', '');
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            var vm =  new Vue({
                el: '#sendAdd',
                components:{  }, // 注册
                data() {
                    return {
                        workers:[],
                        webhooks:[],
                        templates:{},
                        ruleForm: {
                            template_id: '',
                            name: '',
                            type: '1',
                            mentioned_type: '1',
                            fixedtime: '',
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入任务名称', trigger: 'blur' },
                            ],
                            msg_type: [
                                { required: true, message: '请选择消息类型', trigger: 'change' }
                            ],
                            template_id: [
                                { required: true, message: '请选择消息模板', trigger: 'change' }
                            ],
                            fixedtime: [
                                { required: true, message: '请选择发送时间', trigger: 'blur' },
                            ],
                        }
                    }
                },
                mounted(){
                    this.getTemplates();
                },
                methods:{
                    getTemplates:function () {
                        var _self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/message/former/searchfind',
                            data: {},
                        }, function (res) {
                            _self.templates = res.searchlist;
                            return false;
                        })
                    },
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var webhooks = '';
                        if(_self.webhooks.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择机器人',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.workers.length<=0 && _self.ruleForm.mentioned_type=='1'){
                            _self.$message({
                                showClose: true,
                                message: '请选择@的员工',
                                type: 'warning'
                            });
                            return;
                        }

                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.webhooks,function (index,value) {
                            webhooks+=value.webhookid+',';
                        });
                        webhooks=webhooks.substring(0,webhooks.length-1);
                        var params = _self.ruleForm;
                        params.webhooks = webhooks;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/message/send/add',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
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
                    addwebhook:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.webhooks,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/webhooks?ids='+ids;
                        Fast.api.open(url,'选择机器人',{
                            callback:function(data){
                                _self.webhooks= data;
                            }
                        });
                    },
                },
            });
            Controller.api.bindevent();
        },
        edit: function () {
            var vm =  new Vue({
                el: '#sendEdit',
                components:{  }, // 注册
                data() {
                    return {
                        id: Config.row.id,
                        workers: Config.row.workers,
                        webhooks: Config.row.webhooks,
                        templates: {},
                        ruleForm: {
                            template_id: Config.row.template_id,
                            name:  Config.row.name,
                            type:  Config.row.type,
                            mentioned_type:  Config.row.mentioned_type,
                            fixedtime:  Config.row.fixedtime_text,
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入任务名称', trigger: 'blur' },
                            ],
                            msg_type: [
                                { required: true, message: '请选择消息类型', trigger: 'change' }
                            ],
                            template_id: [
                                { required: true, message: '请选择消息模板', trigger: 'change' }
                            ],
                            fixedtime: [
                                { required: true, message: '请选择发送时间', trigger: 'blur' },
                            ],
                        }
                    }
                },
                mounted(){
                    this.getTemplates();
                },
                methods:{
                    getTemplates:function () {
                        var _self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/message/former/searchfind',
                            data: {},
                        }, function (res) {
                            _self.templates = res.searchlist;
                            return false;
                        })
                    },
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var webhooks = '';
                        if(_self.webhooks.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择机器人',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.workers.length<=0 && _self.ruleForm.mentioned_type=='1'){
                            _self.$message({
                                showClose: true,
                                message: '请选择@的员工',
                                type: 'warning'
                            });
                            return;
                        }

                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.webhooks,function (index,value) {
                            webhooks+=value.webhookid+',';
                        });
                        webhooks=webhooks.substring(0,webhooks.length-1);
                        var params = _self.ruleForm;
                        params.id = _self.id;
                        params.webhooks = webhooks;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/message/send/edit',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
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
                    addwebhook:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.webhooks,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/webhooks?ids='+ids;
                        Fast.api.open(url,'选择机器人',{
                            callback:function(data){
                                _self.webhooks= data;
                            }
                        });
                    },
                },
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
