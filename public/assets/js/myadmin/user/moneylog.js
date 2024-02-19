define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/moneylog/index',
                    add_url: 'user/moneylog/add',
                    edit_url: 'user/moneylog/edit',
                    del_url: 'user/moneylog/del',
                    multi_url: 'user/moneylog/multi',
                    table: 'user_money_log',
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
                        //{field: 'id', title: __('Id')},
                        { field: 'createtime', width: '170', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        { field: 'user_id', title: __('User_id'), formatter: Table.api.formatter.search, visible: false },
                        { field: 'user.nickname', width: '120', title: __('Nickname') },
                        { field: 'memo', title: __('Memo'), formatter: Table.api.formatter.search, align: 'left' },
                        { field: 'money', width: '120', title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'before', width: '120', title: __('Before'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'after', width: '120', title: __('After'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'operate', title: __('Operate'), width: '100', table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
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