define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/report/losedata/index' + location.search,
                    add_url: 'fastscrm/report/losedata/add',
                    edit_url: 'fastscrm/report/losedata/edit',
                    del_url: 'fastscrm/report/losedata/del',
                    multi_url: 'fastscrm/report/losedata/multi',
                    import_url: 'fastscrm/report/losedata/import',
                    table: 'fastscrm_customer_lose',
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
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'fl_userid', title: __('Fl_user_name'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/worker/searchfind?field=row[id]"),formatter:Controller.api.formatter.fl_user_name},
                        {field: 'fl_remark', title: __('Fl_remark'), operate: 'LIKE'},
                        {field: 'fl_description', title: __('Fl_description'), operate: 'LIKE'},
                        {field: 'fl_remark_mobiles', title: __('Fl_remark_mobiles'), operate: 'LIKE'},
                        {field: 'fl_add_way', title: __('Fl_add_way'), searchList: {"0":__('Fl_add_way 0'),"1":__('Fl_add_way 1'),"2":__('Fl_add_way 2'),"3":__('Fl_add_way 3'),"4":__('Fl_add_way 4'),"5":__('Fl_add_way 5'),"6":__('Fl_add_way 6'),"7":__('Fl_add_way 7'),"8":__('Fl_add_way 8'),"9":__('Fl_add_way 9'),"10":__('Fl_add_way 10'),"201":__('Fl_add_way 201'),"202":__('Fl_add_way 202')}, formatter: Table.api.formatter.normal},
                        {field: 'del_type', title: __('Del_type'), searchList: {"0":__('Del_type 0'),"1":__('Del_type 1')}, formatter: Table.api.formatter.normal},
                        {field: 'fl_createtime', title: __('Fl_createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
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
            formatter: {//渲染的方法
                fl_user_name: function (value, row, index) {
                    return row.fl_user_name;
                },
            }
        }
    };
    return Controller;
});
