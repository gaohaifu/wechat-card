define(['jquery', 'bootstrap', 'frontend', 'form', 'template'], function ($, undefined, Frontend, Form, Template) {
    var Controller = {
        content:function () {
            $(document).on("click", ".navbar-toggle", function () {
                //alert('gfdsdsg');
                //$("body").toggleClass("sidebar-open");
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
