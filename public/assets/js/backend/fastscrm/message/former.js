define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/message/former/index' + location.search,
                    add_url: 'fastscrm/message/former/add',
                    edit_url: 'fastscrm/message/former/edit',
                    del_url: 'fastscrm/message/former/del',
                    multi_url: 'fastscrm/message/former/multi',
                    import_url: 'fastscrm/message/former/import',
                    table: 'fastscrm_message_template',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'msg_type', title: __('Msg_type'), searchList: {"text":__('Msg_type text'),"markdown":__('Msg_type markdown'),"image":__('Msg_type image'),"news":__('Msg_type news'),"file":__('Msg_type file'),"template_card":__('Msg_type template_card')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'fastscrm/message/former/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'fastscrm/message/former/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'fastscrm/message/former/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        add: function () {
          var vm =  new Vue({
                el: '#templateAdd',
                components:{  }, // 注册
                data() {
                    return {
                        ruleForm: {
                            name: '',
                            word_list: '',
                            msg_type: '',
                            text:'',
                            markdown:'',
                            title:'',
                            description:'',
                            url:'',
                            picurl:'',
                            status:'1',
                            file:'',
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入模版名称', trigger: 'blur' },
                            ],
                            text: [
                                { required: true, message: '请输入内容', trigger: 'blur' },
                            ],
                            markdown: [
                                { required: true, message: '请输入markdown格式内容', trigger: 'blur' },
                            ],
                            title: [
                                { required: true, message: '请输入标题', trigger: 'blur' },
                            ],
                            url: [
                                { required: true, message: '请输入跳转的链接', trigger: 'blur' },
                            ],
                            msg_type: [
                                { required: true, message: '请选择消息类型', trigger: 'change' }
                            ],
                            word_list: [
                                { required: true, message: '请输入敏感词', trigger: 'blur' },
                            ],
                        }
                    }
                },
                mounted(){
                },
                methods:{
                    submit:function () {
                        var _self = this;
                        var params = _self.ruleForm;
                        console.log(params.markdown)
                        Fast.api.ajax({
                            url: 'fastscrm/message/former/add',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                    submitForm(formName) {
                        var _self = this;
                        this.$refs[formName].validate((valid) => {
                            if (valid) {
                                _self.submit();
                            } else {
                                return false;
                            }
                        });
                    },
                    resetForm(formName) {
                        this.$refs[formName].resetFields();
                    },
                    changeType:function (v) {
                        if(v=='image' || v=='news'){
                            $('#picurl').show();
                        }else{
                            $('#picurl').hide();
                        }
                        if(v=='file'){
                            $('#file').show();
                        }else{
                            $('#file').hide();
                        }
                    }
                },
            });
            $(document).on("change", "#c-file", function () {
                vm.ruleForm.file = $('#c-file').val();
            });
            $(document).on("change", "#c-image", function () {
                vm.ruleForm.picurl = $('#c-image').val();

            });
            Form.api.bindevent($(".form-group"));
        },
        edit: function () {
            var vm =  new Vue({
                el: '#templateEdit',
                components:{  }, // 注册
                data() {
                    return {
                        ruleForm: {
                            id: Config.row.id,
                            name: Config.row.name,
                            msg_type: Config.row.msg_type,
                            text: Config.row.json.text,
                            markdown: Config.row.json.markdown,
                            title: Config.row.json.title,
                            description: Config.row.json.description,
                            url: Config.row.json.url,
                            picurl: Config.row.json.picurl,
                            status: Config.row.status,
                            file: Config.row.json.file,
                        },
                        rules: {
                            name: [
                                { required: true, message: '请输入模版名称', trigger: 'blur' },
                            ],
                            text: [
                                { required: true, message: '请输入内容', trigger: 'blur' },
                            ],
                            markdown: [
                                { required: true, message: '请输入markdown格式内容', trigger: 'blur' },
                            ],
                            title: [
                                { required: true, message: '请输入标题', trigger: 'blur' },
                            ],
                            url: [
                                { required: true, message: '请输入跳转的链接', trigger: 'blur' },
                            ],
                            msg_type: [
                                { required: true, message: '请选择消息类型', trigger: 'change' }
                            ],
                            word_list: [
                                { required: true, message: '请输入敏感词', trigger: 'blur' },
                            ],
                        }
                    }
                },
                mounted(){
                    this.changeType(this.ruleForm.msg_type)
                },
                methods:{
                    submit:function () {
                        var _self = this;
                        var params = _self.ruleForm;
                        Fast.api.ajax({
                            url: 'fastscrm/message/former/edit',
                            data: params,
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                    submitForm(formName) {
                        var _self = this;
                        this.$refs[formName].validate((valid) => {
                            if (valid) {
                                _self.submit();
                            } else {
                                return false;
                            }
                        });
                    },
                    resetForm(formName) {
                        this.$refs[formName].resetFields();
                    },
                    changeType:function (v) {
                        if(v=='image' || v=='news'){
                            $('#picurl').show();
                        }else{
                            $('#picurl').hide();
                        }
                        if(v=='file'){
                            $('#file').show();
                        }else{
                            $('#file').hide();
                        }
                    }
                },
            });
            $(document).on("change", "#c-file", function () {
                vm.ruleForm.file = $('#c-file').val();
            });
            $(document).on("change", "#c-image", function () {
                vm.ruleForm.picurl = $('#c-image').val();

            });
            Form.api.bindevent($(".form-group"));
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
