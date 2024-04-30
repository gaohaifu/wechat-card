define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        tags: function () {
            var tags = [];
            $(document).on("click", ".sentence_li", function () {
                if($(this).hasClass('active')){
                    $(this).removeClass('active');
                }else {
                    $(this).addClass('active');
                }
            });
            $(document).on("click", ".btn-primary", function () {
                $('.active').each(function () {
                    let object = {};
                    object.id = $(this).data('id');
                    object.name = $(this).data('name');
                    object.tagid = $(this).data('tagid');
                    tags.push(object);
                });
            });
            $(document).on('click', '.btn-callback', function () {
                Fast.api.close(tags);
            });
            Controller.api.bindevent();
        },
        workers: function () {
            new Vue({
                el: '#workers',
                data() {
                    return {
                        filterText: '',
                        defaultProps: {
                            children: 'childlist',
                            label: 'name'
                        },
                        data: [],
                        choseids: Config.choseids,
                        choses:[],
                        tableData: [],
                        total: 0,
                        expanded:[]
                    }
                },
                watch: {
                    filterText(val) {
                        this.$refs.tree.filter(val);
                    }
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        Fast.api.ajax({
                            url: 'fastscrm/template/workers',
                            data: {
                                choseids: self.choseids
                            },
                        }, function (data, res) {
                                self.data = JSON.parse(JSON.stringify(res.data.list));
                                self.tableData = res.data.chosedata;
                                self.choses = res.data.choses;
                                self.expanded.push(res.data.list[0].id);
                                self.total = res.data.total;
                                self.setCheckend(self.choses);
                                return false;
                            }
                        )
                    },
                    filterNode(value, data) {
                        if (!value) return true;
                        return data.name.indexOf(value) !== -1;
                    },
                    check(node,temp){
                        var self = this;
                        if(node.userid){
                           self.setData(node,node.id);
                        }else{
                            if(node.childlist.length>0){
                                self.loopData(node);
                            }
                        }
                    },
                    loopData(node){
                        var self = this;
                        $.each(node.childlist,function(index,value){
                            if(value.userid){
                                self.setData(value,value.id);
                            }else if(value.childlist.length>0){
                                self.loopData(value);
                            }
                        });
                    },
                    setData(node,id){
                        var self = this;
                        let index = $.inArray(id,self.getCheckedKeys());
                        if(index === -1){
                            self.tableData.splice(self.tableData.indexOf(node),1);
                            self.choses.splice($.inArray(id,self.choses),1);
                        }else if($.inArray(id,self.choses) === -1){
                            self.tableData.push(node);
                            self.choses.push(id);
                        }
                    },
                    getCheckedNodes() {
                        return  this.$refs.tree.getCheckedNodes();
                    },
                    getCheckedKeys() {
                        return  this.$refs.tree.getCheckedKeys();
                    },
                    errorHandler() {
                        return true;
                    },
                    del(index) {
                        this.$refs.tree.setChecked(this.tableData[index].id,false,false);
                        this.choses.splice($.inArray(this.tableData[index].id,this.choses),1);
                        this.tableData.splice(index,1);
                    },
                    delAll(){
                        this.tableData=[];
                        this.choses=[];
                        this.$refs.tree.setCheckedNodes([]);

                    },
                    setCheckend(data) {
                        this.$refs.tree.setCheckedKeys(data,false);
                    },
                    backData(){
                        Fast.api.close(this.tableData);
                    }
                }
            });
            $('#workers').show();
            Controller.api.bindevent();
        },
        departs: function () {
            new Vue({
                el: '#departs',
                data() {
                    return {
                        filterText: '',
                        defaultProps: {
                            children: 'childlist',
                            label: 'name'
                        },
                        data: [],
                        choseids: Config.choseids,
                        choses:[],
                        tableData: [],
                        total: 0,
                        expanded:[]
                    }
                },
                watch: {
                    filterText(val) {
                        this.$refs.tree.filter(val);
                    }
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        Fast.api.ajax({
                                url: 'fastscrm/template/departs',
                                data: {
                                    choseids: self.choseids
                                },
                            }, function (data, res) {
                                self.data = JSON.parse(JSON.stringify(res.data.list));
                                self.tableData = res.data.chosedata;
                                self.choses = res.data.choses;
                                self.expanded.push(res.data.list[0].id);
                                self.total = res.data.total;
                                self.setCheckend(self.choses);
                                return false;
                            }
                        )
                    },
                    filterNode(value, data) {
                        if (!value) return true;
                        return data.name.indexOf(value) !== -1;
                    },
                    check(node,temp){
                        var self = this;
                            self.setData(node,node.id);
                    },
                    setData(node,id){
                        var self = this;
                        let index = $.inArray(id,self.getCheckedKeys());
                        if(index === -1){
                            self.tableData.splice(self.tableData.indexOf(node),1);
                            self.choses.splice($.inArray(id,self.choses),1);
                        }else if($.inArray(id,self.choses) === -1){
                            self.tableData.push(node);
                            self.choses.push(id);
                        }
                    },
                    getCheckedNodes() {
                        return  this.$refs.tree.getCheckedNodes();
                    },
                    getCheckedKeys() {
                        return  this.$refs.tree.getCheckedKeys();
                    },
                    errorHandler() {
                        return true;
                    },
                    del(index) {
                        this.$refs.tree.setChecked(this.tableData[index].id,false,false);
                        this.choses.splice($.inArray(this.tableData[index].id,this.choses),1);
                        this.tableData.splice(index,1);
                    },
                    delAll(){
                        this.tableData=[];
                        this.choses=[];
                        this.$refs.tree.setCheckedNodes([]);

                    },
                    setCheckend(data) {
                        this.$refs.tree.setCheckedKeys(data,false);
                    },
                    backData(){
                        Fast.api.close(this.tableData);
                    }
                }
            });
            Controller.api.bindevent();
        },
        webhooks: function () {
            new Vue({
                el: '#webhooks',
                data() {
                    return {
                        filterText: '',
                        defaultProps: {
                            children: 'childlist',
                            label: 'name'
                        },
                        data: [],
                        choseids: Config.choseids,
                        choses:[],
                        tableData: [],
                        total: 0,
                        expanded:[]
                    }
                },
                watch: {
                    filterText(val) {
                        this.$refs.tree.filter(val);
                    }
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        Fast.api.ajax({
                                url: 'fastscrm/template/webhooks',
                                data: {
                                    choseids: self.choseids
                                },
                            }, function (data, res) {
                                self.data = JSON.parse(JSON.stringify(res.data.list));
                                self.tableData = res.data.chosedata;
                                self.choses = res.data.choses;
                                self.expanded.push(res.data.list[0].id);
                                self.total = res.data.total;
                                self.setCheckend(self.choses);
                                return false;
                            }
                        )
                    },
                    filterNode(value, data) {
                        if (!value) return true;
                        return data.name.indexOf(value) !== -1;
                    },
                    check(node,temp){
                        var self = this;
                        if(node.group_id){
                            self.setData(node,node.id);
                        }else{
                            if(node.childlist.length>0){
                                self.loopData(node);
                            }
                        }
                    },
                    loopData(node){
                        var self = this;
                        $.each(node.childlist,function(index,value){
                            if(value.group_id){
                                self.setData(value,value.id);
                            }else if(value.childlist.length>0){
                                self.loopData(value);
                            }
                        });
                    },
                    setData(node,id){
                        var self = this;
                        let index = $.inArray(id,self.getCheckedKeys());
                        if(index === -1){
                            self.tableData.splice(self.tableData.indexOf(node),1);
                            self.choses.splice($.inArray(id,self.choses),1);
                        }else if($.inArray(id,self.choses) === -1){
                            self.tableData.push(node);
                            self.choses.push(id);
                        }
                    },
                    getCheckedNodes() {
                        return  this.$refs.tree.getCheckedNodes();
                    },
                    getCheckedKeys() {
                        return  this.$refs.tree.getCheckedKeys();
                    },
                    errorHandler() {
                        return true;
                    },
                    del(index) {
                        this.$refs.tree.setChecked(this.tableData[index].id,false,false);
                        this.choses.splice($.inArray(this.tableData[index].id,this.choses),1);
                        this.tableData.splice(index,1);
                    },
                    delAll(){
                        this.tableData=[];
                        this.choses=[];
                        this.$refs.tree.setCheckedNodes([]);

                    },
                    setCheckend(data) {
                        this.$refs.tree.setCheckedKeys(data,false);
                    },
                    backData(){
                        Fast.api.close(this.tableData);
                    }
                }
            });
            Controller.api.bindevent();
        },
        delworkers: function () {
            new Vue({
                el: '#delworkers',
                data() {
                    return {
                        filterText: '',
                        defaultProps: {
                            children: 'childlist',
                            label: 'name'
                        },
                        data: [],
                        choseids: Config.choseids,
                        choses:[],
                        tableData: [],
                        total: 0,
                        expanded:[]
                    }
                },
                watch: {
                    filterText(val) {
                        this.$refs.tree.filter(val);
                    }
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        Fast.api.ajax({
                                url: 'fastscrm/template/delworkers',
                                data: {
                                    choseids: self.choseids
                                },
                            }, function (data, res) {
                                self.data = JSON.parse(JSON.stringify(res.data.list));
                                self.tableData = res.data.chosedata;
                                self.choses = res.data.choses;
                                self.expanded.push(res.data.list[0].id);
                                self.total = res.data.total;
                                self.setCheckend(self.choses);
                                return false;
                            }
                        )
                    },
                    filterNode(value, data) {
                        if (!value) return true;
                        return data.name.indexOf(value) !== -1;
                    },
                    check(node,temp){
                        var self = this;
                        if(node.userid){
                            self.setData(node,node.id);
                        }else{
                            if(node.childlist.length>0){
                                self.loopData(node);
                            }
                        }
                    },
                    loopData(node){
                        var self = this;
                        $.each(node.childlist,function(index,value){
                            if(value.userid){
                                self.setData(value,value.id);
                            }else if(value.childlist.length>0){
                                self.loopData(value);
                            }
                        });
                    },
                    setData(node,id){
                        var self = this;
                        let index = $.inArray(id,self.getCheckedKeys());
                        if(index === -1){
                            self.tableData.splice(self.tableData.indexOf(node),1);
                            self.choses.splice($.inArray(id,self.choses),1);
                        }else if($.inArray(id,self.choses) === -1){
                            self.tableData.push(node);
                            self.choses.push(id);
                        }
                    },
                    getCheckedNodes() {
                        return  this.$refs.tree.getCheckedNodes();
                    },
                    getCheckedKeys() {
                        return  this.$refs.tree.getCheckedKeys();
                    },
                    errorHandler() {
                        return true;
                    },
                    del(index) {
                        this.$refs.tree.setChecked(this.tableData[index].id,false,false);
                        this.choses.splice($.inArray(this.tableData[index].id,this.choses),1);
                        this.tableData.splice(index,1);
                    },
                    delAll(){
                        this.tableData=[];
                        this.choses=[];
                        this.$refs.tree.setCheckedNodes([]);

                    },
                    setCheckend(data) {
                        this.$refs.tree.setCheckedKeys(data,false);
                    },
                    backData(){
                        Fast.api.close(this.tableData);
                    }
                }
            });
            Controller.api.bindevent();
        },
        customer: function () {
            new Vue({
                el: '#customer',
                data() {
                    return {
                        data:[],
                        tableData:[],
                        page_data: {
                            limit: 10,
                            page: 1,
                            total:0,
                        },
                        tags: Config.tags,
                        fl_userid: Config.fl_userid,
                        loading:false,
                        multipleSelection: [],
                    }
                },
                watch: {
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        if(self.loading){
                            return false;
                        }
                        self.loading = true;
                        let param = {limit:self.page_data.limit,page:self.page_data.page};
                        param.tags = self.tags;
                        param.fl_userid = self.fl_userid;
                        Fast.api.ajax({
                                url: 'fastscrm/template/customer',
                                data:param,
                                type: 'get',
                            }, function (data, res) {
                                self.tableData = res.data.rows;
                                self.page_data.total = res.data.total;
                                self.loading = false;
                                return false;
                            },  function (ret, res) {
                                self.loading = false;
                        });
                    },
                    toggleSelection(rows) {
                        if (rows) {
                            rows.forEach(row => {
                                this.$refs.multipleTable.toggleRowSelection(row,true);
                            });
                        } else {
                            this.$refs.multipleTable.clearSelection();
                        }
                    },
                    handleSelectionChange(val) {
                        this.multipleSelection = val;
                    },
                    handleSizeChange(val) {
                        this.page_data.limit = val;
                        this.getData();
                    },
                    handleCurrentChange(val) {
                        this.page_data.page = val;
                        this.getData();
                    },
                    backData(){
                        Fast.api.close(this.multipleSelection);
                    },
                    backAllData(){
                        var self = this;
                        let param = {limit:self.page_data.total,page:self.page_data.page};
                        param.tags = self.tags;
                        param.fl_userid = self.fl_userid;
                        Fast.api.ajax({
                            url: 'fastscrm/template/customer',
                            data:param,
                            type: 'get',
                        }, function (data, res) {
                            Fast.api.close(res.data.rows);
                            return false;
                        },  function (ret, res) {
                        });

                    }
                }
            });
            Controller.api.bindevent();
        },
        chat: function () {
            new Vue({
                el: '#chat',
                data() {
                    return {
                        data:[],
                        tableData:[],
                        page_data: {
                            limit: 10,
                            page: 1,
                            total:0,
                        },
                        fl_userid: Config.fl_userid,
                        loading:false,
                        multipleSelection: [],
                    }
                },
                watch: {
                },
                mounted(){
                    this.getData();
                },
                methods: {
                    getData() {
                        var self = this;
                        if(self.loading){
                            return false;
                        }
                        self.loading = true;
                        let param = {limit:self.page_data.limit,page:self.page_data.page};
                        param.fl_userid = self.fl_userid;
                        Fast.api.ajax({
                            url: 'fastscrm/template/chat',
                            data:param,
                            type: 'get',
                        }, function (data, res) {
                            self.tableData = res.data.rows;
                            self.page_data.total = res.data.total;
                            self.loading = false;
                            return false;
                        },  function (ret, res) {
                            self.loading = false;
                        });
                    },
                    toggleSelection(rows) {
                        if (rows) {
                            rows.forEach(row => {
                                this.$refs.multipleTable.toggleRowSelection(row,true);
                            });
                        } else {
                            this.$refs.multipleTable.clearSelection();
                        }
                    },
                    handleSelectionChange(val) {
                        this.multipleSelection = val;
                    },
                    handleSizeChange(val) {
                        this.page_data.limit = val;
                        this.getData();
                    },
                    handleCurrentChange(val) {
                        this.page_data.page = val;
                        this.getData();
                    },
                    backData(){
                        Fast.api.close(this.multipleSelection);
                    },
                    backAllData(){
                        var self = this;
                        let param = {limit:self.page_data.total,page:self.page_data.page};
                        param.tags = self.tags;
                        param.fl_userid = self.fl_userid;
                        Fast.api.ajax({
                            url: 'fastscrm/template/chat',
                            data:param,
                            type: 'get',
                        }, function (data, res) {
                            Fast.api.close(res.data.rows);
                            return false;
                        },  function (ret, res) {
                        });

                    }
                }
            });
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
        }
    };
    return Controller;
});
