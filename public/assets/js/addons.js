define([], function () {
    require.config({
    paths: {
        'geetest': '../addons/geetest/js/geetest.min'
    }
});

require(['geetest'], function (Geet) {
    var geetInit = false;
    window.renderGeetest = function (captcha) {
        captcha = captcha ? captcha : $("input[name=captcha]");
        if (captcha.length > 0) {
            var form = captcha.closest("form");
            var parentDom = captcha.parent();
            // 非文本验证码
            if ($("a[data-event][data-url]", parentDom).size() > 0) {
                return;
            }
            parentDom.removeClass('input-group')
                .html('<div class="embed-captcha"><input type="hidden" name="captcha" class="form-control" data-msg-required="请完成验证码验证" data-rule="required" /> </div> <p class="wait show" style="min-height:44px;line-height:44px;">正在加载验证码...</p>');

            Fast.api.ajax("/addons/geetest/index/start", function (data) {
                // 参数1：配置参数
                // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                initGeetest({
                    gt: data.gt,
                    https: true,
                    challenge: data.challenge,
                    new_captcha: data.new_captcha,
                    product: Config.geetest.product, // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                    width: '100%',
                    offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                }, function (captchaObj) {
                    // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
                    geetInit = captchaObj;
                    captchaObj.appendTo($(".embed-captcha", form));
                    captchaObj.onReady(function () {
                        $(".wait", form).remove();
                    });
                    captchaObj.onSuccess(function () {
                        var result = captchaObj.getValidate();
                        if (result) {
                            $('input[name="captcha"]', form).val('ok');
                        }
                    });
                    captchaObj.onError(function () {
                        geetInit.reset();
                    });
                });
                // 监听表单错误事件
                form.on("error.form", function (e, data) {
                    geetInit.reset();
                });
                return false;
            });
        }
    };
    renderGeetest($("input[name=captcha]"));
});

