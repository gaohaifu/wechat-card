define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function($, undefined, Backend, Table, Form) {

    var Controller = {
        fun: {
            setPlace: function(map, myValue) {
                map.clearOverlays(); //清除地图上所有覆盖物
                function myFun() {
                    var pp = local.getResults().getPoi(0).point; //获取第一个智能搜索的结果
                    map.centerAndZoom(pp, 18);
                    map.addOverlay(new BMap.Marker(pp)); //添加标注
                }
                var local = new BMap.LocalSearch(map, { //智能搜索
                    onSearchComplete: myFun
                });
                local.search(myValue);
            }
        },
        index: function() {
            $('.nav-tabs li').click(function(e) {
                e.preventDefault()
                if ($(this).text() == '站点主题') {
                    $('#bottom_btn').hide();
                }
                else
                {
                    $('#bottom_btn').show();
                }
            })

            $('#js_configAk').click(function() {
                var that = $(this);
                var url = 'xccms/xccmssiteconfig/ak';

                $(this).data("area", ["40%", "45%"]);
                Fast.api.open(url, '配置地址坐标', $(this).data() || {});
            });

            $('#js_setMap').click(function() {
                var that = $(this);
                if (that.data('map_ak').length == 0) {
                    Layer.alert('请先配置百度AK');
                } else {
                    var url = 'xccms/xccmssiteconfig/map';
                    url += $('#c-center_point').val().length == 0 ? '' : '?center_point=' + $('#c-center_point').val();

                    Fast.api.open(url, "配置地址坐标", {
                        callback: function(data) {
                            window.location.reload();
                        }
                    });
                }
            })

            $('.js_selectTheme').click(function() {
                var that = $(this);
                var val = that.data('value');
                Backend.api.ajax({
                    url: 'xccms/xccmssiteconfig/edit_theme',
                    data: { 'row[theme]': val }
                }, function(d) {
                    $('.js_selectTheme').removeAttr('disabled');
                    $('.js_selectTheme span').text('启用');
                    that.prop('disabled', 'disabled');
                    that.find('span').html('已启用');

                    $('.js_setTheme').prop('disabled', 'disabled');
                    that.next('.js_setTheme').prop('disabled', '');
                });
            })

            $('.js_setTheme').click(function() {
                var that = $(this);
                var theme = that.data('value');
                var url = 'xccms/xccmssiteconfig/set_theme_ext?t_name='+theme;
                $(this).data("area", ["70%", "70%"]);
                Fast.api.open(url, '扩展配置', $(this).data() || {});
            });

            Controller.api.bindevent();
        },
        ak: function() {
            Form.api.bindevent($("form[role=form]"), function() {
                parent.window.location.reload();
            });
        },
        map: function() {
            Form.api.bindevent($("form[role=form]"));
            var p = $('#centre-point');
            var body = $('body');
            var point_x = body.width() / 2 - p.width() / 2;
            var point_y = body.height() / 2 - p.height();
            $('#centre-point').css({ top: point_y + 'px', left: point_x + 'px' });

            var default_points = center_point_data.length == 0 ? '116.404,39.915' : center_point_data;
            var default_pointsSplit = default_points.split(',');
            // console.log(default_points);

            require(['xccms-async!xccms-BMap'], function() {
                // 更多文档可参考 http://lbsyun.baidu.com/jsdemo.htm
                // 百度地图API功能
                var map = new BMap.Map("allmap");
                var point = new BMap.Point(default_pointsSplit[0], default_pointsSplit[1]);

                map.centerAndZoom(point, 18); //设置中心坐标点和级别
                var marker = new BMap.Marker(point); // 创建标注
                // map.addOverlay(marker);               // 将标注添加到地图中
                // marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

                map.enableDragging(); //开启拖拽
                //map.enableInertialDragging();   //开启惯性拖拽
                map.enableScrollWheelZoom(true); //是否允许缩放
                //map.centerAndZoom("上海",15); //根据城市名设定地图中心点

                var ac = new BMap.Autocomplete( //建立一个自动完成的对象
                    {
                        "input": "suggestId",
                        "location": map
                    });

                ac.addEventListener("onhighlight", function(e) { //鼠标放在下拉列表上的事件
                    var str = "";
                    var _value = e.fromitem.value;
                    var value = "";
                    if (e.fromitem.index > -1) {
                        value = _value.province + _value.city + _value.district + _value.street + _value.business;
                    }
                    str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

                    value = "";
                    if (e.toitem.index > -1) {
                        _value = e.toitem.value;
                        value = _value.province + _value.city + _value.district + _value.street + _value.business;
                    }
                    str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
                    $('#searchResultPanel').html(str);
                    // console.log(str);
                });

                var myValue;
                ac.addEventListener("onconfirm", function(e) { //鼠标点击下拉列表后的事件
                    var _value = e.item.value;
                    myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
                    $('#searchResultPanel').html("onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue);

                    Controller.fun.setPlace(map, myValue);

                    // console.log(myValue);
                });

                if (center_point_data.length == 0) {
                    var geolocation = new BMap.Geolocation();
                    geolocation.getCurrentPosition(function(r) {
                        if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                            // var mk = new BMap.Marker(r.point);
                            // map.addOverlay(mk);
                            map.panTo(r.point);
                            // Layer.alert('您的位置：' + r.point.lng + ',' + r.point.lat);
                        } else {
                            Layer.alert('failed' + this.getStatus());
                        }
                    }, { enableHighAccuracy: true });
                }

                $("form[role=form]").submit(function(e) {
                    e.preventDefault
                })

                $('#set-point').click(function() {
                    var center = map.getCenter();
                    var get_center_point = [center.lng.toFixed(5), center.lat.toFixed(5)].toString();
                    Backend.api.ajax({
                        url: 'xccms/xccmssiteconfig/map',
                        data: { center_point: get_center_point }
                    }, function(d) {
                        Fast.api.close(1); // 关闭窗体并回传数据
                    });
                })

            });
        },
        set_theme_ext: function() {
            Template.helper("Fast", Fast);

            //因为日期选择框不会触发change事件，导致无法刷新textarea，所以加上判断
            $(document).on("dp.change", "#second-form .datetimepicker", function() {
                $(this).parent().prev().find("input").trigger("change");
            });
            $(document).on("fa.event.appendfieldlist", "#first-table .btn-append", function(e, obj) {

            });

            $(document).on("fa.event.appendfieldlist", "#second-table .btn-append", function(e, obj) {
                //绑定动态下拉组件
                Form.events.selectpage(obj);
                //绑定日期组件
                Form.events.datetimepicker(obj);
                //绑定上传组件
                Form.events.faupload(obj);

                //上传成功回调事件，变更按钮的背景
                $(".upload-image", obj).data("upload-success", function(data) {
                    $(this).css("background-image", "url('" + Fast.api.cdnurl(data.url) + "')");
                })
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret) {
                Layer.alert(data.data);
            });

        },
        set_theme1_ext: function() {
            Template.helper("Fast", Fast);

            //因为日期选择框不会触发change事件，导致无法刷新textarea，所以加上判断
            $(document).on("dp.change", "#second-form .datetimepicker", function() {
                $(this).parent().prev().find("input").trigger("change");
            });
            $(document).on("fa.event.appendfieldlist", "#first-table .btn-append", function(e, obj) {

            });

            $(document).on("fa.event.appendfieldlist", "#second-table .btn-append", function(e, obj) {
                //绑定动态下拉组件
                Form.events.selectpage(obj);
                //绑定日期组件
                Form.events.datetimepicker(obj);
                //绑定上传组件
                Form.events.faupload(obj);

                //上传成功回调事件，变更按钮的背景
                $(".upload-image", obj).data("upload-success", function(data) {
                    $(this).css("background-image", "url('" + Fast.api.cdnurl(data.url) + "')");
                })
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret) {
                Layer.alert(data.data);
            });

        },
        set_theme2_ext: function() {
            Template.helper("Fast", Fast);

            //因为日期选择框不会触发change事件，导致无法刷新textarea，所以加上判断
            $(document).on("dp.change", "#second-form .datetimepicker", function() {
                $(this).parent().prev().find("input").trigger("change");
            });
            $(document).on("fa.event.appendfieldlist", "#first-table .btn-append", function(e, obj) {

            });

            $(document).on("fa.event.appendfieldlist", "#second-table .btn-append", function(e, obj) {
                //绑定动态下拉组件
                Form.events.selectpage(obj);
                //绑定日期组件
                Form.events.datetimepicker(obj);
                //绑定上传组件
                Form.events.faupload(obj);

                //上传成功回调事件，变更按钮的背景
                $(".upload-image", obj).data("upload-success", function(data) {
                    $(this).css("background-image", "url('" + Fast.api.cdnurl(data.url) + "')");
                })
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret) {
                Layer.alert(data.data);
            });

        },
        set_theme3_ext: function() {
            Template.helper("Fast", Fast);

            //因为日期选择框不会触发change事件，导致无法刷新textarea，所以加上判断
            $(document).on("dp.change", "#second-form .datetimepicker", function() {
                $(this).parent().prev().find("input").trigger("change");
            });
            $(document).on("fa.event.appendfieldlist", "#first-table .btn-append", function(e, obj) {

            });

            $(document).on("fa.event.appendfieldlist", "#second-table .btn-append", function(e, obj) {
                //绑定动态下拉组件
                Form.events.selectpage(obj);
                //绑定日期组件
                Form.events.datetimepicker(obj);
                //绑定上传组件
                Form.events.faupload(obj);

                //上传成功回调事件，变更按钮的背景
                $(".upload-image", obj).data("upload-success", function(data) {
                    $(this).css("background-image", "url('" + Fast.api.cdnurl(data.url) + "')");
                })
            });
            Form.api.bindevent($("form[role=form]"), function(data, ret) {
                Layer.alert(data.data);
            });

        },
        api: {
            bindevent: function() {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});