define([], function () {
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