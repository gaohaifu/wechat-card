define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/theme/index' + location.search,
                    add_url: 'smartcard/theme/add',
                    edit_url: 'smartcard/theme/edit',
                    del_url: 'smartcard/theme/del',
                    multi_url: 'smartcard/theme/multi',
                    import_url: 'smartcard/theme/import',
                    table: 'smartcard_theme',
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
                        {field: 'name', title: __('Nametext'), operate: 'LIKE'},
                        {field: 'colour', title: __('Colour'), operate: 'LIKE'},
                        {field: 'fontcolor', title: __('Fontcolor'), operate: 'LIKE'},
                        {field: 'backgroundimage', title: __('Backgroundimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'cardimage', title: __('Cardimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statusdata', title: __('Statusdata'), searchList: {"1":__('Statusdata 1'),"2":__('Statusdata 2')}, formatter: Table.api.formatter.normal},
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