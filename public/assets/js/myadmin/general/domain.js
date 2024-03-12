define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/domain/index' + location.search,
                    add_url: 'general/domain/add',
                    edit_url: 'general/domain/edit',
                    del_url: 'general/domain/del',
                    table: 'myadmin_domain',
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
                        { checkbox: true },
                        { field: 'install', width: '60', title: __('Install'), formatter: Table.api.formatter.label },
                        { field: 'name', align: 'left', title: __('域名'), operate: 'LIKE' },
                        { field: 'ssl_certificate', align: 'left', title: __('密钥(KEY)'), operate: 'LIKE', formatter: Table.api.formatter.content },
                        { field: 'ssl_certificate_key', align: 'left', title: __('证书(PEM)'), operate: 'LIKE', formatter: Table.api.formatter.content },
                        { field: 'createtime', width: '150', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        { field: 'operate', width: '110', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function() {
            Controller.api.bindevent();
        },
        edit: function() {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function() {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});