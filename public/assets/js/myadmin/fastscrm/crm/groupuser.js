define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/crm/groupuser/index' + location.search,
                    // add_url: 'fastscrm/crm/groupuser/add',
                    // edit_url: 'fastscrm/crm/groupuser/edit',
                    // del_url: 'fastscrm/crm/groupuser/del',
                    multi_url: 'fastscrm/crm/groupuser/multi',
                    import_url: 'fastscrm/crm/groupuser/import',
                    table: 'fastscrm_group_user',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        // {field: 'group_id', title: __('Group_id')},
                        // {field: 'userid', title: __('Userid'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image,formatter:Controller.api.formatter.avatar, operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'group_nickname', title: __('Group_nickname'), operate: 'LIKE'},
                        {field: 'chat_name', title: __('Chat_name'), operate: false,formatter:Controller.api.formatter.chat_name},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        // {field: 'unionid', title: __('Unionid'), operate: 'LIKE'},
                        {field: 'join_scene', title: __('Join_scene'), searchList: {"1":__('Join_scene 1'),"2":__('Join_scene 2')}, formatter: Table.api.formatter.normal},

                        {field: 'join_time', title: __('Join_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                         // {field: 'invitor', title: __('Invitor')},
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
                chat_name: function (value, row, index) {
                    return row.chat_name;
                }
            }
        }
    };
    return Controller;
});
