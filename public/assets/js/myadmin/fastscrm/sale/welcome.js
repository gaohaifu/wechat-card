/*jshint esversion: 6 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/sale/welcome/index' + location.search,
                    add_url: 'fastscrm/sale/welcome/add',
                    edit_url: 'fastscrm/sale/welcome/edit',
                    del_url: 'fastscrm/sale/welcome/del',
                    multi_url: 'fastscrm/sale/welcome/multi',
                    import_url: 'fastscrm/sale/welcome/import',
                    table: 'fastscrm_sale_welcome',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'item_id', title: __('Item_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/material/item/searchfind?field=row[id]"),formatter: Controller.api.formatter.itemname},
                        {field: 'user_id', title: __('User_id'), operate: false,formatter:Controller.api.formatter.workername},
                        {field: 'store_id', title:  __('Store_id'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/store/searchfind?field=row[id]"),formatter: Controller.api.formatter.storename},
                        {field: 'creater', title: __('Creater'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            Controller.api.strinsert();
        },
        edit: function () {
            Controller.api.bindevent();
            Controller.api.strinsert();
        },
        api: {
            formatter: {//渲染的方法
                itemname: function (value, row, index) {
                    return row.itemname;
                },
                workername: function (value, row, index) {
                    return row.workername;
                },
                storename: function (value, row, index) {
                    return row.storename;
                },
            },

            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            strinsert: function (){


                $(document).on("change", "#c-store_id", function(){
                    let storeid  =$('#c-store_id').val();

                    console.log(storeid)
                    if (storeid){
                        $('#c-user_id').parents('.form-group').hide();
                    }else{
                        $('#c-user_id').parents('.form-group').show();
                    }
                    $('#c-user_id').val('').siblings('ul').children().not(':last').remove();
                });


                $('.strinsert').click(function () {
                    let html = $('#c-content').val();
                    let fild = $(this).data('fild');
                    html+='{{'+fild+'}}';
                    console.log(html)
                    $('#c-content').val(html);
                });
                
            }
        }
    };
    return Controller;
});
