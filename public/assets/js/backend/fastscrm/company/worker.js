/*jshint esversion: 6 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/company/worker/index' + location.search,
                    add_url: 'fastscrm/company/worker/add',
                    edit_url: 'fastscrm/company/worker/edit',
                    del_url: 'fastscrm/company/worker/del',
                    multi_url: 'fastscrm/company/worker/multi',
                    import_url: 'fastscrm/company/worker/import',
                    table: 'fastscrm_worker',
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
                        {field: 'userid',visible: false, title: __('Userid'), operate: false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'department', title: __('Department'), operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/depart/searchfind?field=row[id]"),formatter:Controller.api.formatter.departname},
                        {field: 'store_id',visible: false, title: '绑定门店', operate: 'FIND_IN_SET',searchList: $.getJSON("fastscrm/company/store/searchfind?field=row[id]")},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'position', title: __('Position'), operate: 'LIKE'},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'email', title: __('Email'), operate: 'LIKE'},
                        {field: 'biz_mail', title: __('Biz_mail'), operate: 'LIKE'},
                        {field: 'avatar', title: __('avatar'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'qr_code', title: __('qr_code'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    title: __('邀请'),
                                    text: '邀请',
                                    classname: 'btn btn-xs btn-primary btn-magic btn-ajax',
                                    icon: 'fa fa-leaf',
                                    confirm: '是否确认？',
                                    url: 'fastscrm/company/worker/inviter',
                                    success: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        table.bootstrapTable('refresh', {});
                                        //如果需要阻止成功提示，则必须使用return false;
                                        //return false;
                                    },
                                    error: function (data, ret) {
                                        console.log(data, ret);
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                                {
                                    name: 'click',
                                    title: __('更新下载二维码'),
                                    text: '更新下载二维码',
                                    classname: 'btn btn-xs btn-info btn-click',
                                    icon: 'fa fa-leaf',
                                    // dropdown: '更多',//如果包含dropdown，将会以下拉列表的形式展示
                                    click: function (data,i) {
                                        var url =   Fast.api.fixurl('fastscrm/company/worker/download?id='+i.id);
                                        window.location.href=url;
                                    }
                                },

                                {
                                    name: 'detail',
                                    title: __('绑定门店'),
                                    text: '绑定门店',
                                    classname: 'btn btn-xs btn-warning btn-magic btn-dialog ',
                                    icon: 'fa fa-list',
                                    url: 'fastscrm/company/worker/query',
                                    callback: function (data) {
                                        $.ajax({
                                            url: 'fastscrm/company/worker/bindstore',
                                            type: 'POST',
                                            data: {

                                            },
                                            traditional: true,
                                        }).done(function (result) {
                                            layer.msg(result.msg);
                                        })
                                            .fail(function () {
                                                console.log("error");
                                            })
                                            .always(function () {
                                                console.log("complete");
                                            });
                                        table.bootstrapTable('refresh', {});
                                    }
                                },
                            ],},

                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            //展开隐藏全部
            $(document).on("click", ".btn-sync", function () {
                var ids = Table.api.selectedids(table);
                Table.api.multi("changestatus", ids.join(","), table, this);
            });
            $(document).on('click', '.btn-import', function () {
                var url =   Fast.api.fixurl('shopcoupon/game/import');
                Layer.confirm('是否确认下载模板', {
                    icon: 3,
                    title: '提示'
                }, function (index) {
                    window.location.href=url;
                    Layer.close(index);
                });
            });



        },
        add: function () {
            console.log(123)
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        query: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {

                Form.api.bindevent($("form[role=form]"));

            },
            formatter: {//渲染的方法
                departname: function (value, row, index) {
                    // console.log(row.departname)
                    return row.departname;
                },
                storename: function (value, row, index) {
                    // console.log(row.storename)
                    return row.storename;
                }
            }
        }
    };
    return Controller;
});
