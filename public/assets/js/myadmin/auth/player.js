define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'auth/player/index' + location.search,
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
                        { field: 'id', width: '60', title: __('ID') },
                        {
                            field: 'player_id', align: 'left', title: __('Player'), visible: true,
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
                                    name: 'agr',
                                    text: function (table, row, index) {
                                        return '续约';
                                    },
                                    title: function (table, row, index) {
                                        return '续约 [' + table['player']['name'] + '] - 角色';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: 'auth/agreement/add?player_id={player_id}'
                                }
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