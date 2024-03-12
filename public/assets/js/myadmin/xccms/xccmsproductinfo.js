define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'jstree'], function ($, undefined, Backend, Table, Form) {
    var elTree = $('#treeview');// 目录元素

    var Controller = {
        fun: {
            BindData: function(t, cid){
                $('#table').bootstrapTable('refresh', {
                    url: 'xccms/xccmsproductinfo/getList?cid=' + cid
                });
            },
        },
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    // index_url: 'xccms/xccmsproductinfo/index' + location.search,
                    index_url: 'xccms/xccmsproductinfo/getList?cid=-1',
                    add_url: 'xccms/xccmsproductinfo/add',
                    edit_url: 'xccms/xccmsproductinfo/edit',
                    del_url: 'xccms/xccmsproductinfo/del',
                    table: 'xccms_product_info',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                searchFormVisible: true,
                search: false,
                columns: [
                    [
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'category_id', title: __('Category_id'), formatter: function(value, row) {
                            return row['category_name'];
                        }, operate: false},
                        {field: 'title', title: __('Title'), formatter: function(value, row) {
                            var html = value;
                            html += row['is_recommend'] == 1 ? ' <span class="label label-success">推荐</span>' : '';
                            return html;
                        }, operate: 'LIKE'},
                        {field: 'list_image', title: __('List_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'banners', title: __('Banners'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'price', title: __('Price'), operate: 'BETWEEN'},
                        {field: 'visits', title: __('Visits'), operate: 'BETWEEN'},
                        {field: 'is_recommend', title: __('Is_recommend'), searchList: {"0":__('未推荐'),"1":__('推荐')}, visible: false},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'state', title: __('State'), searchList: {"0":__('State 0'),"1":__('State 1'),"-1":__('State -1')}, formatter: Table.api.formatter.status},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatedby', title: __('Updatedby'), formatter: function(value, row, index) {
                            return row['updatedby_nickname']
                        }, operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            elTree.jstree("destroy");
            Controller.api.rendertree(nodeData);
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
            rendertree: function (content) {
                elTree
                    .on("changed.jstree", function (e, data) {
                        if (data['action'] == "select_node")
                        {
                            // console.log(data['node']);
                            var id = data['node']['id'];
                            var text = id == -1 ? '全部' : data['node']['text'];

                            $('.nav-tabs a').text(text);
                            Controller.fun.BindData($("#table"), id);

                            $('.bootstrap-table .search input').val('');
                        }
                    })
                    .on('open_node.jstree',function(e, data){
                        // console.log(data.node);
                    })
                    .jstree({
                        "themes": {"stripes": true},
                        "checkbox": {
                            "keep_selected_style": false,
                        },
                        "types": {
                            "folder": {
                                "icon": "jstree-folder",
                            },
                            "file": {
                                "icon": "jstree-file",
                            }
                        },
                        "plugins": ["types"],
                        "core": {
                            'check_callback': false,
                            "data": content
                        }
                    });
            }
        }
    };
    return Controller;
});