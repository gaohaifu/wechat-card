define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/company/index' + location.search,
                    add_url: 'smartcard/company/add',
                    edit_url: 'smartcard/company/edit',
                    del_url: 'smartcard/company/del',
                    multi_url: 'smartcard/company/multi',
                    import_url: 'smartcard/company/import',
                    table: 'smartcard_company',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'administrators_ids', title: __('Administrators_ids')},
                        //{field: 'administrators_ids', title: __('Administrators_ids'), operate:false,formatter: Table.api.formatter.label},
                        {field: 'ckdadministrators.nickname', title: __('Ckdadministrators.nickname')},
                        {field: 'url', title: __('Url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'intro', title: __('Intro'), operate: 'LIKE'},
                        {field: 'shortname', title: __('Shortname'), operate: 'LIKE'},
                        {field: 'begintime', title: __('Begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'licenseimage', title: __('Licenseimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'licensenumber', title: __('Licensenumber'), operate: 'LIKE'},
                        {field: 'picimages', title: __('Picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'videofiles', title: __('Videofiles'), operate: false},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'latlng', title: __('Latlng'), operate: 'false'},
                        {field: 'phone', title: __('Phone')},
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