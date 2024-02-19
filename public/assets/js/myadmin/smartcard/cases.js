define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/cases/index' + location.search,
                    add_url: 'smartcard/cases/add',
                    edit_url: 'smartcard/cases/edit',
                    del_url: 'smartcard/cases/del',
                    multi_url: 'smartcard/cases/multi',
                    import_url: 'smartcard/cases/import',
                    table: 'smartcard_case',
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
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'smartcardcompany.id', title: __('Smartcardcompany.id')},
                        {field: 'smartcardcompany.name', title: __('Smartcardcompany.name'), operate: 'LIKE'},
                        {field: 'smartcardcompany.url', title: __('Smartcardcompany.url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'smartcardcompany.intro', title: __('Smartcardcompany.intro'), operate: 'LIKE'},
                        {field: 'smartcardcompany.shortname', title: __('Smartcardcompany.shortname'), operate: 'LIKE'},
                        {field: 'smartcardcompany.begintime', title: __('Smartcardcompany.begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcompany.endtime', title: __('Smartcardcompany.endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcompany.licenseimage', title: __('Smartcardcompany.licenseimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'smartcardcompany.licensenumber', title: __('Smartcardcompany.licensenumber'), operate: 'LIKE'},
                        {field: 'smartcardcompany.picimages', title: __('Smartcardcompany.picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'smartcardcompany.videofiles', title: __('Smartcardcompany.videofiles'), operate: false},
                        {field: 'smartcardcompany.address', title: __('Smartcardcompany.address'), operate: 'LIKE'},
                        {field: 'smartcardcompany.phone', title: __('Smartcardcompany.phone')},
                        {field: 'smartcardcompany.createtime', title: __('Smartcardcompany.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'smartcardcompany.weigh', title: __('Smartcardcompany.weigh')},
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