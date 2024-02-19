define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'smartcard/visitors/index' + location.search,
                    add_url: 'smartcard/visitors/add',
                    edit_url: 'smartcard/visitors/edit',
                    del_url: 'smartcard/visitors/del',
                    multi_url: 'smartcard/visitors/multi',
                    import_url: 'smartcard/visitors/import',
                    table: 'smartcard_visitors',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'staff_id', title: __('Staff_id')},
                        {field: 'typedata', title: __('Typedata'), searchList: {"1":__('Typedata 1'),"2":__('Typedata 2'),"3":__('Typedata 3'),"4":__('Typedata 4'),"5":__('Typedata 5'),"6":__('Typedata 6'),"7":__('Typedata 7'),"8":__('Typedata 8'),"9":__('Typedata 9')}, formatter: Table.api.formatter.normal},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'smartcardcompany.name', title: __('Smartcardcompany.name'), operate: 'LIKE'},
                        // {field: 'smartcardcompany.url', title: __('Smartcardcompany.url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        // {field: 'smartcardcompany.intro', title: __('Smartcardcompany.intro'), operate: 'LIKE'},
                        {field: 'smartcardcompany.shortname', title: __('Smartcardcompany.shortname'), operate: 'LIKE'},
                        // {field: 'smartcardcompany.begintime', title: __('Smartcardcompany.begintime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'smartcardcompany.endtime', title: __('Smartcardcompany.endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'smartcardcompany.licenseimage', title: __('Smartcardcompany.licenseimage'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'smartcardcompany.licensenumber', title: __('Smartcardcompany.licensenumber'), operate: 'LIKE'},
                        // {field: 'smartcardcompany.picimages', title: __('Smartcardcompany.picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'smartcardcompany.videofiles', title: __('Smartcardcompany.videofiles'), operate: false},
                        // {field: 'smartcardcompany.address', title: __('Smartcardcompany.address'), operate: 'LIKE'},
                        // {field: 'smartcardcompany.phone', title: __('Smartcardcompany.phone')},
                        // {field: 'smartcardcompany.createtime', title: __('Smartcardcompany.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'smartcardcompany.weigh', title: __('Smartcardcompany.weigh')},
                        // {field: 'smartcardstaff.id', title: __('Smartcardstaff.id')},
                        // {field: 'smartcardstaff.name', title: __('Smartcardstaff.name'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.user_id', title: __('Smartcardstaff.user_id')},
                        // {field: 'smartcardstaff.company_id', title: __('Smartcardstaff.company_id')},
                        // {field: 'smartcardstaff.position', title: __('Smartcardstaff.position'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.tags_ids', title: __('Smartcardstaff.tags_ids'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.mobile', title: __('Smartcardstaff.mobile')},
                        // {field: 'smartcardstaff.wechat', title: __('Smartcardstaff.wechat'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.email', title: __('Smartcardstaff.email'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.visit', title: __('Smartcardstaff.visit')},
                        // {field: 'smartcardstaff.favor', title: __('Smartcardstaff.favor')},
                        // {field: 'smartcardstaff.address', title: __('Smartcardstaff.address'), operate: 'LIKE'},
                        // {field: 'smartcardstaff.picimages', title: __('Smartcardstaff.picimages'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'smartcardstaff.videofiles', title: __('Smartcardstaff.videofiles'), operate: false},
                        // {field: 'smartcardstaff.updatetime', title: __('Smartcardstaff.updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'smartcardstaff.createtime', title: __('Smartcardstaff.createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'smartcardstaff.weigh', title: __('Smartcardstaff.weigh')},
                        {field: 'visittime', title: __('Visittime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'weigh', title: __('Weigh'), operate: false},
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
        },
        edit: function () {
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