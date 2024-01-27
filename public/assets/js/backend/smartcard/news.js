define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/news/index' + location.search,
                    add_url: 'smartcard/news/add',
                    edit_url: 'smartcard/news/edit',
                    del_url: 'smartcard/news/del',
                    multi_url: 'smartcard/news/multi',
                    import_url: 'smartcard/news/import',
                    table: 'smartcard_news',
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
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'picimages', title: __('Picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'smartcardcompany.id', title: __('Smartcardcompany.id')},
                        {field: 'smartcardcompany.name', title: __('Smartcardcompany.name'), operate: 'LIKE'},
                        {field: 'smartcardcompany.shortname', title: __('Smartcardcompany.shortname'), operate: 'LIKE'},
                        {field: 'smartcardcompany.address', title: __('Smartcardcompany.address'), operate: 'LIKE'},
                        {field: 'smartcardcompany.phone', title: __('Smartcardcompany.phone')},
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