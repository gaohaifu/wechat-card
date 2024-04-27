define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/company/index/index' + location.search,
                    add_url: 'myadmin/company/index/add' + (Config.group_id ? '/group_id/' + Config.group_id : ''),
                    edit_url: 'myadmin/company/index/edit',
                    del_url: 'myadmin/company/index/del',
                    multi_url: 'myadmin/company/index/multi',
                    import_url: 'myadmin/company/index/import',
                    table: 'company',
                },
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    if (Config.group_id && Config.group_id != 'null') {
                        filter.group_id = Config.group_id;
                        op.group_id = "=";
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
                sortName: 'id',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', width: '80', title: __('ID') },
                        { field: 'identifier', width: '100', title: __('Identifier'), visible: false, operate: 'LIKE' },
                        { field: 'type', visible: true, width: '60', title: __('Type'), searchList: Config.typeList, operate: 'FINDIN', formatter: Table.api.formatter.label },
                        { field: 'group.name', width: '90', title: __('Group'), visible: false, searchList: Config.groupList, operate: 'LIKE', formatter: Table.api.formatter.label },
                        { field: 'logo', width: '60', title: __('Logo'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image },
                        { field: 'name', title: __('CompanyName'), operate: 'LIKE', align: 'left' },
                        { field: 'label', width: '100', title: __('Label'), operate: 'LIKE', align: 'left', formatter: Table.api.formatter.flag, visible: false },
                        { field: 'player', title: __('Player'), width: '80', operate: 'FINDIN', align: 'left', searchList: Config.playerList, formatter: Table.api.formatter.flag },
                        { field: 'money', title: __('Money'), width: '80' },
                        { field: 'score', title: __('Score'), width: '80' },
                        { field: 'admin_limit', width: '100', title: __('AdminIimit'), visible: false },
                        { field: 'address', width: '200', title: __('Address'), visible: false, operate: 'LIKE', align: 'left', formatter: Table.api.formatter.content },
                        { field: 'user.username', width: '120', title: __('User'), visible: true },
                        { field: 'status', width: '80', title: __('Status'), operate: 'FINDIN', searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate',
                            width: '240',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'audit',
                                    text: function (table, row, index) {
                                        return '审核';
                                    },
                                    title: function (table, row, index) {
                                        return '审核 [' + table['name'] + '] - 角色';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/company/index/edit?type=audit&ids=' + table['id'];
                                    }
                                },
                                {
                                    name: 'add',
                                    text: function (table, row, index) {
                                        return '签约';
                                    },
                                    title: function (table, row, index) {
                                        return '签约 [' + table['name'] + '] - 角色';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/company/agreement/add?company_id=' + table['id'];
                                    }
                                },
                                {
                                    name: 'add',
                                    text: function (table, row, index) {
                                        return '协议管理';
                                    },
                                    title: function (table, row, index) {
                                        return '[' + table['name'] + '] - 协议管理';
                                    },
                                    icon: 'fa fa-file-o',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/company/agreement/index?company_id=' + table['id'];
                                    },
                                    extend: 'data-area=\'["90%","90%"]\''
                                },
                                {
                                    name: 'admin',
                                    text: function (table, row, index) {
                                        return '账号管理';
                                    },
                                    title: function (table, row, index) {
                                        return '' + table['name'] + ' - 账号管理';
                                    },
                                    icon: 'fa fa-users',
                                    classname: 'btn btn-info btn-xs btn-dialog',
                                    url: function (table, row, index) {
                                        return 'myadmin/auth/admin/index/company_id/' + table['id'];
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
                url: 'myadmin/company/index/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', title: __('Id') },
                        { field: 'name', title: __('Name'), align: 'left' },
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '150',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [{
                                name: 'Restore',
                                text: __('Restore'),
                                classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                icon: 'fa fa-rotate-left',
                                url: 'myadmin/company/index/restore',
                                refresh: true
                            },
                            {
                                name: 'Destroy',
                                text: __('Destroy'),
                                classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                icon: 'fa fa-times',
                                url: 'myadmin/company/index/destroy',
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
        myCommon: function () {
            /*
            $("#c-address_area").on("cp:updated", function() {
                var citypicker = $(this).data("citypicker");
                var province = citypicker.getCode("province") || '';
                var city = citypicker.getCode("city") || '';
                var district = citypicker.getCode("district") || '';
                var code = province + ',' + city + ',' + district;
                $("#c-address_code").val(code);
                $("#c-address_province").val(province);
                $("#c-address_city").val(city);
                $("#c-address_district").val(district);
            });
            */
            $("#c-group_id").data("eSelect", function (obj) {
                let label = obj.label || null;
                $('#label').selectPageData(JSON.parse(label));
                let price = obj.price || 0;
                $('#c-price').val(price);
            });
        },
        add: function () {
            this.myCommon();
            var si;
            $(document).on("keyup", "#c-name", function () {
                var value = $(this).val();
                if (value != '' && !value.match(/\n/)) {
                    clearTimeout(si);
                    si = setTimeout(function () {
                        var nickname = $("#c-name").val().replace(/\s+/g, "");
                        $("#c-name").val(nickname);
                        $("#c-nickname").val(nickname);
                    }, 100);
                }
            });

            Controller.api.bindevent();
        },
        edit: function () {
            this.myCommon();
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                founder: function (value, row, index) {
                    if (row.founder) {
                        return '<a href="/myadmin?company_identify=' + row.identifier + '" target="_blank" class="label bg-green" title="' + row.founder.nickname + '">' + row.founder.username + '</a>'
                    }
                    return '-';
                },
            }
        }
    };
    return Controller;
});