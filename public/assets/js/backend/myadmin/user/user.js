define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/user.user/index',
                    add_url: 'myadmin/user.user/add',
                    edit_url: 'myadmin/user.user/edit',
                    del_url: 'myadmin/user.user/del',
                    multi_url: 'myadmin/user.user/multi',
                    table: 'company_user',
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
                        { checkbox: true },
                        { field: 'id', title: __('Id') },
                        { field: 'group_id', title: __('Group'), visible: false },
                        { field: 'group.name', title: __('Group') },
                        { field: 'info.avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false },
                        { field: 'info.username', title: __('Username'), operate: 'LIKE' },
                        { field: 'info.nickname', title: __('Nickname'), operate: 'LIKE' },
                        { field: 'info.email', title: __('Email'), operate: 'LIKE', visible: false },
                        { field: 'info.mobile', title: __('Mobile'), operate: 'LIKE' },
                        { field: 'info.level', title: __('Level'), operate: 'BETWEEN', sortable: false },
                        { field: 'info.gender', title: __('Gender'), visible: false, searchList: { 1: __('Male'), 0: __('Female') } },
                        { field: 'money', title: __('Money'), operate: 'BETWEEN', sortable: false },
                        { field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: false },
                        { field: 'info.successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: false },
                        { field: 'info.maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: false },
                        { field: 'info.logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true, visible: false },
                        { field: 'info.loginip', title: __('Loginip'), formatter: Table.api.formatter.search, visible: false },
                        { field: 'jointime', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: false },
                        { field: 'info.joinip', title: __('Joinip'), formatter: Table.api.formatter.search },
                        { field: 'company.name', title: __('company'), formatter: Table.api.formatter.search },
                        { field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: { normal: __('Normal'), hidden: __('Hidden') } },
                        { field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        myCommon: function () {
            // 选择企业
            $(document).on("change", "#c-company_id", function(obj) {
                $("#c-group_id").val('');
                $("#c-group_id_text").val('');
            });
            // 查询分组      
            $("#c-group_id").data("params", function(obj) {
                var company_id = $("#c-company_id").val() || 0;
                return { custom: { 'company_id': company_id } };
            });
        },
        add: function() {
            this.myCommon();
            Controller.api.bindevent();
        },
        edit: function() {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function() {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});