define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        fun: {
            bind_category_box: function(value, object_value) {
                $('#js_Category_box').addClass('hidden');
                $('#js_Url_box').addClass('hidden');
                // $('#js_Sub_box').addClass('hidden');
                switch(value)
                {
                    case 'product':
                    case 'content': 
                    case 'page':
                        $('#js_Category_box').removeClass('hidden');
                        switch(value)
                        {
                            case 'product':
                                $('#c-menu_object_id').empty();
                                $.each(product_category_list, function(i){
                                    var item = product_category_list[i];
                                    var is_selected = item['id'] == object_value ? 'selected' : '';
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'" '+is_selected+'>'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box').removeClass('hidden');
                                break;
                            case 'content':
                                $('#c-menu_object_id').empty();
                                $.each(content_category_list, function(i){
                                    var item = content_category_list[i];
                                    var is_selected = item['id'] == object_value ? 'selected' : '';
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'" '+is_selected+'>'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box').removeClass('hidden');
                                break;
                            case 'page':
                                $('#c-menu_object_id').empty();
                                $.each(page_list, function(i){
                                    var item = page_list[i];
                                    var is_selected = item['id'] == object_value ? 'selected' : '';
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'" '+is_selected+'>'+item['name']+'</option>');
                                });
                                break;
                        }
                        $('#c-menu_object_id').selectpicker('refresh');
                        break;
                    case 'link':
                        $('#js_Url_box').removeClass('hidden');
                        break;
                }
            },
            show_sub_option: function() {
                
            }
        },
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'xccms/xccmsmenuinfo/index' + location.search,
                    add_url: 'xccms/xccmsmenuinfo/add',
                    edit_url: 'xccms/xccmsmenuinfo/edit',
                    del_url: 'xccms/xccmsmenuinfo/del',
                    multi_url: 'xccms/xccmsmenuinfo/multi',
                    import_url: 'xccms/xccmsmenuinfo/import',
                    table: 'xccms_menu_info',
                }
            });

            var table = $("#table");

            //在普通搜索渲染后
            table.on('post-common-search.bs.table', function (event, table) {
                var form = $("form", table.$commonsearch);

                Form.events.cxselect(form);
                Form.events.selectpage(form);
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                searchFormVisible: true,
                commonSearch: false,
                search: false,
                pagination: false,
                columns: [
                    [
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'name', title: __('Name'), align: 'left', formatter:function (value, row, index) {
                            var html = value.toString().replace(/(&|&amp;)nbsp;/g, '&nbsp;');
                            html += row['is_top_show'] == 1 ? ' <span class="label label-info">顶</span>' : '';
                            html += row['is_bottom_show'] == 1 ? ' <span class="label label-default">底</span>' : '';
                            return html;
                        }, operate: false},
                        {field: 'en_name', title: __('En_name'), operate: 'LIKE'},
                        {field: 'icon', title: __('图标'), formatter: Table.api.formatter.image},
                        {field: 'menu_type', title: __('Menu_type'), searchList: {"product":__('产品分类'),"content":__('内容分类'),"page":__('单页'),"news":__('新闻'),"index":__('首页'),"aboutus":__('关于我们'),"contactus":__('联系我们'),"partner":__('合作伙伴'),"job":__('在线招聘'),"faq":__('FAQ'),"link":__('链接')}, formatter: Table.api.formatter.flag},
                        {field: 'menu_object_id', title: __('Menu_object_id'), formatter: function(value, row) {
                            return row['menu_object_name'];
                        }, operate: false},
                        {field: 'url', title: __('Url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'is_top_show', title: __('Is_top_show'), searchList: {"1":__('显示'),"0":__('不显示')}, visible: false},
                        {field: 'is_bottom_show', title: __('Is_bottom_show'), searchList: {"1":__('显示'),"0":__('不显示')}, visible: false},
                        {field: 'state', title: __('State'), searchList: {"0":__('State 0'),"1":__('State 1'),"-1":__('State -1')}, formatter: Table.api.formatter.status},
                        {field: 'weigh', title: __('Weigh'), operate: false},
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
        },
        add: function () {
            $('input[name="row[menu_type]"]').click(function(){
                $('#js_Category_box').addClass('hidden');
                $('#js_Url_box').addClass('hidden');
                // $('#js_Sub_box input[type="radio"]').prop('disabled', true);
                var value = $('input[name="row[menu_type]"]:checked').val();
                switch(value)
                {
                    case 'product':
                    case 'content':
                    case 'page':
                        $('#js_Category_box').removeClass('hidden');

                        switch(value)
                        {
                            case 'product':
                                $('#c-menu_object_id').empty();
                                $.each(product_category_list, function(i){
                                    var item = product_category_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box input[type="radio"]').prop('disabled', false);
                                break;
                            case 'content':
                                $('#c-menu_object_id').empty();
                                $.each(content_category_list, function(i){
                                    var item = content_category_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box input[type="radio"]').prop('disabled', false);
                                break;
                            case 'page':
                                $('#c-menu_object_id').empty();
                                $.each(page_list, function(i){
                                    var item = page_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                break;
                        }
                        $('#c-menu_object_id').selectpicker('refresh');
                        break;

                    case 'link':
                        $('#js_Url_box').removeClass('hidden');
                        break;
                }
            })

            Controller.api.bindevent();
        },
        edit: function () {
            $('input[name="row[menu_type]"]').click(function(){
                $('#js_Category_box').addClass('hidden');
                $('#js_Url_box').addClass('hidden');
                // $('#js_Sub_box').addClass('hidden');
                var value = $('input[name="row[menu_type]"]:checked').val();
                switch(value)
                {
                    case 'product':
                    case 'content':
                    case 'page':
                        $('#js_Category_box').removeClass('hidden');

                        switch(value)
                        {
                            case 'product':
                                $('#c-menu_object_id').empty();
                                $.each(product_category_list, function(i){
                                    var item = product_category_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box').removeClass('hidden');
                                break;
                            case 'content':
                                $('#c-menu_object_id').empty();
                                $.each(content_category_list, function(i){
                                    var item = content_category_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                // $('#js_Sub_box').removeClass('hidden');
                                break;
                            case 'page':
                                $('#c-menu_object_id').empty();
                                $.each(page_list, function(i){
                                    var item = page_list[i];
                                    $('#c-menu_object_id').append('<option value="'+item['id']+'">'+item['name']+'</option>');
                                });
                                break;
                        }
                        $('#c-menu_object_id').selectpicker('refresh');
                        break;

                    case 'link':
                        $('#js_Url_box').removeClass('hidden');
                        break;
                }
            })

            Controller.api.bindevent();

            setTimeout(function(){
                Controller.fun.bind_category_box($('input[name="row[menu_type]"]:checked').val(), $('#c-menu_object_id').data('value'));
            }, 300)
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});