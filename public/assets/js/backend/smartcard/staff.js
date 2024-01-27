define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/staff/index' + location.search,
                    add_url: 'smartcard/staff/add',
                    edit_url: 'smartcard/staff/edit',
                    del_url: 'smartcard/staff/del',
                    multi_url: 'smartcard/staff/multi',
                    import_url: 'smartcard/staff/import',
                    table: 'smartcard_staff',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'smartcardcompany.name', title: __('Smartcardcompany.name'), operate: 'LIKE'},
                        {field: 'position', title: __('Position'), operate: 'LIKE'},
                        {field: 'tags_ids', title: __('Tags_ids'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'wechat', title: __('Wechat'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'statusdata', title: __('Statusdata'), searchList: {"1":__('Statusdata 1'),"2":__('Statusdata 2'),"3":__('Statusdata 3'),"4":__('Statusdata 4')}, formatter: Table.api.formatter.normal},
                        {field: 'visit', title: __('Visit')},
                        {field: 'favor', title: __('Favor')},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'picimages', title: __('Picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'videofiles', title: __('Videofiles'), operate: false},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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