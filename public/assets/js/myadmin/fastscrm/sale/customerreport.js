define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/sale/customerreport/index' + location.search,
                    multi_url: 'fastscrm/sale/customerreport/multi',
                    import_url: 'fastscrm/sale/customerreport/import',
                    table: 'fastscrm_sale_log',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                search:false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'sale_id', title: __('Sale_id'), operate: false,formatter:Controller.api.formatter.sale_title},
                        {field: 'userid', title: __('Userid'), operate: '=',searchList: $.getJSON("fastscrm/company/worker/searchfind"),formatter:Controller.api.formatter.owner_name},
                        {field: 'external_userid', title: __('External_userid'), operate: false,formatter:Controller.api.formatter.customer_name},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'send_status', title: __('Send_status'), searchList: {"0":__('Send_status 0'),"1":__('Send_status 1'),"2":__('Send_status 2'),"3":__('Send_status 3')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'send_time', title: __('Send_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'message',operate: false,  title: __('Message')},
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
            },
            formatter: {//渲染的方法
                sale_title: function (value, row, index) {
                    return row.sale_title;
                },
                owner_name: function (value, row, index) {
                    return row.owner_name;
                },
                customer_name: function (value, row, index) {
                    return row.customer_name;
                }
            }
        }
    };
    return Controller;
});
