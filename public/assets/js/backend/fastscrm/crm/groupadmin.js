define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/crm/groupadmin/index' + location.search,
                    // add_url: 'fastscrm/crm/groupadmin/add',
                    // edit_url: 'fastscrm/crm/groupadmin/edit',
                    // del_url: 'fastscrm/crm/groupadmin/del',
                    multi_url: 'fastscrm/crm/groupadmin/multi',
                    import_url: 'fastscrm/crm/groupadmin/import',
                    table: 'fastscrm_group_admin',
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
                        // {field: 'group_id', title: __('Group_id')},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image,formatter:Controller.api.formatter.avatar, operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE',formatter:Controller.api.formatter.name},
                        {field: 'chat_name', title: __('Chat_name'), operate: false,formatter:Controller.api.formatter.chat_name},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                avatar: function (value, row, index) {
                    return '<img class="img-sm img-center" src="'+row.avatar+'">';
                },
                name: function (value, row, index) {
                    return row.name;
                },
                chat_name: function (value, row, index) {
                    return row.chat_name;
                }
            }
        }
    };
    return Controller;
});
