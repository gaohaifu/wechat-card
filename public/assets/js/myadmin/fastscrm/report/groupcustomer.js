define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Template, Echarts) {

    var Controller = {
        index: function (type=false) {
            //这句话在多选项卡统计表时必须存在，否则会导致影响的图表宽度不正确
            $(document).on("click", ".charts-custom a[data-toggle=\"tab\"]", function () {
                var that = this;
                setTimeout(function () {
                    var id = $(that).attr("href");
                    var chart = Echarts.getInstanceByDom($(id)[0]);
                    chart.resize();
                }, 0);
            });

            // 基于准备好的dom，初始化echarts实例
            var lineChart = Echarts.init(document.getElementById('line-chart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                legend: {
                    data: ['客户群总数', '有过消息的客户群数', '发过消息的群成员数', '客户群消息总数']
                },
                tooltip: {
                    trigger: 'axis'
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                xAxis: {
                    type: 'category',
                    data: Config.days
                },
                yAxis: {
                    type: 'value'
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                series: [
                    {
                        name: '客户群总数',
                        type: 'line',
                        stack: 'chat_total',
                        data: Config.chat_total
                    },
                    {
                        name: '有过消息的客户群数',
                        type: 'line',
                        stack: 'chat_has_msg',
                        data: Config.chat_has_msg
                    },
                    {
                        name: '发过消息的群成员数',
                        type: 'line',
                        stack: 'member_has_msg',
                        data: Config.member_has_msg
                    },
                    {
                        name: '客户群消息总数',
                        type: 'line',
                        stack: 'msg_total',
                        data: Config.msg_total
                    }
                ]
            };

            // 使用刚指定的配置项和数据显示图表。
            lineChart.setOption(option);
            // 基于准备好的dom，初始化echarts实例
            var areaChart = Echarts.init(document.getElementById('area-chart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {
                        type: 'cross',
                        label: {
                            backgroundColor: '#6a7985'
                        }
                    }
                },
                legend: {
                    data: ['客户群总数', '有过消息的客户群数', '发过消息的群成员数', '客户群消息总数']
                },
                toolbox: {
                    feature: {
                        saveAsImage: {}
                    }
                },
                grid: {
                    left: '3%',
                    right: '4%',
                    bottom: '3%',
                    containLabel: true
                },
                xAxis: [
                    {
                        type: 'category',
                        boundaryGap: false,
                        data: Config.days
                    }
                ],
                yAxis: [
                    {
                        type: 'value'
                    }
                ],
                series: [
                    {
                        name: '客户群总数',
                        type: 'line',
                        stack: 'chat_total',
                        areaStyle: {},
                        emphasis: {
                            focus: 'series'
                        },
                        data: Config.chat_total
                    },
                    {
                        name: '有过消息的客户群数',
                        type: 'line',
                        stack: 'chat_has_msg',
                        areaStyle: {},
                        emphasis: {
                            focus: 'series'
                        },
                        data: Config.chat_has_msg
                    },
                    {
                        name: '发过消息的群成员数',
                        type: 'line',
                        stack: 'member_has_msg',
                        areaStyle: {},
                        emphasis: {
                            focus: 'series'
                        },
                        data: Config.member_has_msg
                    },
                    {
                        name: '客户群消息总数',
                        type: 'line',
                        stack: 'msg_total',
                        areaStyle: {},
                        emphasis: {
                            focus: 'series'
                        },
                        data: Config.msg_total
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            areaChart.setOption(option);
            // 初始化表格参数配置
            Table.api.init();
            if(type){
                console.log(type)
                $('.mult-custom li.active a[data-toggle="tab"]').click();
            }
        },
        table: {
            first: function (type=false) {
                // 表格1
                var table1 = $("#table1");
                var day = $('.dayactive').data('day');
                var userid = $('#c-user').val();
                if(type){
                    table1.bootstrapTable('refresh', {url:'fastscrm/report/groupcustomer/table1?day='+ day+'&userid='+userid, pageNumber: 1});
                }
                table1.bootstrapTable({
                    url: 'fastscrm/report/groupcustomer/table1?day='+day+'&userid='+userid,
                    toolbar: '#toolbar1',
                    sortName: 'id',
                    search: false,
                    commonSearch: false,
                    columns: [
                        [
                            {field: 'day', title: __('Day')},
                            {field: 'chat_total', title: __('Chat_total')},
                            {field: 'new_chat_cnt', title: __('New_chat_cnt')},
                            {field: 'chat_has_msg', title: __('Chat_has_msg')},
                            {field: 'member_has_msg', title: __('Member_has_msg')},
                            {field: 'msg_total', title: __('Msg_total')},
                        ]
                    ]
                });

                // 为表格1绑定事件
                Table.api.bindevent(table1);
            },
            second: function (type=false) {
                // 表格2
                var table2 = $("#table2");
                var day = $('.dayactive').data('day');
                var userid = $('#c-user').val();

                if(type){
                    table2.bootstrapTable('refresh', {url:'fastscrm/report/groupcustomer/table2?day='+ day+'&userid='+userid, pageNumber: 1});
                }

                table2.bootstrapTable({
                    url: 'fastscrm/report/groupcustomer/table2?day='+day+'&userid='+userid,
                    extend: {
                        index_url: '',
                        add_url: '',
                        edit_url: '',
                        del_url: '',
                        multi_url: '',
                        table: '',
                    },
                    toolbar: '#toolbar2',
                    sortName: 'id',
                    search: false,
                    commonSearch: false,
                    columns: [
                        [
                            {field: 'name', title: __('Name')},
                            {field: 'chat_total', title: __('Chat_total')},
                            {field: 'new_chat_cnt', title: __('New_chat_cnt')},
                            {field: 'chat_has_msg', title: __('Chat_has_msg')},
                            {field: 'member_has_msg', title: __('Member_has_msg')},
                            {field: 'msg_total', title: __('Msg_total')},
                         ]
                    ]
                });

                // 为表格2绑定事件
                Table.api.bindevent(table2);
            }
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {//渲染的方法

            }
        }
    };

    //绑定事件
    $('.mult-custom a[data-toggle="tab"]').on('click', function (e) {
        if($($(this).attr("href")).attr('id')=='first'){
            Controller.table.first(true);
        }else{
            Controller.table.second(true);
        }
    });

    //必须默认触发shown.bs.tab事件
    setTimeout(function () {
        $('.mult-custom li.active a[data-toggle="tab"]').click();
    },1000);

    $(document).on("click", ".btn-day", function () {
        if (!$(this).hasClass('dayactive')) {
            $('.btn-day').removeClass('dayactive');
            $(this).addClass('dayactive');
            var day = $(this).data('day');
            var userid = $('#c-user').val();
            Fast.api.ajax({
                url: 'fastscrm/report/groupcustomer',
                data: {
                    day: day,
                    userid:userid
                }
            }, function (data, ret) {
                Config.days=data.days;
                Config.chat_total=data.chat_total;
                Config.chat_has_msg=data.chat_has_msg;
                Config.member_has_msg=data.member_has_msg;
                Config.msg_total=data.msg_total;
                Controller.index(true);
                return false;
            });
            // if ($('#first').hasClass('active')) {
            //     Controller.table.first(true,false);
            // }
            // if ($('#second').hasClass('active')) {
            //     Controller.table.second(true,false);
            // }
        }
    });
    $(document).on("change", "#c-user", function(){
        var day = $('.dayactive').data('day');
        var userid = $(this).val();
        Fast.api.ajax({
            url: 'fastscrm/report/groupcustomer',
            data: {
                day: day,
                userid:userid
            }
        }, function (data, ret) {
            Config.days=data.days;
            Config.chat_total=data.chat_total;
            Config.chat_has_msg=data.chat_has_msg;
            Config.member_has_msg=data.member_has_msg;
            Config.msg_total=data.msg_total;
            Controller.index(true);
            return false;
        });
        // if ($('#first').hasClass('active')) {
        //     Controller.table.first(true,false);
        // }
        // if ($('#second').hasClass('active')) {
        //     Controller.table.second(true,false);
        // }
    });
    return Controller;
});
