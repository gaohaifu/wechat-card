define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xccms/xccmsguestbook/index' + location.search,
                    add_url: 'xccms/xccmsguestbook/add',
                    edit_url: 'xccms/xccmsguestbook/edit',
                    del_url: 'xccms/xccmsguestbook/del',
                    multi_url: 'xccms/xccmsguestbook/multi',
                    import_url: 'xccms/xccmsguestbook/import',
                    table: 'xccms_guestbook',
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
                searchFormVisible: true,
                search: false,
                columns: [
                    [
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'guest_book_type', title: __('Guest_book_type'), searchList: {"product":__('产品'),"content":__('内容'),"page":__('单页'),"news":__('新闻'),"aboutus":__('关于我们'),"contactus":__('联系我们'),"partner":__('合作伙伴'),"job":__('招聘')}, formatter: Table.api.formatter.flag},
                        {field: 'resource_id', title: __('Resource_id'), formatter: function(value, row) {
                            return row['resource_title'];
                        }, operate: false},
                        {field: 'realname', title: __('Realname'), operate: 'LIKE'},
                        {field: 'tel', title: __('Tel'), operate: 'LIKE'},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'state', title: __('State'), searchList: {"0":__('State 0'),"1":__('State 1'),"-1":__('State -1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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