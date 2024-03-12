define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/user/moneylog/index',
                    add_url: 'myadmin/user/moneylog/add',
                    edit_url: 'myadmin/user/moneylog/edit',
                    del_url: 'myadmin/user/moneylog/del',
                    multi_url: 'myadmin/user/moneylog/multi',
                    table: 'company_user_money_log',
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
                        //{ field: 'id', title: __('Id') },
                        { field: 'createtime', width: '170', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        { field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search, visible: false },
                        { field: 'user.nickname', width: '120', title: __('Nickname') },
                        { field: 'memo', title: __('Memo'), formatter: Table.api.formatter.search, align: 'left' },
                        { field: 'money', width: '120', title: __('Money'), formatter: Table.api.formatter.search },
                        { field: 'before', width: '120', title: __('Before'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'after', width: '120', title: __('After'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'company.name', width: '170', title: __('company'), formatter: Table.api.formatter.search },
                        { field: 'operate', title: __('Operate'), width: '100', table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        myCommon: function () {
            // 选择企业
            $(document).on("change", "#c-company_id", function(obj) {
                $("#c-user_id").val('');
                $("#c-user_id_text").val('');
            });
            // 选择用户       
            $("#c-user_id").data("params", function(obj) {
                var company_id = $("#c-company_id").val() || 0;
                return { custom: { 'company_id': company_id } };
            });
        },
        add: function() {
            this.myCommon();
            Controller.api.bindevent();
        },
        edit: function() {
            this.myCommon();
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