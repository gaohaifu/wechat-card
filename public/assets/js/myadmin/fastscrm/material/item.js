define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/material/item/index' + location.search,
                    add_url: 'fastscrm/material/item/add',
                    edit_url: 'fastscrm/material/item/edit',
                    del_url: 'fastscrm/material/item/del',
                    multi_url: 'fastscrm/material/item/multi',
                    import_url: 'fastscrm/material/item/import',
                    table: 'fastscrm_material_item',
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
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2'),"3":__('Type 3'),"4":__('Type 4'),"5":__('Type 5'),"6":__('Type 6'),"7":__('Type 7')}, formatter: Table.api.formatter.normal},
                        {field: 'group.id', title: __('Group_id'), operate: 'LIKE',searchList: $.getJSON("fastscrm/material/group/searchfind?field=row[id]"),formatter: Controller.api.formatter.grouptitle},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'remark', title: __('Remark'), operate: 'LIKE'},
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
            $(document).ready(function () {
                Controller.api.itemshow();
            })
            $('#c-type').change(function () {
                Controller.api.itemshow();
            })
        },
        edit: function () {
            Controller.api.bindevent();
            $(document).ready(function () {
                Controller.api.itemshow();
            })
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法
                grouptitle: function (value, row, index) {
                    return row.group.title;
                },

            },
            itemshow:function () {
                let vl = $("#c-type option:selected").val()
                $('.form-group').hide();
                $('#c-type').parents('.form-group').show();
                $('#c-group_id').parents('.form-group').show();

                $('#c-title').parents('.form-group').show();
                console.log(vl)
                if (vl==0){
                    $('#c-content').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==1){
                    $('#c-image').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==2){

                    $('#c-image').parents('.form-group').show();
                    $('#c-content').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==3){

                    $('#c-image').parents('.form-group').show();
                    // $('#c-content').parents('.form-group').show();
                    $('#c-link_url').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==4){
                    $('#c-voice').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==5){
                    $('#c-video').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==6){
                    $('#c-appid').parents('.form-group').show();
                    $('#c-path').parents('.form-group').show();
                    $('#c-title').parents('.form-group').show();
                    $('#c-image').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }
                if (vl==7){
                    $('#c-file').parents('.form-group').show();
                    $('#c-remark').parents('.form-group').show();
                }



            }
        }
    };
    return Controller;
});
