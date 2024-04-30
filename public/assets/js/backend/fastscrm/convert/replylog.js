define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/convert/replylog/index' + location.search,
                    multi_url: 'fastscrm/convert/replylog/multi',
                    import_url: 'fastscrm/convert/replylog/import',
                    table: 'fastscrm_reply_log',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                showToggle: false,
                // commonSearch: false,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'reply_name', title: __('Reply_name'), operate: false,formatter: Controller.api.formatter.reply_name},
                        {field: 'entry', title: __('Entry'), searchList: {"single_chat_tools":__('Entry single_chat_tools'),"group_chat_tools":__('Entry group_chat_tools'),"chat_attachment":__('Entry chat_attachment')}, formatter: Table.api.formatter.normal},
                        {field: 'worker_name', title: __('Worker_name'), operate: false,formatter: Controller.api.formatter.worker_name},
                        {field: 'user_name', title: __('User_name'), operate: false,formatter: Controller.api.formatter.user_name},
                        {field: 'chat_name', title: __('Chat_name'), operate: false,formatter: Controller.api.formatter.chat_name},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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
            },
            formatter: {//渲染的方法
                reply_name: function (value, row, index) {
                    return row.reply_name;
                },
                user_name: function (value, row, index) {
                    return row.user_name;
                },
                chat_name: function (value, row, index) {
                    return row.chat_name;
                },
                worker_name: function (value, row, index) {
                    return row.worker_name;
                },
            },
        }
    };
    return Controller;
});
