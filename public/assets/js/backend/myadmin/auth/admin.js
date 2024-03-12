define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/auth/admin/index' + location.search,
                    add_url: 'myadmin/auth/admin/add/company_id/' + Config.company_id,
                    edit_url: 'myadmin/auth/admin/edit',
                    del_url: 'myadmin/auth/admin/del',
                    multi_url: 'myadmin/auth/admin/multi',
                }, queryParams: function (params) {
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

            //在表格内容渲染完成后回调的事件
            table.on('post-body.bs.table', function (e, json) {
                $("tbody tr[data-index]", this).each(function () {
                    /*
                    if (parseInt($("td:eq(1)", this).text()) == Config.admin.id) {
                        $("input[type=checkbox]", this).prop("disabled", true);
                    }
                    */
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        { field: 'state', checkbox: true, },
                        { field: 'id', title: 'ID', width: '90' },
                        { field: 'username', title: __('Username') , width: '150'},
                        { field: 'nickname', title: __('Nickname'), width: '150' },
                        { field: 'groups_text', title: __('Group'), align: 'left', operate: false, formatter: Table.api.formatter.label },
                        { field: 'company.name', align: 'left', title: __('company'), formatter: Table.api.formatter.search },
                        { field: 'logintime', title: __('Login time'), width: '150', formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: false },
                        { field: 'status', width: '90', title: __("Status"), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate', width: '90', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
