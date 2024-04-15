define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/addon/index' + location.search,
                    add_url: 'myadmin/addon/add',
                    edit_url: 'myadmin/addon/edit',
                    del_url: 'myadmin/addon/del',
                    multi_url: 'myadmin/addon/multi',
                    import_url: 'myadmin/addon/import',
                    table: 'myadmin_addons',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'name',
                sortName: 'name',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'title', width: '110', align: 'left', title: __('配置名称'), operate: 'LIKE' },
                        { field: 'intro', align: 'left', title: __('配置说明'), operate: 'LIKE' },
                        {
                            field: 'operate',
                            width: '110',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'level',
                                text: function (table, row, index) {
                                    return '已配置企业';
                                },
                                title: function (table, row, index) {
                                    return '查看:[' + table['title'] + ']配置';
                                },
                                icon: 'fa fa-list',
                                classname: 'btn btn-info btn-xs btn-dialog',
                                url: function (table, row, index) {
                                    return 'myadmin/addon/buy/name/' + table['name'];
                                },
                                extend: 'data-area=\'["90%","90%"]\''
                            }],
                            formatter: Table.api.formatter.operate,
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        buy: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/addon/buy' + location.search,
                    edit_url: false,
                    del_url: 'myadmin/addon/buydel',
                    multi_url: 'myadmin/addon/buymulti',
                    table: 'myadmin_addons',
                },
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    if (Config.name && Config.name != 'null') {
                        filter.name = Config.name;
                        op.name = "=";
                    }
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
                sortName: 'endtime',
                columns: [
                    [
                        //{ checkbox: true },
                        { field: 'id', width: '80', title: __('Id'), visible: false, operate: false },
                        
                        {
                            field: 'company_id', width: '170', align: 'left', title: __('所属企业'), operate: '=',                            
                            searchList: function (column) { return Template('company', {}); },
                            formatter: function (value, row, index) { 
                                return row.company.name
                            }
                        },
                        { field: 'info.title', align: 'left', title: __('配置名称'), operate: false },
                        //{ field: 'begintime', width: '170', title: __('开始时间'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        //{ field: 'endtime', width: '170', title: __('到期时间'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },

                        //{ field: 'buy', width: '100', title: __('buy'), operate: 'LIKE' },
                        { field: 'status', title: __("Status"), width: '80', operate: false, searchList: { "normal": __('Normal'), "hidden": __('Hidden'), "expired": __('过期') }, formatter: Table.api.formatter.status },
                        { field: 'isuse', title: __("启用"), width: '80', operate: '=', searchList: { "1": __('开启'), "0": __('关闭') }, confirm: "您确定要操作吗？", formatter: Table.api.formatter.toggle },
                        {
                            field: 'operate',
                            width: '90',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'level',
                                title: function (table, row, index) {
                                    return '配置:' + table['info']['title'] + '';
                                },
                                icon: 'fa fa-cog',
                                classname: 'btn btn-primary btn-xs btn-dialog',
                                url: function (table, row, index) {
                                    return 'myadmin/addon/config?company_id=' + table.company_id + '&name=' + table.name;
                                }
                            }],
                            formatter: Table.api.formatter.operate,
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
        config: function () {
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