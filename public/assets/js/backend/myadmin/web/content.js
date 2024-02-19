define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    var Controller = {
        params: function () {
            var params = '';
            if (Config.company_id) {
                params += '/company_id/' + Config.company_id
            }
            if (Config.category_id) {
                params += '/category_id/' + Config.category_id
            }
            if (Config.mould_id) {
                params += '/mould_id/' + Config.mould_id
            }
            return params;
        },
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/web/content/index' + location.search,
                    add_url: 'myadmin/web/content/add' + Controller.params(),
                    edit_url: 'myadmin/web/content/edit',
                    del_url: 'myadmin/web/content/del',
                    multi_url: 'myadmin/web/content/multi',
                    import_url: 'myadmin/web/content/import',
                    table: 'myadmin_website_article',
                },
                queryParams: function (params) {
                    // 自定义搜索条件
                    var filter = params.filter ? JSON.parse(params.filter) : {};
                    var op = params.op ? JSON.parse(params.op) : {};
                    if (Config.company_id && Config.company_id != 'null') {
                        filter.company_id = Config.company_id;
                        op.company_id = "=";
                    }
                    if (Config.category_id && Config.category_id != 'null') {
                        filter.category_id = Config.category_id;
                        op.category_id = "=";
                    }
                    if (Config.mould_id && Config.mould_id != 'null') {
                        filter.mould_id = Config.mould_id;
                        op.mould_id = "=";
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
                        { field: 'id', width: '60', title: __('ID') },
                        { field: 'type', width: '60', title: __('Type'), searchList: Config.typeList, operate: 'LIKE', formatter: Table.api.formatter.label },
                        { field: 'name', width: '160', align: 'left', title: __('Name'), operate: 'LIKE' , formatter: Table.api.formatter.content},
                        { field: 'title', align: 'left', title: __('Title'), operate: 'LIKE', formatter: Table.api.formatter.content },
                        { field: 'cover', width: '60', title: __('Cover'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image },
                        { field: 'views', width: '80', title: __('Views') },
                        { field: 'category.name', width: '120', title: __('Category'), operate: false },
                        { field: 'createtime', width: '150', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', autocomplete: false, formatter: Table.api.formatter.datetime },
                        {
                            field: 'company_id', width: '160', title: __('Company'), visible: true,
                            searchList: function (column) { return Template('company', {}); },
                            formatter: Table.api.formatter.search,
                            formatter: function (value, row, index) {
                                if (row.company) {
                                    return '<a href="javascript:;" class="searchit" data-field="company_id" data-value="' + row.company_id + '">' + row.company.name + '</a>';
                                }
                                return '-';
                            }
                        }, 
                        { field: 'status', width: '100', title: __('Status'), searchList: Config.statusList, formatter: Table.api.formatter.status },
                        {
                            field: 'operate',
                            width: '110',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: []
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
                url: 'myadmin/web/content/recyclebin' + location.search,
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
                                url: 'myadmin/web/content/restore',
                                refresh: true
                            },
                            {
                                name: 'Destroy',
                                text: __('Destroy'),
                                classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                icon: 'fa fa-times',
                                url: 'myadmin/web/content/destroy',
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
        filetype(val) {
            var image = $(".box-image");
            var video = $(".box-video");
            var audio = $(".box-audio");
            switch (val) {
                case 'image':
                    image.css("display", "block");
                    video.css("display", "none");
                    audio.css("display", "none");
                    break;
                case 'video':
                    image.css("display", "none");
                    video.css("display", "block");
                    audio.css("display", "none");
                    break;
                case 'audio':
                    image.css("display", "none");
                    video.css("display", "none");
                    audio.css("display", "block");
                    break;
                default:
                    image.css("display", "none");
                    video.css("display", "none");
                    audio.css("display", "none");
                    break;
            }
            return;
        },
        myCommon: function () {
            var filetype = $("[name='row[type]']:checked").val();
            this.filetype(filetype);
            let that = this;
            $(document).on("change", "[name='row[type]']", function (row) {
                that.filetype(this.value);
            });
            $("#c-category_id").data("eSelect", function (obj) {
                let company_id = obj.company_id || '';
                if (company_id) {
                    $('#c-company_id').val(company_id);
                    $('#c-company_id').selectPageDisabled(true);
                } else {
                    $('#c-company_id').selectPageDisabled(false);
                }
                $('#c-company_id').selectPageRefresh();
            });
        },

        add: function () {
            this.myCommon();
            Controller.api.bindevent();
        },
        edit: function () {
            this.myCommon();
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