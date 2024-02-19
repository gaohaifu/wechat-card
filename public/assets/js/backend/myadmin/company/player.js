define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/company/player/index' + location.search,
                    add_url: 'myadmin/company/player/add',
                    edit_url: 'myadmin/company/player/edit',
                    del_url: 'myadmin/company/player/del',
                    multi_url: 'myadmin/company/player/multi',
                    import_url: 'myadmin/company/player/import',
                    table: 'myadmin_company_player',
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
                        {
                            field: 'company_id', align: 'left', title: __('Company'), visible: true,
                            searchList: function (column) { return Template('company', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.company) {
                                    return '<a href="javascript:;" class="searchit" data-field="company_id" data-value="' + row.company_id + '">' + row.company.name + '</a>';
                                }
                                return '-';
                            }
                        },
                        {
                            field: 'player_id', width: '160', title: __('Player'), visible: true,
                            searchList: function (column) { return Template('player', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.player) {
                                    return '<a href="javascript:;" class="searchit" data-field="player_id" data-value="' + row.player_id + '">' + row.player.name + '</a>';
                                }
                                return '-';
                            }
                        },
                        { field: 'expiredtime', width: '100', title: __('ExpiredTime') },
                        { field: 'createtime', width: '150', title: __('Createtime'), visible: false, operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate', width: '110', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'add',
                                    text: function (table, row, index) {
                                        return '续约';
                                    },
                                    title: function (table, row, index) {
                                        return '续约 [' + table['player']['name'] + '] - 角色';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/company/agreement/add?player_id=' + table['player_id'] + '&company_id=' + table['company_id'];
                                    }
                                }, {
                                    name: 'admin',
                                    text: function (table, row, index) {
                                        return '协议管理';
                                    },
                                    title: function (table, row, index) {
                                        return '身份' + table['player']['name'] + ' - 协议管理';
                                    },
                                    icon: 'fa fa-users',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/company/agreement/index?player_id=' + table['player_id'] + '&company_id=' + table['company_id'];
                                    },
                                    extend: 'data-area=\'["90%","90%"]\''
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