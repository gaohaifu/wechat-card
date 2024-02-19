define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/agreement/index' + location.search,
                    add_url: 'auth/agreement/add' + location.search,
                    edit_url: 'auth/agreement/edit',
                    del_url: 'auth/agreement/del',
                    multi_url: 'auth/agreement/multi',
                    import_url: 'auth/agreement/import',
                    table: 'myadmin_company_agreement',
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
                        { field: 'id', width: '60', title: __('ID') },
                        { field: 'agreement_no', width: '150', title: __('AgreementNo') },
                        { field: 'name', align: 'left', title: __('Name') },
                        {
                            field: 'player_id', width: '150',  title: __('Player'), visible: true,
                            searchList: function (column) { return Template('player', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.player) {
                                    return '<a href="javascript:;" class="searchit" data-field="player_id" data-value="' + row.player_id + '">' + row.player.name + '</a>';
                                }
                                return '-';
                            }
                        },
                        { field: 'starttime', width: '100', title: __('StartTime') },
                        { field: 'expiredtime', width: '100', title: __('ExpiredTime') },
                        { field: 'createtime', width: '150', title: __('Createtime'), visible: false, operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate', width: '60', title: __('Operate'), table: table, events: Table.api.events.operate,
                            formatter: function (value, row, index) {
                                var extend = $.fn.bootstrapTable.defaults.extend;
                                if (row.status == 'normal') {
                                    $(table).data("operate-del", null)
                                } else {
                                    $(table).data("operate-del", true)
                                }
                                if (row.status == 'expired') {
                                    $(table).data("operate-edit", true)
                                } else {
                                    $(table).data("operate-edit", null)
                                }
                                return Table.api.formatter.operate.call(this, value, row, index);
                            },
                            buttons: [
                                {
                                    name: 'detail',
                                    text: function (table, row, index) {
                                        return '协议';
                                    },
                                    title: function (table, row, index) {
                                        return '协议详情';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: 'auth/agreement/detail?ids={ids}'
                                }
                            ]
                        }
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
                url: 'auth/agreement/recyclebin' + location.search,
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
                                url: 'auth/agreement/restore',
                                refresh: true
                            },
                            {
                                name: 'Destroy',
                                text: __('Destroy'),
                                classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                icon: 'fa fa-times',
                                url: 'auth/agreement/destroy',
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