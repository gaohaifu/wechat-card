define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/message/sendlog/index' + location.search,
                    add_url: 'fastscrm/message/sendlog/add',
                    edit_url: 'fastscrm/message/sendlog/edit',
                    del_url: 'fastscrm/message/sendlog/del',
                    multi_url: 'fastscrm/message/sendlog/multi',
                    import_url: 'fastscrm/message/sendlog/import',
                    table: 'fastscrm_webhook_send_log',
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
                        {field: 'send.name', title: __('Send_id'), operate: 'LIKE'},
                        {field: 'webhook.name', title: __('Webhook_id'), operate: 'LIKE'},
                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'username', title: __('Username'), operate: 'LIKE'},
                        {field: 'ip', title: __('Ip'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"-1":__('Status -1'),"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'message', title: __('Message'), operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
