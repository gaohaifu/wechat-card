define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/kf/servicer/index' + location.search,
                    add_url: 'fastscrm/kf/servicer/add',
                    del_url: 'fastscrm/kf/servicer/del',
                    multi_url: 'fastscrm/kf/servicer/multi',
                    import_url: 'fastscrm/kf/servicer/import',
                    table: 'fastscrm_kf_servicer',
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
                        {field: 'account.name', title: __('Open_kfid'), operate: 'LIKE'},
                        {field: 'worker.name', title: __('Worker_id'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(document).on("click", ".btn-sync", function () {
                Table.api.multi("changestatus", '', table, this);
            });
        },
        add: function () {
            new Vue({
                el: '#servicerAdd',
                components: {}, // 注册
                data() {
                    return {
                        workers: [],
                        accounts: [],
                        ruleForm: {
                            open_kfid: '',
                            worker_list: []
                        },
                        rules: {
                            open_kfid: [
                                {required: true, message: '请选择客户账户', trigger: 'blur'},
                            ],
                        }
                    }
                },
                mounted() {
                    this.getAccounts();
                },
                methods: {

                    submit: function () {
                        var _self = this;
                        var workers = '';
                        if (_self.workers.length <= 0) {
                            _self.$message({
                                showClose: true,
                                message: '请选择使用员工',
                                type: 'warning'
                            });
                            return;
                        }
                        $.each(_self.workers, function (index, value) {
                            workers += value.userid + ',';
                        });
                        workers = workers.substring(0, workers.length - 1);
                        var params = _self.ruleForm;
                        params.workers = workers;
                        Fast.api.ajax({
                            url: 'fastscrm/kf/servicer/add',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                    getAccounts: function () {
                        var _self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/kf/account/searchfind',
                            data: {},
                        }, function (res) {
                            _self.accounts = res.searchlist;
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
                        this.workers = [];
                    },
                    addworkers: function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.workers, function (index, value) {
                            ids += value.id + ',';
                        });
                        ids = ids.substring(0, ids.length - 1);
                        var url = 'fastscrm/template/workers?ids=' + ids;
                        Fast.api.open(url, '选择员工', {
                            callback: function (data) {
                                _self.workers = data;
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
