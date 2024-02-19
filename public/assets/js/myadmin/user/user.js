define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user/user/index',
                    add_url: 'user/user/add',
                    edit_url: 'user/user/edit',
                    del_url: 'user/user/del',
                    multi_url: 'user/user/multi',
                    table: 'user',
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
                        { field: 'id', title: __('Id') },
                        { field: 'group_id', title: __('Group'), visible: false },
                        { field: 'group.name', title: __('Group') },
                        { field: 'info.avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false },
                        { field: 'info.username', title: __('Username'), operate: 'LIKE' },
                        { field: 'info.nickname', title: __('Nickname'), operate: 'LIKE' },
                        { field: 'info.email', title: __('Email'), operate: 'LIKE', visible: false },
                        { field: 'info.mobile', title: __('Mobile'), operate: 'LIKE' },
                        { field: 'info.gender', title: __('Gender'), visible: false, searchList: { 1: __('Male'), 0: __('Female') } },
                        { field: 'money', title: __('Money'), operate: 'BETWEEN', sortable: false },
                        { field: 'score', title: __('Score'), operate: 'BETWEEN', sortable: false },
                        { field: 'info.successions', title: __('Successions'), visible: false, operate: 'BETWEEN', sortable: false },
                        { field: 'info.maxsuccessions', title: __('Maxsuccessions'), visible: false, operate: 'BETWEEN', sortable: false },
                        { field: 'info.logintime', title: __('Logintime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true, visible: false },
                        { field: 'jointime', title: __('Jointime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: false },
                        { field: 'status', title: __('Status'), formatter: Table.api.formatter.status, searchList: { normal: __('Normal'), hidden: __('Hidden') } },
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'add',
                                    text: function (table, row, index) {
                                        return '变动余额';
                                    },
                                    title: function (table, row, index) {
                                        return '变动 [' + table['name'] + '] - 余额';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'user/moneylog/add?user_id=' + table['id'];
                                    }
                                },
                                
                                {
                                    name: 'add',
                                    text: function (table, row, index) {
                                        return '变动积分';
                                    },
                                    title: function (table, row, index) {
                                        return '变动 [' + table['name'] + '] - 积分';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'user/scorelog/add?user_id=' + table['id'];
                                    }
                                },
                            ]
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