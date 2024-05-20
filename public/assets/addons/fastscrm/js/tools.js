var app = new Vue({
        el: '#indexPage',
        data: {
            item: {},
            loading: false,
            show:false,
            active: 1,
        },
        mounted: function(){
            this.getData();
        },
        methods: {
            getData() {
                var self=this;
                self.loading=true;
                axios.post('addons/fastscrm/tools/getdata',{
                }).then(function (response) {
                    self.item = response.data.item;
                    self.loading=false;
                    self.show=true;
                }).catch(function (error) {
                    self.show=true;
                });
            },
        },
    });