require.config({
    paths: {
        'nkeditor': '../addons/nkeditor/js/customplugin',
        'nkeditor-core': '../addons/nkeditor/nkeditor',
        'nkeditor-lang': '../addons/nkeditor/lang/zh-CN',
    },
    shim: {
        'nkeditor': {
            deps: [
                'nkeditor-core',
                'nkeditor-lang'
            ]
        },
        'nkeditor-core': {
            deps: [
                'css!../addons/nkeditor/themes/black/editor.min.css',
                'css!../addons/nkeditor/css/common.css'
            ],
            exports: 'window.KindEditor'
        },
        'nkeditor-lang': {
            deps: [
                'nkeditor-core'
            ]
        }
    }
});
require(['form'], function (Form) {
    var _bindevent = Form.events.bindevent;
    Form.events.bindevent = function (form) {
        _bindevent.apply(this, [form]);
        if ($(Config.nkeditor.classname || '.editor', form).length > 0) {
            require(['nkeditor', 'upload'], function (Nkeditor, Upload) {
                var getFileFromBase64, uploadFiles;
                uploadFiles = async function (files) {
                    var self = this;
                    for (var i = 0; i < files.length; i++) {
                        try {
                            await new Promise((resolve) => {
                                var url, html, file;
                                file = files[i];
                                Upload.api.send(file, function (data) {
                                    url = Fast.api.cdnurl(data.url, true);
                                    if (file.type.indexOf("image") !== -1) {
                                        self.exec("insertimage", url);
                                    } else {
                                        html = '<a class="ke-insertfile" href="' + url + '" data-ke-src="' + url + '" target="_blank">' + (file.name || url) + '</a>';
                                        self.exec("inserthtml", html);
                                    }
                                    resolve();
                                }, function () {
                                    resolve();
                                });
                            });
                        } catch (e) {

                        }
                    }
                };
                getFileFromBase64 = function (data, url) {
                    var arr = data.split(','), mime = arr[0].match(/:(.*?);/)[1],
                        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }
                    var filename, suffix;
                    if (typeof url != 'undefined') {
                        var urlArr = url.split('.');
                        filename = url.substr(url.lastIndexOf('/') + 1);
                        suffix = urlArr.pop();
                    } else {
                        filename = Math.random().toString(36).substring(5, 15);
                    }
                    if (!suffix) {
                        suffix = data.substring("data:image/".length, data.indexOf(";base64"));
                    }

                    var exp = new RegExp("\\." + suffix + "$", "i");
                    filename = exp.test(filename) ? filename : filename + "." + suffix;
                    var file = new File([u8arr], filename, {type: mime});
                    return file;
                };

                $(Config.nkeditor.classname || '.editor', form).each(function () {
                    var that = this;
                    var options = $(this).data("nkeditor-options");
                    var editor = Nkeditor.create(that, $.extend({}, {
                        width: '100%',
                        filterMode: false,
                        wellFormatMode: false,
                        allowMediaUpload: true, //是否允许媒体上传
                        allowFileManager: true,
                        allowImageUpload: true,
                        baiduMapKey: Config.nkeditor.baidumapkey || '',
                        baiduMapCenter: Config.nkeditor.baidumapcenter || '',
                        fontSizeTable: ['9px', '10px', '12px', '14px', '16px', '18px', '21px', '24px', '32px'],
                        formulaPreviewUrl: typeof Config.nkeditor != 'undefined' && Config.nkeditor.formulapreviewurl ? Config.nkeditor.formulapreviewurl : "", //数学公式的预览地址
                        cssPath: Config.site.cdnurl + '/assets/addons/nkeditor/plugins/code/prism.css',
                        cssData: "body {font-size: 13px}",
                        fillDescAfterUploadImage: false, //是否在上传后继续添加描述信息
                        themeType: typeof Config.nkeditor != 'undefined' ? Config.nkeditor.theme : 'black', //编辑器皮肤,这个值从后台获取
                        fileManagerJson: Fast.api.fixurl("/addons/nkeditor/index/attachment/module/" + Config.modulename),
                        items: [
                            'source', 'undo', 'redo', 'preview', 'print', 'template', 'code', 'quote', 'cut', 'copy', 'paste',
                            'plainpaste', 'justifyleft', 'justifycenter', 'justifyright',
                            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
                            'superscript', 'clearhtml', 'quickformat', 'selectall',
                            'formatblock', 'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'bold',
                            'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', 'image', 'multiimage', 'graft',
                            'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
                            'anchor', 'link', 'unlink', 'remoteimage', 'search', 'math', 'about', 'fullscreen'
                        ],
                        afterCreate: function () {
                            var self = this;
                            //Ctrl+回车提交
                            Nkeditor.ctrl(document, 13, function () {
                                self.sync();
                                $(that).closest("form").submit();
                            });
                            Nkeditor.ctrl(self.edit.doc, 13, function () {
                                self.sync();
                                $(that).closest("form").submit();
                            });
                            //粘贴上传
                            $("body", self.edit.doc).bind('paste', function (event) {
                                var originalEvent;
                                originalEvent = event.originalEvent;
                                if (originalEvent.clipboardData && originalEvent.clipboardData.files.length > 0) {
                                    uploadFiles.call(self, originalEvent.clipboardData.files);
                                    return false;
                                }
                            });
                            //拖拽上传
                            $("body", self.edit.doc).bind('drop', function (event) {
                                var originalEvent;
                                originalEvent = event.originalEvent;
                                if (originalEvent.dataTransfer && originalEvent.dataTransfer.files.length > 0) {
                                    uploadFiles.call(self, originalEvent.dataTransfer.files);
                                    return false;
                                }
                            });
                        },
                        afterChange: function () {
                            $(this.srcElement[0]).trigger("change");
                        },
                        //自定义处理
                        beforeUpload: function (callback, file) {
                            var file = file ? file : $("input.ke-upload-file", this.form).prop('files')[0];
                            Upload.api.send(file, function (data) {
                                var data = {code: '000', data: {url: Fast.api.cdnurl(data.url, true)}, title: '', width: '', height: '', border: '', align: ''};
                                callback(data);
                            });
                        },
                        //错误处理 handler
                        errorMsgHandler: function (message, type) {
                            try {
                                Fast.api.msg(message);
                                console.log(message, type);
                            } catch (Error) {
                                alert(message);
                            }
                        },
                        uploadFiles: uploadFiles
                    }, options || {}));
                    $(this).data("nkeditor", editor);
                });
            });
        }
    }
});

if (Config.modulename === 'index' && Config.controllername === 'user' && ['login', 'register'].indexOf(Config.actionname) > -1 && $("#register-form,#login-form").length > 0 && $(".social-login").length == 0) {
    $("#register-form,#login-form").append(Config.third.loginhtml || '');
}

require.config({
    paths: {
        'xccms-async': '../addons/xccms/js/async',
        'xccms-BMap': ['//api.map.baidu.com/api?v=2.0&ak='],
    },
    shim: {
        'xccms-BMap': {
            deps: ['jquery'],
            exports: 'BMap'
        }
    }
});


});