define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/general/domain/index' + location.search,
                    add_url: 'myadmin/general/domain/add' + (Config.company_id ? '/company_id/' + Config.company_id : ''),
                    edit_url: 'myadmin/general/domain/edit',
                    del_url: 'myadmin/general/domain/del',
                    multi_url: 'myadmin/general/domain/multi',
                    import_url: 'myadmin/general/domain/import',
                    table: 'myadmin_domain',
                },
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    if (Config.company_id && Config.company_id != 'null') {
                        filter.company_id = Config.company_id;
                        op.company_id = "=";
                    }
                    params.filter = JSON.stringify(filter);
                    params.op = JSON.stringify(op);
                    return params;
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
                        {
                            field: 'company_id', width: '160', title: __('Company'), visible: true,
                            searchList: function (column) { return Template('company', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.company) {
                                    return '<a href="javascript:;" class="searchit" data-field="company_id" data-value="' + row.company_id + '">' + row.company.name + '</a>';
                                }
                                return '-';
                            }
                        }, {
                            field: 'operate', width: '110', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate, buttons: [{
                                name: 'level',
                                text: function (table, row, index) {
                                    return '安装证书';
                                },
                                title: function (table, row, index) {
                                    return '' + table['name'] + ' - 安装证书';
                                },
                                icon: 'fa fa-file-o',
                                classname: 'btn btn-info btn-xs btn-dialog',
                                url: function (table, row, index) {
                                    return 'myadmin/general/domain/install/domain_id/' + table['id'];
                                },
                            }]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        install: function () {
            Controller.api.bindevent();
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