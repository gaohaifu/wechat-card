define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/web/single/index' + location.search,
                    add_url: 'myadmin/web/single/add',
                    edit_url: 'myadmin/web/single/edit',
                    del_url: 'myadmin/web/single/del',
                    multi_url: 'myadmin/web/single/multi',
                    import_url: 'myadmin/web/single/import',
                    table: 'myadmin_web_single',
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
                        { field: 'cover', width: '60', title: __('Cover'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image },
                        { field: 'name', width: '160', align: 'left', title: __('Name'), operate: 'LIKE' , formatter: Table.api.formatter.content},
                        { field: 'title', align: 'left', title: __('Title'), operate: 'LIKE', formatter: Table.api.formatter.content },
                        { field: 'views', width: '80', title: __('Views') },
                        { field: 'createtime', width: '150', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
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
                        },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        { field: 'weigh', width: '60', title: __('Weigh'), operate: false },
                        { field: 'operate', width: '110', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate }
                    ]
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
                url: 'myadmin/web/single/recyclebin' + location.search,
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
                                url: 'myadmin/web/single/restore',
                                refresh: true
                            },
                            {
                                name: 'Destroy',
                                text: __('Destroy'),
                                classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                icon: 'fa fa-times',
                                url: 'myadmin/web/single/destroy',
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