import {
	baseUrl,
	baseApiUrl
} from './config.js';
import * as common from './common.js' //引入common
import * as db from './db.js' //引入common
// 需要登陆的，都写到这里，否则就是不需要登陆的接口
const methodsToken = [
	'doIndex',
	'doIndexShare',
	'industryCategoryList'
];
const post = (method, data, callback,type) => {
	let userToken = '';
	let auth = '';
	// 判断token是否存在
	if (methodsToken.indexOf(method) >= 0) {
		// 获取用户token
		let auth = db.get("auth");
		let nowdate = (new Date()) / 1000; //当前时间戳
		//新增用户判断是否登录逻辑begin
		 common.isLogin();
		userToken = auth.token?auth.token:'';
	}
	
	if(type){
		method =  type + '/' + method
	}else{
		method = '/' + method
	}
    uni.showLoading({title:'努力加载中',icon:'loading'});
	uni.request({
		url: baseApiUrl + method,
		data: data,
		header: {
			'Accept': 'application/json',
			'Content-Type': 'application/x-www-form-urlencoded',
			'token': userToken,
		},
		method: 'POST',
		success: (response) => {
			uni.hideLoading();
			const result = response.data
			if (result.msg == 'Please login' || result.msg == '请登陆') {
				db.del("user");
				db.del("auth");
				console.log('未登陆')
				uni.showToast({
					title: result.msg,
					icon: 'none',
					duration: 2000,
					complete: function() {
						uni.reLaunch({
							url: '/pages/myCard/myCard',
						})
					}
				});
			}
			callback(result);
		},
		fail: (error) => {
			uni.hideLoading();
			if (error && error.response) {
				showError(error.response);
			}
		},
	});
}

// 上传图片
export const uploadImage = (method , data = {} , callback , num = 9 ,type) => {
	if(type){
		method =  type + '/' + method
	}else{
		method =method
	}
	let userToken = '';
	let auth = db.get("auth");
	userToken = auth.token;
	uni.chooseImage({
		count:num,
		success: (res) => {
			uni.showLoading({
				title: '上传中...'
			});
			let tempFilePaths = res.tempFilePaths
			for (var i = 0; i < tempFilePaths.length; i++) {
				data.file = tempFilePaths[i]
				uni.uploadFile({
					url: baseApiUrl + method,
					filePath: tempFilePaths[i],
					fileType: 'image',
					name: 'file',
					headers: {
						'Accept': 'application/json',
						'Content-Type': 'multipart/form-data',
						'token': userToken
					},
					formData: data,
					success: (uploadFileRes) => {
						callback(JSON.parse(uploadFileRes.data))
					},
					fail: (error) => {
						if (error && error.response) {
							common.showError(error.response);
						}
					},
					complete: () => {
						setTimeout(function () {
							uni.hideLoading();
						}, 250);
					},
				});
			}
		}
	});
}

const get = (url, callback) => {
	uni.showLoading({
		title: '加载中'
	});
	uni.request({
		url: url,
		header: {
			'Accept': 'application/json',
			'Content-Type': 'application/x-www-form-urlencoded', //自定义请求头信息
		},
		method: 'GET',
		success: (response) => {
			callback(response.data);
		},
		fail: (error) => {
			if (error && error.response) {
				showError(error.response);
			}
		},
		complete: () => {
			setTimeout(function() {
				uni.hideLoading();
			}, 250);
		}
	});
}

const showError = error => {
	let errorMsg = ''
	switch (error.status) {
		case 400:
			errorMsg = '请求参数错误'
			break
		case 401:
			errorMsg = '未授权，请登录'
			break
		case 403:
			errorMsg = '跨域拒绝访问'
			break
		case 404:
			errorMsg = `请求地址出错: ${error.config.url}`
			break
		case 408:
			errorMsg = '请求超时'
			break
		case 500:
			errorMsg = '服务器内部错误'
			break
		case 501:
			errorMsg = '服务未实现'
			break
		case 502:
			errorMsg = '网关错误'
			break
		case 503:
			errorMsg = '服务不可用'
			break
		case 504:
			errorMsg = '网关超时'
			break
		case 505:
			errorMsg = 'HTTP版本不受支持'
			break
		default:
			errorMsg = error.msg
			break
	}
	uni.showToast({
		title: errorMsg,
		icon: 'none',
		duration: 2000
	});
}
// 首页
export const doIndex = (data, callback) => post('index', data, callback,'smartcard/Common');
// 首页 - 分享
export const doIndexShare = (data, callback) => post('indexShare', data, callback,'smartcard/Common');
// 加入企业 - 编辑个人资料
export const applyStaffAdd = (data, callback) => post('applyStaffAdd', data, callback,'smartcard/Common');
// 获取行业列表
export const industryCategoryList = (data, callback) => post('industryCategoryList', data, callback,'smartcard/Common');