define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/sale/groupmessage/index' + location.search,
                    add_url: 'fastscrm/sale/groupmessage/add',
                    edit_url: 'fastscrm/sale/groupmessage/edit',
                    del_url: 'fastscrm/sale/groupmessage/del',
                    multi_url: 'fastscrm/sale/groupmessage/multi',
                    import_url: 'fastscrm/sale/groupmessage/import',
                    table: 'fastscrm_group_chat_sale',
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
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'item_id', title: __('Item_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/material/item/searchfind?field=row[id]"),formatter: Controller.api.formatter.item_title},
                        {field: 'typedata', title: __('Typedata'), searchList: {"1":__('Typedata 1'),"2":__('Typedata 2'),"3":__('Typedata 3')}, formatter: Table.api.formatter.normal},
                        {field: 'worker_id', title: __('Worker_id'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/worker/searchfind?field=row[id]"),formatter: Controller.api.formatter.worker_total},
                        {field: 'depart_id', title: __('Depart_id'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/depart/searchfind?field=row[id]"),formatter: Controller.api.formatter.depart_total},
                        {field: 'store_id', title: __('Store_id'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/store/searchfind?field=row[id]"),formatter: Controller.api.formatter.store_total},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('执行任务'),
                                    text: '执行任务',
                                    classname: 'btn btn-xs btn-primary btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    confirm: '是否确认执行？',
                                    url: 'fastscrm/sale/groupmessage/action',
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh', {});
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'chatreport',
                                    title: __('效果分析'),
                                    text: '效果分析',
                                    classname: 'btn btn-xs btn-success btn-chatreport',
                                    icon: 'fa fa-line-chart',
                                },

                            ],
                            formatter: Table.api.formatter.operate,
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            $(document).on("click", ".btn-chatreport", function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/sale/chatreport?sale_id='+row.id;
                Backend.api.addtabs(url, '效果分析', '');
            });
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
                url: 'fastscrm/sale/groupmessage/recyclebin' + location.search,
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
                                    url: 'fastscrm/sale/groupmessage/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'fastscrm/sale/groupmessage/destroy',
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
                        $('.group-box').show();
                        $('.depart-box').hide();
                        $('.store-box').hide();
                        break;
                    case '2':
                        $('.group-box').hide();
                        $('.depart-box').show();
                        $('.store-box').hide();
                        break;
                    case '3':
                        $('.group-box').hide();
                        $('.depart-box').hide();
                        $('.store-box').show();
                        break;
                }
            });
            Controller.api.bindevent();
        },
        edit: function () {
            var checked = $("input:radio[name='row[typedata]']:checked").val();
            switch (checked) {
                case '1':
                    $('.group-box').show();
                    $('.depart-box').hide();
                    $('.store-box').hide();
                    break;
                case '2':
                    $('.group-box').hide();
                    $('.depart-box').show();
                    $('.store-box').hide();
                    break;
                case '3':
                    $('.group-box').hide();
                    $('.depart-box').hide();
                    $('.store-box').show();
                    break;
            }
            $("input:radio[name='row[typedata]']").change(function (){
                switch ($(this).val()) {
                    case '1':
                        $('.group-box').show();
                        $('.depart-box').hide();
                        $('.store-box').hide();
                        break;
                    case '2':
                        $('.group-box').hide();
                        $('.depart-box').show();
                        $('.store-box').hide();
                        break;
                    case '3':
                        $('.group-box').hide();
                        $('.depart-box').hide();
                        $('.store-box').show();
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
                item_title: function (value, row, index) {
                    return row.item_title;
                },
                worker_total: function (value, row, index) {
                    if(row.worker_total>0){
                        return row.worker_total+'位群主';
                    }else{
                        return '-';
                    }
                },
                depart_total: function (value, row, index) {
                    if(row.depart_total>0){
                        return row.depart_total+'个部门';
                    }else{
                        return '-';
                    }
                },
                store_total: function (value, row, index) {
                    if(row.store_total>0){
                        return row.store_total+'个门店';
                    }else{
                        return '-';
                    }
                },
            }
        }
    };
    return Controller;
});
