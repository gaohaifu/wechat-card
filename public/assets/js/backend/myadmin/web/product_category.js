define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/web/product_category/index' + location.search,
                    add_url: 'myadmin/web/product_category/add' + (Config.type ? '/type/' + Config.type : '') + location.search,
                    edit_url: 'myadmin/web/product_category/edit',
                    del_url: 'myadmin/web/product_category/del',
                    multi_url: 'myadmin/web/product_category/multi',
                    import_url: 'myadmin/web/product_category/import',
                    table: 'myadmin_web_category',
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
                sortName: 'weigh',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', width: '60', title: __('ID') },
                        { field: 'icon', width: '60', title: __('Icon'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image },
                        { field: 'name', align: 'left', title: __('Name'), operate: 'LIKE' },
                        { field: 'createtime', width: '150', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
                        {
                            field: 'company_id', width: '160', title: __('Company'), visible: true,
                            searchList: function (column) { return Template('company', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.company) {
                                    return '<a href="javascript:;" class="searchit" data-field="company_id" data-value="' + row.company_id + '">' + row.company.name + '</a>';
                                }
                                return '<a href="javascript:;" class="searchit" data-field="company_id" data-value="0"><span class="text-danger"><i class="fa fa-circle"></i> 共同拥有</span></a>';
                            }
                        },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        { field: 'weigh', width: '80', title: __('Weigh'), operate: false },
                        {
                            field: 'operate',
                            width: '110',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'level',
                                    text: function (table, row, index) {
                                        return '发布';
                                    },
                                    title: function (table, row, index) {
                                        return '发布[' + table['name'] + ']内容';
                                    },
                                    icon: 'fa fa-plus',
                                    classname: 'btn btn-warning btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/web/product/add?category_id=' + table['id'];
                                    }
                                }, {
                                    name: 'level',
                                    text: function (table, row, index) {
                                        return '管理';
                                    },
                                    title: function (table, row, index) {
                                        return '' + table['name'] + '';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-addtabs',//addtabs
                                    url: function (table, row, index) {
                                        return 'myadmin/web/product/index?category_id=' + table['id'];
                                    },
                                    extend: 'data-area=\'["90%","90%"]\''
                                },]
                        }]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: 'myadmin/web/product_category/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', title: __('Id'), width: '80' },
                        { field: 'name', title: __('Name'), align: 'left' },
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            width: '160',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '140',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'Restore',
                                text: __('Restore'),
                                classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                icon: 'fa fa-rotate-left',
                                url: 'myadmin/web/product_category/restore',
                                refresh: true
                            },
                            {
                                name: 'Destroy',
                                text: __('Destroy'),
                                classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                icon: 'fa fa-times',
                                url: 'myadmin/web/product_category/destroy',
                                refresh: true
                            }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        myCommon: function () {
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