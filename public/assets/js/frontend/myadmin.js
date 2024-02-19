define(['jquery', 'bootstrap', 'frontend', 'form', 'template'], function ($, undefined, Frontend, Form, Template) {
    var Controller = {
        profile: function () {
            // 给上传按钮添加上传成功事件
            $("#faupload-avatar").data("upload-success", function (data) {
                var url = Fast.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success(__('Uploaded successful'));
            });
            //为表单绑定事件
            Form.api.bindevent($("#profile-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = "/index/myadmin/center";
                }, 1000);
            }, function (data) {
            });
        },
        myCommon: function () {
            $("#c-group_id").data("eSelect", function (obj) {
                let label = obj.label || null;
                $('#label').selectPageData(JSON.parse(label));
            });
        },
        center: function () {
            this.myCommon();
            // 给上传按钮添加上传成功事件
            $("#faupload-avatar").data("upload-success", function (data) {
                var url = Fast.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success(__('Uploaded successful'));
            });
            //为表单绑定事件
            Form.api.bindevent($("#profile-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = "/index/myadmin/center";
                }, 1000);
            }, function (data) {
            });
        },
        register: function () {
            this.myCommon();
            // 给上传按钮添加上传成功事件
            $("#faupload-avatar").data("upload-success", function (data) {
                var url = Fast.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success(__('Uploaded successful'));
            });
            //为表单绑定事件
            Form.api.bindevent($("#profile-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = "/index/myadmin/center";
                }, 1000);
            }, function (data) {
            });
        },
        apply: function () {
            //为表单绑定事件
            Form.api.bindevent($("#profile-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = "/index/myadmin/user";
                }, 1000);
            }, function (data) {
            });
        },
        withdrawapply: function () {
            jisuan();
            function jisuan() {
                var company = { taxerate: $("#c-taxerate").text() || 0, handrate: $("#c-handrate").text() || 0, };
                var handrate = company.handrate / 100;
                var taxerate = company.taxerate / 100;
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
                //后续操作
            });

            $("#c-handfee,#c-taxefee,#c-money").on("keyup change", function () {
                jisuan();
            });
            // 给上传按钮添加上传成功事件
            $("#faupload-avatar").data("upload-success", function (data) {
                var url = Fast.api.cdnurl(data.url);
                $(".profile-user-img").prop("src", url);
                Toastr.success(__('Uploaded successful'));
            });
            //为表单绑定事件
            Form.api.bindevent($("#profile-form"), function (data, ret) {
                setTimeout(function () {
                    location.href = "/index/myadmin/withdraw";
                }, 1000);
            }, function (data) {
            });

        },
    };
    return Controller;
});
