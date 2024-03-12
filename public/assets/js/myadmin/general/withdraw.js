define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/withdraw/index' + location.search,
                    add_url: 'general/withdraw/add',
                    edit_url: 'general/withdraw/edit',
                    del_url: 'general/withdraw/del',
                    table: 'company_myadmin_withdraw',
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
                        { checkbox: true },
                        { field: 'id', title: __('Id') },
                        { field: 'orderid',width: '170', title: __('Orderid') },
                        { field: 'money', width: '90',title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'handfee', width: '90', title: __('HandFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'taxefee', width: '90', title: __('TaxeFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'account', title: __('Account'), formatter: Table.api.formatter.search },
                        { field: 'name', title: __('Realname'), formatter: Table.api.formatter.search },
                        { field: 'memo', title: __('Memo') },
                        { field: 'type', visible: true, width: '90', title: __('Type'), searchList: Config.typeList, operate: 'LIKE', formatter: Table.api.formatter.label },
                        { field: 'status', width: '90',title: __('Status'), searchList: { "created": __('Status created'), "successed": __('Status successed'), "rejected": __('Status rejected') }, formatter: Table.api.formatter.status },
                        { field: 'createtime', width: '150', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: true },
                        { field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: false },
                        //{ field: 'transfertime', title: __('Transfertime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        {
                            field: 'operate', width: '80',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: []
                        }
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
                $("form[role=form]").data("validator-options", {
                    rules: {
                        settledmoney: function (element) {
                            var money = parseFloat($("#c-money").val()).toFixed(2);
                            var handfee = parseFloat($("#c-handfee").val()).toFixed(2);
                            var taxefee = parseFloat($("#c-taxefee").val()).toFixed(2);
                            var settledmoney = (money - handfee - taxefee).toFixed(2);
                            return settledmoney > 0 || '金额输入不正确';
                        }
                    }
                });
                $("#c-handfee,#c-taxefee,#c-money").on("keyup change", function () {
                    var company = Config.company;
                    var handrate = company.handrate /100;
                    var taxerate = company.taxerate /100;                               
                    var money = parseFloat($("#c-money").val()).toFixed(2);
                    var handfee = parseFloat(money * handrate).toFixed(2);
                    var taxefee = parseFloat(money * taxerate).toFixed(2);
                    $("#c-handfee").val(handfee);
                    $("#c-taxefee").val(taxefee);
                    var settledmoney = (money - handfee - taxefee).toFixed(2);
                    $("#c-settledmoney").text("￥" + settledmoney);

                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});