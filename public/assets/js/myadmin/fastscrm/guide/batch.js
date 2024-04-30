define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'fastscrm/guide/batch/index' + location.search,
                    add_url: 'fastscrm/guide/batch/add',
                    del_url: 'fastscrm/guide/batch/del',
                    multi_url: 'fastscrm/guide/batch/multi',
                    import_url: 'fastscrm/guide/batch/import',
                    table: 'fastscrm_customer_batch',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'remark', title: __('Remark'), operate: 'LIKE'},
                        {field: 'tags', title: __('Tags'), operate: false,formatter:Controller.api.formatter.tags},
                        {field: 'worker_name', title: __('Worker_name'), operate: '=',searchList: $.getJSON("fastscrm/company/worker/searchfind"),formatter:Controller.api.formatter.worker_name},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
                        {field: 'branchnum', title: __('Branchnum')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'send',
                                    title: __('提醒'),
                                    classname: 'btn btn-xs btn-success btn-send',
                                    icon: 'fa fa-bullhorn',
                                },
                            ],
                            formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            $(document).on('click', '.btn-addworker', function () {
                var ids = Table.api.selectedids(table);
                var url = 'fastscrm/template/workers';
                Fast.api.open(url,'选择员工',{
                    callback:function(data){
                        Fast.api.ajax({
                            url: 'fastscrm/guide/batch/multi',
                            data: {
                                worker:data[0].userid,
                                ids:ids
                            },
                        }, function (data, res) {
                            $(".btn-refresh").trigger("click");
                        })
                    }
                });
            });
            $(document).on('click', '.btn-send', function () {
                var index = $(this).data("row-index");
                var row = Table.api.getrowbyindex(table, index);
                var url = 'fastscrm/guide/batch/send';
                    Layer.confirm('确认发送应用消息提醒吗？', {
                        icon: 3,
                        title: '提示'
                    }, function (index) {
                        Fast.api.ajax({
                            url: url,
                            data: {
                                worker_id:row.worker_id
                            },
                        }, function (data, res) {
                            layer.close(index);
                            $(".btn-refresh").trigger("click");
                        })
                    })
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            new Vue({
                el: '#batchadd',
                components:{  }, // 注册
                data() {
                    return {
                        tags: [],
                        mode: "transfer", // transfer addressList
                        workers:[]
                    }
                },

                methods:{
                    addtags:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.tags,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/tags?ids='+ids;
                        Fast.api.open(url,'选择标签',{
                            callback:function(data){
                                _self.tags= data;
                            }
                        });
                    },
                    addworkers:function () {
                        var _self = this;
                        var ids = '';
                        $.each(_self.workers,function (index,value) {
                            ids+=value.id+',';
                        });
                        ids=ids.substring(0,ids.length-1);
                        var url = 'fastscrm/template/workers?ids='+ids;
                        Fast.api.open(url,'选择员工',{
                            callback:function(data){
                                _self.workers= data;
                            }
                        });
                    },
                    reset:function () {
                        this.tags=[];
                        this.workers=[];
                    },
                    deltag:function (e) {
                        this.tags.splice(this.tags.indexOf(e), 1);
                    },
                    submit:function () {
                        var _self = this;
                        var workers = '';
                        var tags = '';
                        $.each(_self.workers,function (index,value) {
                            workers+=value.userid+',';
                        });
                        workers=workers.substring(0,workers.length-1);
                        $.each(_self.tags,function (index,value) {
                            tags+=value.tagid+',';
                        });
                        tags=tags.substring(0,tags.length-1);
                        Fast.api.ajax({
                            url: 'fastscrm/guide/batch/add',
                            data: {
                                file:$('#c-file').val(),
                                tags:tags,
                                workers:workers
                            },
                        }, function (data, res) {
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        })
                    },
                },
                mounted(){

                },
            });
            $('.element').show();
            Controller.api.bindevent();

        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"), '', '', function(){
                    return false;
                });
            },
            formatter: {//渲染的方法
                worker_name: function (value, row, index) {
                    return row.worker_name;
                },
                tags: function (value, row, index) {
                    var html = '';
                    row.tags.forEach(function (item) {

                        html+='<span class="sentence_li">'+item.name+'</span>';
                    });
                    return html;
                }
            },
        }
    };
    return Controller;
});
