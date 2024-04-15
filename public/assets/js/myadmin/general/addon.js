define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template'], function($, undefined, Backend, Table, Form, Template) {
    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/addon/index' + location.search,
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
                        //{ checkbox: true },
                        { field: 'title', width: '160', align: 'left', title: __('配置名称'), operate: 'LIKE' },
                        { field: 'intro', align: 'left', title: __('配置说明'), operate: 'LIKE' },
                        //{ field: 'status', title: __("Status"), width: '80', operate: false, searchList: { "normal": __('Normal'), "hidden": __('Hidden'), "expired": __('过期') }, formatter: Table.api.formatter.status },
                        { field: 'status', title: __("Status"), width: '80', operate: false, searchList: { "normal": __('Normal'), "hidden": __('Hidden'), "expired": __('过期') }, formatter: Table.api.formatter.status },

                        {
                            field: 'operate',
                            width: '100',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'level',
                                text: function(table, row, index) {
                                    return '配置';
                                },
                                title: function(table, row, index) {
                                    return '配置:' + table['title'] + '';
                                },
                                icon: 'fa fa-cog',
                                classname: 'btn btn-primary btn-xs btn-dialog',
                                url: function(table, row, index) {
                                    return 'general/addon/config/name/' + table['name'];
                                },
                            }],
                            formatter: Table.api.formatter.operate,

                        }
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function() {
            Controller.api.bindevent();
        },
        config: function() {
            Controller.api.bindevent();
        },
        api: {
            formatter: {},
            bindevent: function() {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});