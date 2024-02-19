define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/message/index' + location.search,
                    add_url: 'smartcard/message/add',
                    edit_url: 'smartcard/message/edit',
                    del_url: 'smartcard/message/del',
                    multi_url: 'smartcard/message/multi',
                    import_url: 'smartcard/message/import',
                    table: 'smartcard_message',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'smartcardcompany.name', title: __('Smartcardcompany.name'), operate: 'LIKE'},
                        {field: 'staff_id', title: __('Staff_id')},
                        {field: 'invite_id', title: __('Invite_id')},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'position', title: __('Position'), operate: 'LIKE'},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statusdata', title: __('Statusdata'), searchList: {"1":__('Statusdata 1'),"2":__('Statusdata 2'),"3":__('Statusdata 3')}, formatter: Table.api.formatter.normal},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                       
                        
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