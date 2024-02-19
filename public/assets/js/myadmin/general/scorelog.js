define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/scorelog/index',
                    //add_url: 'general/scorelog/add',
                    //edit_url: 'general/scorelog/edit',
                    //del_url: 'general/scorelog/del',
                    //multi_url: 'general/scorelog/multi',
                    table: 'company_score_log',
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
                        { field: 'memo', title: __('Memo'), formatter: Table.api.formatter.search, align: 'left' },
                        { field: 'score', width: '120', title: __('Score'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
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