define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/crm/groupchat/index' + location.search,
                    multi_url: 'fastscrm/crm/groupchat/multi',
                    import_url: 'fastscrm/crm/groupchat/import',
                    table: 'fastscrm_group_chat',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'owner', title: __('Worker_name'), operate: '=',searchList: $.getJSON("fastscrm/company/worker/searchfind?field=row[id]"),formatter:Controller.api.formatter.worker_name},
                        {field: 'notice', title: __('Notice'), operate: 'LIKE'},
                        {field: 'member_total', operate: false, title: __('Member_total'),formatter:Controller.api.formatter.member_total},
                        {field: 'admin_total',operate: false, title: __('Admin_total'),formatter:Controller.api.formatter.admin_total},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'memberlist',
                                    title: __('群成员'),
                                    text: '群成员',
                                    classname: 'btn btn-xs btn-success btn-membertabs',
                                    icon: 'fa fa-users',
                                },
                                {
                                    name: 'adminlist',
                                    title: __('群管理'),
                                    text: '群管理',
                                    classname: 'btn btn-xs btn-primary btn-admintabs',
                                    icon: 'fa fa-users',
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            // 同步
            $(document).on("click", ".btn-sync", function () {
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
            $(document).on("click", ".btn-membertabs", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/crm/groupuser?group_id='+row.id;
                Backend.api.addtabs(url, '群成员', '');
            });
            $(document).on("click", ".btn-admintabs", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/crm/groupadmin?group_id='+row.id;
                Backend.api.addtabs(url, '群成员', '');
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
            formatter: {//渲染的方法
                worker_name: function (value, row, index) {
                    return row.worker_name;
                },
                member_total: function (value, row, index) {
                    return row.member_total;
                },
                admin_total: function (value, row, index) {
                    return row.admin_total;
                },
            }
        }
    };
    return Controller;
});
