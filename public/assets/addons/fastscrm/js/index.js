var app = new Vue({
        el: '#indexPage',
        data: {
            worker: {},
            ranks: {},
            loading: false,
            show:true,
            active: 0,
        },
        mounted: function(){
            this.getData();
        },
        methods: {
            getData() {
                var self=this;
                self.loading=true;
                axios.post('addons/fastscrm/index/getdata',{
                }).then(function (response) {
                    self.worker = response.data.worker;
                    self.ranks = response.data.ranks;
                    self.loading=false;
                    self.show=true;
                }).catch(function (error) {
                    self.show=true;
                });
            },
        },
    });
