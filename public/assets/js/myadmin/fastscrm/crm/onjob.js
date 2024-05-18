define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/crm/onjob/index' + location.search,
                    add_url: 'fastscrm/crm/onjob/add',
                    del_url: 'fastscrm/crm/onjob/del',
                    multi_url: 'fastscrm/crm/onjob/multi',
                    import_url: 'fastscrm/crm/onjob/import',
                    table: 'fastscrm_transfer_onjob',
                }
            });

            var table = $("#table");
            $(".btn-add").data("area", ["90%", "90%"]);
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'handover_name', title: __('Handover_name'), operate: 'LIKE'},
                        {field: 'handover_department', title: __('Handover_department'), operate: 'LIKE'},
                        {field: 'takeover_name', title: __('Takeover_name'), operate: 'LIKE'},
                        {field: 'takeover_department', title: __('Takeover_department'), operate: 'LIKE'},
                        {field: 'type', title: __('Type'), searchList: {"customer":__('Type customer'),"groupchat":__('Type groupchat')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('执行任务'),
                                    text: '执行任务',
                                    classname: 'btn btn-xs btn-info btn-magic btn-ajax',
                                    confirm: '是否确认执行？',
                                    url: 'fastscrm/crm/onjob/action',
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh', {});
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'ajax',
                                    title: __('同步客户接替状态'),
                                    text: '同步接替状态',
                                    classname: 'btn btn-xs btn-warning btn-magic btn-ajax',
                                    confirm: '是否确认同步？',
                                    url: 'fastscrm/crm/onjob/sync',
                                    visible: function (row) {
                                        if(row.type=='customer'){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    },
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh', {});
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'customer',
                                    title: __('客户列表'),
                                    text: '客户列表',
                                    classname: 'btn btn-xs btn-success btn-customer',
                                    visible: function (row) {
                                        if(row.type=='customer'){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }
                                },
                                {
                                    name: 'chat',
                                    title: __('群聊列表'),
                                    text: '群聊列表',
                                    classname: 'btn btn-xs btn-primary btn-chat',
                                    visible: function (row) {
                                        if(row.type=='groupchat'){
                                            return true;
                                        }else{
                                            return false;
                                        }
                                    }
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(document).on("click", ".btn-customer", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/crm/onjobcustomer?onjob_id='+row.id;
                Backend.api.addtabs(url, '转移客户列表', '');
            });
            $(document).on("click", ".btn-chat", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/crm/onjobchat?onjob_id='+row.id;
                Backend.api.addtabs(url, '转移群列表', '');
            });
        },
        add: function () {
            new Vue({
                el: '#onjobAdd',
                components:{  }, // 注册
                data() {
                    return {
                        handover_workers:[],
                        takeover_workers:[],
                        customers:[],
                        chats:[],
                        ruleForm: {
                            type: 'customer',
                        },
                        rules: {
                        },
                        showChoseCustomer:false
                    }
                },
                mounted(){

                },
                methods:{
                    submit:function () {
                        var _self = this;
                        if(_self.handover_workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择原跟进员工',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.takeover_workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择接替员工',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.handover_workers[0].userid == _self.takeover_workers[0].userid){
                            _self.$message({
                                showClose: true,
                                message: '原跟进员工不能和接替员工相同',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.ruleForm.type=='customer'){
                            if(_self.customers.length<=0){
                                _self.$message({
                                    showClose: true,
                                    message: '请选择转移客户',
                                    type: 'warning'
                                });
                                return;
                            }
                        } else{
                            if(_self.chats.length<=0){
                                _self.$message({
                                    showClose: true,
                                    message: '请选择转移群聊',
                                    type: 'warning'
                                });
                                return;
                            }
                        }
                        var customers = '';
                        var chats = '';
                        $.each(_self.customers,function (index,value) {
                            customers+=value.external_userid+',';
                        });
                        customers=customers.substring(0,customers.length-1);
                        $.each(_self.chats,function (index,value) {
                            chats+=value.chat_id+',';
                        });
                        chats=chats.substring(0,chats.length-1);
                        var params = _self.ruleForm;
                        params.handover_workers = _self.handover_workers[0];
                        params.takeover_workers = _self.takeover_workers[0];
                        params.customers = customers;
                        params.chats = chats;
                        Fast.api.ajax({
                            url: 'fastscrm/crm/onjob/add',
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
                    addHandoverWorkers:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.handover_workers,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/workers?ids='+ids;
                        Fast.api.open(url,'选择原跟进员工',{
                            callback:function(data){
                                if(data.length>0){
                                    let temp = new Array();
                                    temp.push(data[0]);
                                    _self.handover_workers = temp;
                                }
                            }
                        });
                    },
                    delHandoverWorkers:function (e) {
                        this.handover_workers.splice(this.handover_workers.indexOf(e), 1);
                    },
                    addTakeoverWorkers:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.takeover_workers,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/workers?ids='+ids;
                        Fast.api.open(url,'选择接替员工',{
                            callback:function(data){
                                if(data.length>0){
                                    let temp = new Array();
                                    temp.push(data[0]);
                                    _self.takeover_workers = temp;
                                }
                            }
                        });
                    },
                    delTakeoverWorkers:function (e) {
                        this.takeover_workers.splice(this.takeover_workers.indexOf(e), 1);
                    },
                    addCustomer:function (tags) {
                        var _self = this;
                        var url = 'fastscrm/template/customer?tags='+tags+'&fl_userid='+_self.handover_workers[0].userid;
                        Fast.api.open(url,'选择转移客户',{
                            area:["90%","90%"],
                            callback:function(data){
                                _self.showChoseCustomer = false;
                                _self.customers = data;
                            }
                        });
                    },
                    addtags:function () {
                        var _self = this;
                        var url = 'fastscrm/template/tags';
                        Fast.api.open(url,'选择标签',{
                            callback:function(data){
                                var tags = '';
                                $.each(data,function (index,value) {
                                    tags+=value.tagid+',';
                                });
                                tags=tags.substring(0,tags.length-1);
                                _self.addCustomer(tags)
                            }
                        });
                    },
                    choseCustomer:function () {
                        var _self = this;
                        if(_self.handover_workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择原跟进员工',
                                type: 'warning'
                            });
                            return;
                        }
                        _self.showChoseCustomer = true;
                    },
                    addChat:function () {
                        var _self = this;
                        if(_self.handover_workers.length<=0){
                            _self.$message({
                                showClose: true,
                                message: '请选择原跟进员工',
                                type: 'warning'
                            });
                            return;
                        }
                        var url = 'fastscrm/template/chat?&fl_userid='+_self.handover_workers[0].userid;
                        Fast.api.open(url,'选择转移群聊',{
                            area:["90%","90%"],
                            callback:function(data){
                                _self.chats = data;
                            }
                        });
                    },
                },
            });
            Controller.api.bindevent();
        },
        edit: function () {
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
