define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/crm/customer/index' + location.search,
                    add_url: 'fastscrm/crm/customer/add',
                    edit_url: 'fastscrm/crm/customer/edit',
                    del_url: 'fastscrm/crm/customer/del',
                    multi_url: 'fastscrm/crm/customer/multi',
                    import_url: 'fastscrm/crm/customer/import',
                    table: 'fastscrm_customer',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'avatar', title: __('Avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'fl_add_way', title: __('Fl_add_way'), searchList: {"0":__('Fl_add_way 0'),"1":__('Fl_add_way 1'),"2":__('Fl_add_way 2'),"3":__('Fl_add_way 3'),"4":__('Fl_add_way 4'),"5":__('Fl_add_way 5'),"6":__('Fl_add_way 6'),"7":__('Fl_add_way 7'),"8":__('Fl_add_way 8'),"9":__('Fl_add_way 9'),"10":__('Fl_add_way 10'),"11":__('Fl_add_way 11'),"12":__('Fl_add_way 12'),"13":__('Fl_add_way 13'),"14":__('Fl_add_way 14'),"15":__('Fl_add_way 15'),"201":__('Fl_add_way 201'),"202":__('Fl_add_way 202')}, formatter: Table.api.formatter.normal},
                        {field: 'state', title: __('State'), operate: 'LIKE'},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'fl_tags', title: __('Fl_tags'), operate: false,formatter:Controller.api.formatter.fl_tags},
                        {field: 'fl_userid', title: __('Fl_user_name'), operate: '=',searchList: $.getJSON("fastscrm/company/worker/searchfind"),formatter:Controller.api.formatter.fl_user_name},
                        {field: 'fl_remark', title: __('Fl_remark'), operate: 'LIKE'},
                        {field: 'fl_description', title: __('Fl_description'), operate: 'LIKE'},
                        {field: 'fl_remark_mobiles', title: __('Fl_remark_mobiles'), operate: 'LIKE'},
                        {field: 'fl_createtime', title: __('Fl_createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            // 打标签
            $(document).on("click", ".btn-addtags", function () {
                var ids = Table.api.selectedids(table);
                var url = 'fastscrm/crm/customer/addtags?ids='+ids.join(",");
                Fast.api.open(url,'',ids);
            });
            // 删标签
            $(document).on("click", ".btn-deltags", function () {
                var ids = Table.api.selectedids(table);
                var url = 'fastscrm/crm/customer/deltags?ids='+ids.join(",");
                Fast.api.open(url,'',ids);
            });
            // 同步
            $(document).on("click", ".btn-sync", function () {
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        addtags: function () {
            $(document).on("click", ".sentence_li", function () {
                if(!$(this).hasClass('nocursor')){
                    if($(this).hasClass('active')){
                        $(this).removeClass('active');
                    }else {
                        $(this).addClass('active');
                    }
                }
            });
            $(document).on("click", ".btn-primary", function () {
                var tags = '';
                $('.active').each(function () {
                    tags+=$(this).data('tagid')+',';
                });
                tags=tags.substring(0,tags.length-1);
                Fast.api.ajax({
                    url: 'fastscrm/crm/customer/addtags',
                    data: {
                        tags: tags,
                        customers: $('#customers').val()
                    }
                }, function (data, ret) {
                    setTimeout(function () {
                        parent.$(".btn-refresh").trigger("click");
                        var index = parent.Layer.getFrameIndex(window.name);
                        parent.Layer.close(index);
                    },800)
                });
            });
        },
        deltags: function () {
            $(document).on("click", ".sentence_li", function () {
                if(!$(this).hasClass('nocursor')){
                    if($(this).hasClass('active')){
                        $(this).removeClass('active');
                        $(this).addClass('delactive');
                    }else {
                        $(this).addClass('active');
                        $(this).removeClass('delactive');
                    }
                }
            });
            $(document).on("click", ".btn-primary", function () {
                var tags = '';
                $('.delactive').each(function () {
                    tags+=$(this).data('tagid')+',';
                });
                tags=tags.substring(0,tags.length-1);
                Fast.api.ajax({
                    url: 'fastscrm/crm/customer/deltags',
                    data: {
                        tags: tags,
                        customers: $('#customers').val()
                    }
                }, function (data, ret) {
                    setTimeout(function () {
                        parent.$(".btn-refresh").trigger("click");
                        var index = parent.Layer.getFrameIndex(window.name);
                        parent.Layer.close(index);
                    },800)
                });
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                fl_user_name: function (value, row, index) {
                    return row.fl_user_name;
                },
                fl_tags: function (value, row, index) {
                    var html = '';
                    row.tags.forEach(function (item) {

                        html+='<span class="sentence_li">'+item.name+'</span>';
                    });
                    return html;
                }
            },
            //获取选中的数据
            selecteddata: function (table, current) {
                var options = table.bootstrapTable('getOptions');
                //如果有设置翻页记忆模式
                if (!current && options.maintainSelected) {
                    return options.selectedData;
                }
                return table.bootstrapTable('getSelections');
            },
        }
    };
    return Controller;
});
