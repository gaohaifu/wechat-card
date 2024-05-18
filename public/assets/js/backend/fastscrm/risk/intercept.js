define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/risk/intercept/index' + location.search,
                    add_url: 'fastscrm/risk/intercept/add',
                    edit_url: 'fastscrm/risk/intercept/edit',
                    del_url: 'fastscrm/risk/intercept/del',
                    multi_url: 'fastscrm/risk/intercept/multi',
                    import_url: 'fastscrm/risk/intercept/import',
                    table: 'fastscrm_intercept',
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
                        {field: 'group.title', title: __('Group_id'), operate: 'LIKE'},
                        {field: 'rule_name', title: __('Rule_name'), operate: 'LIKE'},
                        {field: 'typedata', title: __('Typedata'), searchList: {"1":__('Typedata 1'),"2":__('Typedata 2')}, formatter: Table.api.formatter.normal},
                        {field: 'intercept_type', title: __('Intercept_type'), searchList: {"1":__('Intercept_type 1'),"2":__('Intercept_type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'creater', title: __('Creater'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            new Vue({
                el: '#interceptAdd',
                components:{  }, // 注册
                data() {
                    return {
                        workers:[],
                        departs:[],
                        groups:{},
                        ruleForm: {
                            rule_name: '',
                            word_list: '',
                            group_id: '',
                            typedata: '1',
                            intercept_type: '1',
                            semantics_list:[]
                        },
                        rules: {
                            rule_name: [
                                { required: true, message: '请输入规则名称', trigger: 'blur' },
                            ],
                            group_id: [
                                { required: true, message: '请选择所属分组', trigger: 'change' }
                            ],
                            word_list: [
                                { required: true, message: '请输入敏感词', trigger: 'blur' },
                            ],
                        }
                    }
                },
                mounted(){
                    this.getGroup();
                },
                methods:{

                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var departs = '';
                        if(_self.workers.length<=0 && _self.ruleForm.typedata=='1'){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用员工',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.departs.length<=0 && _self.ruleForm.typedata=='2'){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用部门',
                                type: 'warning'
                            });
                            return;
                        }
                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.departs,function (index,value) {
                            departs+=value.depart_id+',';
                        });
                        departs=departs.substring(0,departs.length-1);
                        var params = _self.ruleForm;
                        params.departs = departs;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/risk/intercept/add',
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
                            url: 'fastscrm/risk/interceptgroup/searchfind',
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
                        this.workers=[];
                        this.departs=[];
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
                    adddeparts:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.departs,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/departs?ids='+ids;
                        Fast.api.open(url,'选择部门',{
                            callback:function(data){
                                _self.departs= data;
                            }
                        });
                    },
                },
            });
        },
        edit: function () {
            Controller.api.bindevent();
            new Vue({
                el: '#interceptEdit',
                components:{  }, // 注册
                data() {
                    return {
                        id: Config.row.id,
                        departs: Config.row.departs,
                        workers:Config.row.workers,
                        groups:{},
                        ruleForm: {
                            rule_name: Config.row.rule_name,
                            word_list: Config.row.word_list,
                            group_id: Config.row.group_id,
                            typedata: Config.row.typedata,
                            intercept_type: Config.row.intercept_type,
                            semantics_list: Config.row.semantics_list,
                        },
                        rules: {
                            rule_name: [
                                { required: true, message: '请输入规则名称', trigger: 'blur' },
                            ],
                            group_id: [
                                { required: true, message: '请选择所属分组', trigger: 'change' }
                            ],
                            word_list: [
                                { required: true, message: '请输入敏感词', trigger: 'blur' },
                            ],
                        }
                    }
                },

                methods:{
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var departs = '';
                        if(_self.workers.length<=0 && _self.ruleForm.typedata=='1'){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用员工',
                                type: 'warning'
                            });
                            return;
                        }
                        if(_self.departs.length<=0 && _self.ruleForm.typedata=='2'){
                            _self.$message({
                                showClose: true,
                                message: '请选择使用部门',
                                type: 'warning'
                            });
                            return;
                        }
                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.departs,function (index,value) {
                            departs+=value.depart_id+',';
                        });
                        departs=departs.substring(0,departs.length-1);
                        var params = _self.ruleForm;
                        params.id = _self.id;
                        params.departs = departs;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/risk/intercept/edit',
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
                            url: 'fastscrm/risk/interceptgroup/searchfind',
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
                        this.workers=[];
                        this.departs=[];
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
                    adddeparts:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.departs,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/departs?ids='+ids;
                        Fast.api.open(url,'选择部门',{
                            callback:function(data){
                                _self.departs= data;
                            }
                        });
                    },
                },
                mounted(){
                    this.getGroup();
                },
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
