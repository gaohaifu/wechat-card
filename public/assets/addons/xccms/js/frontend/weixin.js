const ua = navigator.userAgent.toLowerCase();
if (ua.match(/MicroMessenger/i) == 'micromessenger') {
    wx.miniProgram.getEnv((res) => {
        if (res.miniprogram) {
            console.log('在小程序内');
            $('.top').show();
        } else {
            console.log('不在小程序内');
            $('.top').hide();
        }
    });
} else {
    console.log('不在微信浏览器内');
    $('.top').hide();
}
function gotIndex(){
    // 小程序跳转方法
    wx.miniProgram.navigateTo({
        url:'pages/myCard/myCard',        // 指定跳转至小程序页面的路径
        success: (res) => {
            console.log(res);   // 页面跳转成功的回调函数
        },
        fail: (err) => {
            console.log(err);   // 页面跳转失败的回调函数
        }
    });
}