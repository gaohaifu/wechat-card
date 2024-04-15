define([], function () {
    if (typeof Config.upload.storage !== 'undefined' && Config.upload.storage === 'alioss') {
    require(['upload'], function (Upload) {
        //获取文件MD5值
        var getFileMd5 = function (file, cb) {
            //如果savekey中未检测到md5，则无需获取文件md5，直接返回upload的uuid
            if (!Config.upload.savekey.match(/\{(file)?md5\}/)) {
                cb && cb(file.upload.uuid);
                return;
            }
            require(['../addons/alioss/js/spark'], function (SparkMD5) {
                var blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice,
                    chunkSize = 10 * 1024 * 1024,
                    chunks = Math.ceil(file.size / chunkSize),
                    currentChunk = 0,
                    spark = new SparkMD5.ArrayBuffer(),
                    fileReader = new FileReader();

                fileReader.onload = function (e) {
                    spark.append(e.target.result);
                    currentChunk++;
                    if (currentChunk < chunks) {
                        loadNext();
                    } else {
                        cb && cb(spark.end());
                    }
                };

                fileReader.onerror = function () {
                    console.warn('文件读取错误');
                };

                function loadNext() {
                    var start = currentChunk * chunkSize,
                        end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;

                    fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
                }

                loadNext();

            });
        };

        var _onInit = Upload.events.onInit;
        //初始化中完成判断
        Upload.events.onInit = function () {
            _onInit.apply(this, Array.prototype.slice.apply(arguments));
            //如果上传接口不是阿里OSS，则不处理
            if (this.options.url !== Config.upload.uploadurl) {
                return;
            }
            $.extend(this.options, {
                //关闭自动处理队列功能
                autoQueue: false,
                params: function (files, xhr, chunk) {
                    var params = Config.upload.multipart;
                    if (chunk) {
                        return $.extend({}, params, {
                            filesize: chunk.file.size,
                            filename: chunk.file.name,
                            chunkid: chunk.file.upload.uuid,
                            chunkindex: chunk.index,
                            chunkcount: chunk.file.upload.totalChunkCount,
                            chunksize: this.options.chunkSize,
                            chunkfilesize: chunk.dataBlock.data.size,
                            width: chunk.file.width || 0,
                            height: chunk.file.height || 0,
                            type: chunk.file.type,
                            uploadId: chunk.file.uploadId,
                            key: chunk.file.key,
                        });
                    } else {
                        params = $.extend({}, params, files[0].params);
                        params.category = files[0].category || '';
                    }
                    return params;
                },
                chunkSuccess: function (chunk, file, response) {
                    var etag = chunk.xhr.getResponseHeader("ETag").replace(/(^")|("$)/g, '');
                    file.etags = file.etags ? file.etags : [];
                    file.etags[chunk.index] = etag;
                },
                chunksUploaded: function (file, done) {
                    var that = this;
                    Fast.api.ajax({
                        url: "/addons/alioss/index/upload",
                        data: {
                            action: 'merge',
                            filesize: file.size,
                            filename: file.name,
                            chunkid: file.upload.uuid,
                            chunkcount: file.upload.totalChunkCount,
                            md5: file.md5,
                            key: file.key,
                            uploadId: file.uploadId,
                            etags: file.etags,
                            category: file.category || '',
                            aliosstoken: Config.upload.multipart.aliosstoken,
                        },
                    }, function (data, ret) {
                        done(JSON.stringify(ret));
                        return false;
                    }, function (data, ret) {
                        file.accepted = false;
                        that._errorProcessing([file], ret.msg);
                        return false;
                    });

                },
            });

            var _success = this.options.success;
            //先移除已有的事件
            this.off("success", _success).on("success", function (file, response) {
                var ret = {code: 0, msg: response};
                try {
                    if (response) {
                        ret = typeof response === 'string' ? JSON.parse(response) : response;
                    }
                    if (file.xhr.status === 200) {
                        if (Config.upload.uploadmode === 'client') {
                            ret = {code: 1, data: {url: '/' + file.key}};
                            var url = ret.data.url || '';

                            Fast.api.ajax({
                                url: "/addons/alioss/index/notify",
                                data: {name: file.name, url: url, md5: file.md5, size: file.size, width: file.width || 0, height: file.height || 0, type: file.type, category: file.category || '', aliosstoken: Config.upload.multipart.aliosstoken}
                            }, function () {
                                return false;
                            }, function () {
                                return false;
                            });
                        }
                    } else {
                        console.error(file.xhr);
                    }
                } catch (e) {
                    console.error(e);
                }
                _success.call(this, file, ret);
            });

            this.on("addedfile", function (file) {
                var that = this;
                setTimeout(function () {
                    if (file.status === 'error') {
                        return;
                    }
                    getFileMd5(file, function (md5) {
                        var chunk = that.options.chunking && file.size > that.options.chunkSize ? 1 : 0;
                        var params = $(that.element).data("params") || {};
                        var category = typeof params.category !== 'undefined' ? params.category : ($(that.element).data("category") || '');
                        category = typeof category === 'function' ? category.call(that, file) : category;
                        Fast.api.ajax({
                            url: "/addons/alioss/index/params",
                            data: {method: 'POST', category: category, md5: md5, name: file.name, type: file.type, size: file.size, chunk: chunk, chunksize: that.options.chunkSize, aliosstoken: Config.upload.multipart.aliosstoken},
                        }, function (data) {
                            file.md5 = md5;
                            file.id = data.id;
                            file.key = data.key;
                            file.date = data.date;
                            file.uploadId = data.uploadId;
                            file.policy = data.policy;
                            file.signature = data.signature;
                            file.partsAuthorization = data.partsAuthorization;
                            file.params = data;
                            file.category = category;

                            if (file.status != 'error') {
                                //开始上传
                                that.enqueueFile(file);
                            } else {
                                that.removeFile(file);
                            }
                            return false;
                        }, function () {
                            that.removeFile(file);
                        });
                    });
                }, 0);
            });

            if (Config.upload.uploadmode === 'client') {
                var _method = this.options.method;
                var _url = this.options.url;
                this.options.method = function (files) {
                    if (files[0].upload.chunked) {
                        var chunk = null;
                        files[0].upload.chunks.forEach(function (item) {
                            if (item.status === 'uploading') {
                                chunk = item;
                            }
                        });
                        if (!chunk) {
                            return "POST";
                        } else {
                            return "PUT";
                        }
                    }
                    return _method;
                };
                this.options.url = function (files) {
                    if (files[0].upload.chunked) {
                        var chunk = null;
                        files[0].upload.chunks.forEach(function (item) {
                            if (item.status === 'uploading') {
                                chunk = item;
                            }
                        });
                        var index = chunk.dataBlock.chunkIndex;
                        // debugger;
                        this.options.headers = {"Authorization": "OSS " + files[0]['id'] + ":" + files[0]['partsAuthorization'][index], "x-oss-date": files[0]['date']};
                        if (!chunk) {
                            return Config.upload.uploadurl + "/" + files[0].key + "?uploadId=" + files[0].uploadId;
                        } else {
                            return Config.upload.uploadurl + "/" + files[0].key + "?partNumber=" + (index + 1) + "&uploadId=" + files[0].uploadId;
                        }
                    }
                    return _url;
                };
                this.on("sending", function (file, xhr, formData) {
                    var that = this;
                    if (file.upload.chunked) {
                        var _send = xhr.send;
                        xhr.send = function () {
                            var chunk = null;
                            file.upload.chunks.forEach(function (item) {
                                if (item.status == 'uploading') {
                                    chunk = item;
                                }
                            });
                            if (chunk) {
                                _send.call(xhr, chunk.dataBlock.data);
                            }
                        };
                    }
                });
            }
        };
    });
}

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