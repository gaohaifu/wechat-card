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

