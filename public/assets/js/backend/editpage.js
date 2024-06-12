define(['jquery', 'bootstrap', 'backend', 'form', 'ace', 'tools'], function ($, undefined, Backend, Form) {

    var Controller = {
        index: function () {
            //初始化编辑器
            editor = Controller.api.editpageInit(false, true);

            //设置标题
            var parent_title = $('.layui-layer-title:last', window.parent.document).html() + '&nbsp;' + Config.filepath;
            $('.layui-layer-title:last', window.parent.document).html(parent_title);

            //改变的时候记录未保存
            editor.on("change", function () {
                $('#update_file').html('<b style="color: #f00"> 未保存</b>');
            });

            // 复制路径
            $(document).on("click", ".btn-copy", function () {
                Controller.api.copy($(this).data('path'));
            });

            //Ctrl + S 保存
            $(document).bind("keydown", function(e) {
                console.log(e.which);
                if (e.ctrlKey && (e.which == 83)) {
                    e.preventDefault();
                    $("form[role=form]").submit();
                    return false;
                }
            });

            //点击保存事件
            $(document).on("click", "#editpage_btn", function () {
                $("form[role=form]").submit();
            });

            //表单提交事件绑定
            Form.api.bindevent($("#add-form"), function (data, ret) {
                return false;
            }, function (data, ret) {
                var content = editor.getValue();
                var file = $('#file').val();
                Fast.api.ajax({
                    url: 'editpage/savefile',
                    data: {file: file, content: content}
                }, function (data, ret) {
                    $('#update_file').html('已保存');
                });
                return false;
            });
        },
        command: function () {
            //初始化编辑器
            editor = Controller.api.editpageInit('text', false);

            //点击命令按钮
            $('.btn').click(function() {
                var val = $(this).data('val');
                editor.setValue($('#' + val).html() + "\n", true);
            })

            $(document).keydown(function(){
                if (event.keyCode == 13) {//回车键的键值为13
                    var content = editor.getValue();
                    var content = content.split(/[\n,]/g);
                    for(var i =0;i<content.length;i++){
                        if(content[i] == ""){
                            content.splice(i, 1);
                            //删除数组索引位置应保持不变
                            i--;
                        }
                    }
                    var len = content.length - 1;
                    content = content[len];
                    if(content.indexOf("php think") == 0){
                        $("form[role=form]").submit();
                    }
                }
            });

            //表单提交事件绑定
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                return false;
            }, function (data, ret) {
                var content = editor.getValue();
                var content = content.split(/[\n,]/g);
                for(var i =0;i<content.length;i++){
                    if(content[i] == ""){
                        content.splice(i, 1);
                        //删除数组索引位置应保持不变
                        i--;
                    }
                }
                var len = content.length - 1;
                content = content[len];

                if(content.indexOf("php think") == 0){
                    Fast.api.ajax({
                        url: 'editpage/command',
                        data: {content: content}
                    }, function (data, ret) {
                        editor.setValue(editor.getValue() + ret.msg + "\n", true);
                    });
                }
                return false;
            });
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            editpageInit: function (language, menu) {
                //loading层
                var index = Layer.load(1, {
                    shade: [0.1, '#fff'] //0.1透明度的白色背景
                });

                //初始化对象
                editor = ace.edit("code");

                //设置风格和语言（更多风格和语言，请到github上相应目录查看）
                theme = Config.editpage_config.theme;//clouds,chaos

                if(language){
                    language = language;
                }else{
                    language = Config.language_type;
                }

                editor.setTheme("ace/theme/" + theme);
                editor.session.setMode("ace/mode/" + language);

                //字体大小
                editor.setFontSize(parseInt(Config.editpage_config.font_size));

                //设置只读（true时只读，用于展示代码）
                editor.setReadOnly(parseInt(Config.editpage_config.setreadonly));

                //自动换行,设置为off关闭
                editor.setOption("wrap", Config.editpage_config.auto_wrap);

                //启用提示菜单
                if(menu){
                    ace.require("ace/ext/language_tools");
                    editor.setOptions({
                        enableBasicAutocompletion: true,
                        enableSnippets: true,
                        enableLiveAutocompletion: true
                    });
                }

                $('#add-form').show();
                Layer.close(index);
                return editor;
            },
            copy: function (val) {
                var oInput = document.createElement('input');
                oInput.value = val;
                document.body.appendChild(oInput);
                oInput.select(); // 选择对象
                document.execCommand("Copy"); // 执行浏览器复制命令
                oInput.className = 'oInput';
                oInput.style.display='none';
                Layer.alert('<b>拷贝路径成功，请使用Ctrl+V粘贴。</b><br/>' + val);
            }
        }
    };
    return Controller;
});
