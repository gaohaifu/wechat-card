document.addEventListener('DOMContentLoaded', function() {
    // 这里放置你的代码
    console.log('DOM已加载完成');
    const ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == 'micromessenger') {
        wx.miniProgram.getEnv((res) => {
            if (res.miniprogram) {
                console.log('在小程序内');
                document.querySelector('.top').style.display = 'block';
            } else {
                console.log('不在小程序内');
                document.querySelector('.top').style.display = 'none';
            }
        });
    } else {
        console.log('不在微信浏览器内');
        document.querySelector('.top').style.display = 'none';
    }
});


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