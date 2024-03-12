define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function() {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'myadmin/company/withdraw/index' + location.search,
                    add_url: 'myadmin/company/withdraw/add',
                    edit_url: 'myadmin/company/withdraw/edit',
                    del_url: 'myadmin/company/withdraw/del',
                    multi_url: 'myadmin/company/withdraw/multi',
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
                        { field: 'orderid', title: __('Orderid') },
                        { field: 'money', title: __('Money'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'handfee', title: __('HandFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'taxefee', title: __('TaxeFee'), operate: 'BETWEEN', formatter: Table.api.formatter.search },
                        { field: 'account', title: __('Account'), formatter: Table.api.formatter.search },
                        { field: 'name', title: __('Realname'), formatter: Table.api.formatter.search },
                        { field: 'memo', title: __('Memo') },
                        { field: 'type', visible: true, width: '90', title: __('Type'), searchList: Config.typeList, operate: 'LIKE', formatter: Table.api.formatter.label },
                        { field: 'status', title: __('Status'), searchList: { "created": __('Status created'), "successed": __('Status successed'), "rejected": __('Status rejected') }, formatter: Table.api.formatter.status },
                        { field: 'createtime', title: __('Createtime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: false },
                        { field: 'updatetime', title: __('Updatetime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime, visible: false },
                        { field: 'transfertime', title: __('Transfertime'), operate: 'RANGE', addclass: 'datetimerange', formatter: Table.api.formatter.datetime },
                        
                        { field: 'company_id', title: __('User_id'), formatter: Table.api.formatter.search, visible: false },
                        { field: 'company.name', title: __('Company'), operate: false },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttonss: [{
                                name: 'query',
                                text: '转账状态',
                                icon: 'fa fa-search',
                                classname: 'btn btn-info btn-xs btn-ajax',
                                url: 'myadmin/company/withdraw/query',
                            }]
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function() {
            jisuan();
            function jisuan() {
                var companyrate = { taxerate: $("#c-taxerate").text() || 0, handrate: $("#c-handrate").text() || 0, };
                var handrate = companyrate.handrate / 100;
                var taxerate = companyrate.taxerate / 100;
                var money = parseFloat($("#c-money").val()).toFixed(2);
                var handfee = parseFloat(money * handrate).toFixed(2);
                var taxefee = parseFloat(money * taxerate).toFixed(2);
                $("#c-handfee").val(handfee);
                $("#c-taxefee").val(taxefee);
                var settledmoney = (money - handfee - taxefee).toFixed(2);
                $("#c-settledmoney").text("￥" + settledmoney);
            }
            $("#c-company_id").data("eSelect", function (row) {
                $("#c-handrate").text(row.handrate)
                $("#c-taxerate").text(row.taxerate)
                $("#c-money").val(row.money)
                jisuan();
            });

            $("#c-handfee,#c-taxefee,#c-money").on("keyup change", function () {
                jisuan();
            });
            Controller.api.bindevent();
        },
        edit: function() {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function() {
                $("form[role=form]").data("validator-options", {
                    rules: {
                        settledmoney: function(element) {
                            var money = parseFloat($("#c-money").val()).toFixed(2);
                            var handfee = parseFloat($("#c-handfee").val()).toFixed(2);
                            var taxefee = parseFloat($("#c-taxefee").val()).toFixed(2);
                            var settledmoney = (money - handfee - taxefee).toFixed(2);
                            return settledmoney > 0 || '金额输入不正确';
                        }
                    }
                });
                $("#c-handfee,#c-taxefee,#c-money").on("keyup change", function() {
                    var money = parseFloat($("#c-money").val()).toFixed(2);
                    var handfee = parseFloat($("#c-handfee").val()).toFixed(2);
                    var taxefee = parseFloat($("#c-taxefee").val()).toFixed(2);
                    var settledmoney = (money - handfee - taxefee).toFixed(2);
                    $("#c-settledmoney").text("￥" + settledmoney);
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});