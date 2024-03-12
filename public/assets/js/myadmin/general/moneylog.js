define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/moneylog/index',
                    //add_url: 'general/moneylog/add',
                    //edit_url: 'general/moneylog/edit',
                    //del_url: 'general/moneylog/del',
                    //multi_url: 'general/moneylog/multi',
                    table: 'company_money_log',
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
                        //{ field: 'id', width: '60', title: __('Id') },
                        { field: 'createtime', width: '170', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        { field: 'memo', title: __('Memo'), formatter: Table.api.formatter.search, align: 'left' },
                        { field: 'money', width: '120', title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'before', width: '120', title: __('Before'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'after', width: '120', title: __('After'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        //{ field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
                ]
            });


            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        myCommon: function () {},
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