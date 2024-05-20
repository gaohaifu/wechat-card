define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/company/depart/index' + location.search,
                    add_url: 'fastscrm/company/depart/add',
                    edit_url: 'fastscrm/company/depart/edit',
                    del_url: 'fastscrm/company/depart/del',
                    multi_url: 'fastscrm/company/depart/multi',
                    import_url: 'fastscrm/company/depart/import',
                    table: 'fastscrm_depart',
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
                        // {field: 'depart_id', title: __('Depart_id')},
                        {field: 'name', title: __('Name'), align: 'left', formatter: Controller.api.formatter.name},
                        // {field: 'department_leader', title: __('Department_leader'), operate: 'LIKE'},
                        // {field: 'parentid', title: __('Parentid')},
                        {field: 'order', title: __('Order')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                pagination: false,
                search: false,
                commonSearch: false,
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            //期初同步
            $(document).on("click", ".btn-start", function () {
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
            //同步部门
            $(document).on("click", ".btn-sync", function () {
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
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
            },
            formatter: {
                name: function (value, row, index) {
                    value = value.toString().replace(/(&|&amp;)nbsp;/g, '&nbsp;');
                    return  value ;
                }
            }
        }
    };
    return Controller;
});
