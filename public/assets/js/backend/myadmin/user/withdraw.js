define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/user/withdraw/index' + location.search,
                    add_url: 'myadmin/user/withdraw/add',
                    edit_url: 'myadmin/user/withdraw/edit',
                    del_url: 'myadmin/user/withdraw/del',
                    table: 'myadmin_user_withdraw',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns:true,
                fixedNumber:1,
                fixedRightNumber: 1,
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', title: __('Id') },
                        { field: 'orderid', title: __('Orderid'), width: '210' },
                        { field: 'money', width: '90', title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'handfee', width: '90', title: __('HandFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'taxefee', width: '90', title: __('TaxeFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'settledmoney', width: '90', title: __('Settledmoney') },
                        { field: 'account', title: __('Account'), width: '160', formatter: Table.api.formatter.search },
                        { field: 'name', title: __('Realname'), formatter: Table.api.formatter.search },
                        { field: 'memo', title: __('Memo'), align: 'left', formatter: Table.api.formatter.content},
                        { field: 'type', visible: true, width: '90', title: __('Type'), searchList: Config.typeList, operate: 'LIKE', formatter: Table.api.formatter.label },
                        { field: 'status', width: '100', title: __('Status'), searchList: { "created": __('Status created'), "successed": __('Status successed'), "rejected": __('Status rejected') }, formatter: Table.api.formatter.status },
                        { field: 'user.username', width: '100', title: __('User'), operate: 'LIKE' },
                        { field: 'company.name', title: __('company'), formatter: Table.api.formatter.search },
                        { field: 'createtime', width: '140', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: true },
                        { field: 'updatetime', width: '140', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: false },
                        {
                            field: 'operate',
                            width: '90',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: []
                        }
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
                $("form[role=form]").data("validator-options", {
                    rules: {
                        settledmoney: function (element) {
                           return true
                        }
                    }
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});