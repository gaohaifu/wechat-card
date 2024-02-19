define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/goods/index' + location.search,
                    add_url: 'smartcard/goods/add',
                    edit_url: 'smartcard/goods/edit',
                    del_url: 'smartcard/goods/del',
                    multi_url: 'smartcard/goods/multi',
                    import_url: 'smartcard/goods/import',
                    table: 'smartcard_goods',
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
                        {field: 'picimages', title: __('Picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'recommenddata', title: __('Recommenddata'), searchList: {"0":__('Recommenddata 0'),"1":__('Recommenddata 1')}, formatter: Table.api.formatter.normal},
                        {field: 'category_id', title: __('Category_id')},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'tags', title: __('Tags'), operate: 'LIKE'},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcategory.id', title: __('Smartcardcategory.id')},
                        {field: 'smartcardcategory.name', title: __('Smartcardcategory.name'), operate: 'LIKE'},
                        {field: 'smartcardcategory.picimage', title: __('Smartcardcategory.picimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'smartcardcategory.introtext', title: __('Smartcardcategory.introtext'), operate: 'LIKE'},
                        {field: 'smartcardcategory.company_id', title: __('Smartcardcategory.company_id')},
                        {field: 'smartcardcategory.updatetime', title: __('Smartcardcategory.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcategory.createtime', title: __('Smartcardcategory.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcategory.weigh', title: __('Smartcardcategory.weigh')},
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