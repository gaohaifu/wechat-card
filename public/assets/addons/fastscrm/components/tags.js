Vue.component('wlk-tags',{
    template:'#tags',
    data: function () {
        return {
            loading:false,
            list:{},
            tags:[],
            show:false
        }
    },
    mounted: function(){
        this.getData();
    },
    methods: {
        getData() {
            var self=this;
            var ids = '';
            self.loading=true;
            $.each(self.tags,function (index,value) {
                ids+=value.id+',';
            });
            ids=ids.substring(0,ids.length-1);
            axios.post('addons/fastscrm/Components/getdata',{
                ids:ids
            }).then(function (response) {
                self.list = response.data.rows;
                self.loading = false;
            }).catch(function (error) {
                self.loading = false;
            });
        },
        chose(index,key){
            this.list[index].tags[key].active = !this.list[index].tags[key].active;
            let object = {};
            object.id = this.list[index].tags[key].id;
            object.name = this.list[index].tags[key].name;
            object.tagid = this.list[index].tags[key].tag_id;
            let k = this.tags.findIndex((item) => item.id === object.id);
            if(k==-1){
                this.tags.push(object);
            }else{
                this.tags.splice(k,1);
            }
        },
        clear() {
            this.tags = [];
            $.each(this.list, function (k,v) {
                $.each(v.tags, function (i,t) {
                    t.active = false;
                });
            });
        },
        change(){
            this.show = !this.show;
        },
        cancel(){
            this.show = false;
        },
        confirm(){
            this.show = false;
            this.$parent.returnTags(this.tags);
        },
    }
})