define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/convert/replyitem/index' + location.search,
                    add_url: 'fastscrm/convert/replyitem/add',
                    edit_url: 'fastscrm/convert/replyitem/edit',
                    del_url: 'fastscrm/convert/replyitem/del',
                    multi_url: 'fastscrm/convert/replyitem/multi',
                    import_url: 'fastscrm/convert/replyitem/import',
                    table: 'fastscrm_reply_item',
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
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'group_id', title: __('Group_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/convert/replygroup/searchfind?field=row[id]"),formatter: Controller.api.formatter.grouptitle},
                        {field: 'typedata', title: __('Typedata'), searchList: {"1":__('Typedata 1'),"2":__('Typedata 2')}, formatter: Table.api.formatter.normal},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'admin_id', title: __('Admin_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/convert/replyitem/searchfind?field=row[id]"),formatter: Controller.api.formatter.admin_name},
                        {field: 'share_num', title: __('Share_num'), operate: 'false',events: Controller.api.events.logs,formatter: Controller.api.formatter.share_num},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
                url: 'fastscrm/convert/replyitem/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'fastscrm/convert/replyitem/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'fastscrm/convert/replyitem/destroy',
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
            $("input:radio[name='row[typedata]']").change(function (){
                switch ($(this).val()) {
                    case '1':
                        $('.content_box').show();
                        $('.material_box').hide();
                        break;
                    case '2':
                        $('.content_box').hide();
                        $('.material_box').show();
                        break;
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            var checked = $("input:radio[name='row[typedata]']:checked").val();
            switch (checked) {
                case '1':
                    $('.content_box').show();
                    $('.material_box').hide();
                    break;
                case '2':
                    $('.content_box').hide();
                    $('.material_box').show();
                    break;
            }
            $("input:radio[name='row[typedata]']").change(function (){
                switch ($(this).val()) {
                    case '1':
                        $('.content_box').show();
                        $('.material_box').hide();
                        break;
                    case '2':
                        $('.content_box').hide();
                        $('.material_box').show();
                        break;
                }
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                grouptitle: function (value, row, index) {
                    return row.replygroup.title;
                },
                admin_name: function (value, row, index) {
                    return row.admin_name;
                },
                share_num: function (value, row, index) {
                    return '<a class="btn btn-xs btn-browser">' + row.share_num+ '</a>';
                },
            },
            events: {//绑定事件的方法
                logs: {
                    'click .btn-browser': function (e, value, row, index) {
                        e.stopPropagation();
                        var url = 'fastscrm/convert/replylog?reply_id='+row.id;
                        Backend.api.open(url, '分享记录',  {
                            area: ["50%", "60%"],
                        });

                    }
                },
            }
        }
    };
    return Controller;
});
