define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/company/group/index' + location.search,
                    add_url: 'myadmin/company/group/add' + (Config.type ? '/type/' + Config.type : '') + location.search,
                    edit_url: 'myadmin/company/group/edit',
                    del_url: 'myadmin/company/group/del',
                    multi_url: 'myadmin/company/group/multi',
                    import_url: 'myadmin/company/group/import',
                    table: 'myadmin_company_group',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        { checkbox: true },
                        { field: 'id', width: '60', title: __('Id') },
                        { field: 'icon', width: '60', title: __('Icon'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image },
                        { field: 'name', align: 'left', title: __('Name'), operate: 'LIKE', formatter: Table.api.formatter.content },
                        { field: 'labelname', width: '150', title: __('Label'), operate: false, align: 'left', formatter: Table.api.formatter.label },
                        { field: 'weigh', width: '80', title: __('Weigh'), operate: false },
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate',
                            width: '110',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [{
                                name: 'level',
                                text: function (table, row, index) {
                                    return '企业';
                                },
                                title: function (table, row, index) {
                                    return '[' + table['name'] + '] - 企业管理';
                                },
                                icon: 'fa fa-file-o',
                                classname: 'btn btn-info btn-xs btn-dialog',
                                url: function (table, row, index) {
                                    return 'myadmin/company/index?group_id=' + table['id'];
                                },
                                'extend': 'data-area=\'["80%","80%"]\''
                            },]
                        }]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        getIn: function (type) {
            var urls = '';
            switch (type) {
                case 'article':
                    urls = 'news/';
                    break;
                case 'product':
                    urls = 'mall/';
                    break;
                default:
                    urls = '';
                    break;
            }
            return urls;
        },
        diyFieldList: function (type) {
            $("#label").on("fa.event.appendfieldlist", ".btn-append", function (e, obj) {
                Form.events.plupload(obj);
                Form.events.faselect(obj);
            });
        },

        add: function () {
            this.diyFieldList('edit');
            Controller.api.bindevent();
        },
        edit: function () {
            this.diyFieldList('edit');
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