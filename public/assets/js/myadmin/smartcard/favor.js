define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/favor/index' + location.search,
                    add_url: 'smartcard/favor/add',
                    edit_url: 'smartcard/favor/edit',
                    del_url: 'smartcard/favor/del',
                    multi_url: 'smartcard/favor/multi',
                    import_url: 'smartcard/favor/import',
                    table: 'smartcard_favor',
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
                        {field: 'tags_id', title: __('Tags_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'staff_id', title: __('Staff_id')},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'smartcardtags.name', title: __('Smartcardtags.name'), operate: 'LIKE'},
                        {field: 'smartcardstaff.name', title: __('Smartcardstaff.name'), operate: 'LIKE'},
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