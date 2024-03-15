import * as db from './db.js' //引入common
import * as api from './api.js'
import {
	baseUrl,
	cdnUrl,
	bgClass,
	title,
	baseLogo
} from './config.js'
import htmlParser from '@/common/html-parser' //引入htmlParser

// 小程序要用到各种背景图
export const smartcardBG = {
	backgroundImg: cdnUrl + 'uploads/20240313/0c0ba632ab08bb4e34b3643267c13dca.png', // 自定义头部背景
	cardimage: cdnUrl + 'uploads/20240313/2ef527af6aaef43fb534d081ff048a60.png', // 用户个人信息面板背景
	cert: cdnUrl + 'uploads/20240313/6931c2d991a68fb9c68dc3c4ca1ce057.png', // 认证背景
	unCert: cdnUrl + 'uploads/20240313/8698ab6a90ba338e35481ea3fd87bc3a.png', // 未认证状态背景图
}

/**
 * 跳转登陆页面
 */
function toLogin() {
	uni.showToast({
		title: '请登录...',
		icon: 'loading',
		duration: 2000,
		success: function(res) {
			// #ifdef H5 || APP-PLUS
			uni.navigateTo({
				url: '/pages/user/login'
			})
			// #endif
			// #ifdef MP
			uni.navigateTo({
				url: '/pages/user/login',
				animationType: 'pop-in',
				animationDuration: 200
			});
			// #endif
		}
	})
}

/**
 * 是否登陆，和绑定手机号，否则返回登录页
 */
function isLogin() {
	let user = db.get('user');
	//用户存在，不跳转，不存在直接跳转
	if(user){
		if(user.id){
			// if(user.mobile==''){
			// 	uni.navigateTo({
			// 		url: '/pages/user/bind'
			// 	})
			// }	
			
	}else{
		console.log("user: ",user);
		db.del('user');
		db.del('auth');
		toLogin()
	}
  }
}
/**
 * 无图标提示
 * @param {String} msg 提示消息
 * @param {Function} callback 回调函数
 */

function normalToShow(msg = '保存成功', callback = function() {}) {
	uni.showToast({
		title: msg,
		icon: 'none',
		duration: 2000,
	});
	setTimeout(function() {
		callback();
	}, 500);
}
/**
 * 成功提示
 * @param {String} msg 提示消息
 * @param {Function} callback 回调函数
 */

function successToShow(msg = '保存成功', callback = function() {}) {
	uni.showToast({
		title: msg,
		icon: 'success',
		duration: 2000,
	});
	setTimeout(function() {
		callback();
	}, 500);
}

/**
 * 失败提示
 * @param {String} msg 提示消息
 * @param {Function} callback 回调函数
 */

function errorToShow(msg = '操作失败', callback = function() {}) {
	uni.showToast({
		title: msg,
		icon: 'none',
		duration: 2000,
	});
	setTimeout(function() {
		callback();
	}, 2000);
}

/**
 * 加载显示
 * @param {String} msg 提示消息
 */
function loadToShow(msg = '加载中',icon='loading') {
	uni.showToast({
		title: msg,
		icon: icon,
	});
}

/**
 * 加载隐藏
 */

function loadToHide() {
	uni.hideToast();
}

/**
 * 提示框
 * @param {String} title 提示标题
 * @param {String} content 提示内容
 * @param {Function} callback 回调函数
 * @param {Boolean} showCancel = [true|false] 显示关闭按钮
 * @param {String} cancelText 关闭按钮文字 
 * @param {String} confirmText 确定按钮文字 
 * @example
 * modelShow('提示','确认执行此操作吗?',()=>{},true,'取消','确定')
 */
function modelShow(title = '提示', content = '确认执行此操作吗?', callback = () => {},callback2 = () => {}, showCancel = true, cancelText = '拒绝',
	confirmText = '通过') {
	uni.showModal({
		title: title,
		content: content,
		showCancel: showCancel,
		cancelText: cancelText,
		confirmText: confirmText,
		success: function(res) {
			if (res.confirm) {
				// 用户点击确定操作
				setTimeout(() => {
					callback()
				}, 500)
			} else if (res.cancel) {
				// 用户取消操作
				setTimeout(() => {
					callback2()
				}, 500)
			}
		}
	});
}



/**
 * 判断数组
 * @param {Object} arr 数组
 */
function isArray(object) {
	return object && typeof object === 'object'
}

/**
 * 统一跳转
 * @param {String} url 跳转链接
 */
function navigateTo(url) {
	if(url.indexOf("/pages/index/index")>=0  || url.indexOf("/pages/user/plan")>=0  || url.indexOf("/pages/user/index")>=0){
		uni.switchTab({
			url: url,
			animationType: 'pop-in',
			animationDuration: 300
		});
	}else{
		uni.navigateTo({
			url: url,
			animationType: 'pop-in',
			animationDuration: 300
		});
	}
	
}

/**
 * 关闭当前页面并跳转
 * @param {String} url 跳转链接
 */
function redirectTo(url) {
	uni.redirectTo({
		url: url,
		animationType: 'pop-in',
		animationDuration: 300
	})
}
/**
 * 返回上一层的逻辑判断
 * @param {num} delta 跳转上一层，或者几层
 */
function navigateBack(delta=1){
	let pages = getCurrentPages();//当前页
	//console.log(pages);
	//当有前一页的时候。
	if(pages.length>1){
		let page = pages[pages.length - 2];//上个页面
		console.log(page.route);
		if(page.route=="pages/index/index" || page.route=='pages/index/homepage' || page.route=='pages/user/index' ||  page.route=='pages/consult/index' ){
			uni.switchTab({
				url:'/'+page.route
			})
		}else if(page.route=="pages/user/login"){
			uni.switchTab({
				url:'/pages/user/index'
			})
		}else if(page.route=="pages/user/register"){
			uni.switchTab({
				url:'/pages/user/user'
			})
		}else{
			uni.navigateBack({
				delta:delta
			})
		}
	}else{
		uni.switchTab({
			url:'/pages/index/index'
		})
	}
	
}

/**
 *  判断是否在微信浏览器
 */
function isWeiXinBrowser() {
	// #ifdef H5
	// window.navigator.userAgent属性包含了浏览器类型、版本、操作系统类型、浏览器引擎类型等信息，这个属性可以用来判断浏览器类型
	let ua = window.navigator.userAgent.toLowerCase()
	// 通过正则表达式匹配ua中是否含有MicroMessenger字符串
	if (ua.match(/MicroMessenger/i) == 'micromessenger') {
		return true
	} else {
		return false
	}
	// #endif

	// #ifdef MP
	return false;
	// #endif
}


/**
 * 加载cms列表
 * @param {Array} data
 * 
 */

function cmsGetList(data, callback = function() {}) {
	api.cmsGetList(data, res => {
		if (res.code) {
			res.data.list.forEach(function(ele, index) {
				ele.ctime = timeToDate(ele.createtime);
				if (ele.image.substring(0, 9) == '/uploads/') {
					ele.image = cdnUrl + ele.image
				}
			});
			callback(res.data)
		} else {
			errorToShow(res.msg)
		}
	})
}


/**
 * 加载cms详情
 * @param {Array} data
 * 
 */

function cmsGetDetails(data, callback = function() {}) {
	api.cmsGetDetails(data, res => {
		//console.log(data)
		if (res.code) {
			res.data.content = htmlParser(res.data.content); //	设置资源cdn;
			res.data.ctime = timeToDate(res.data.createtime);
			if(res.data.image){
				if (res.data.image.substring(0, 9) == '/uploads/') {
					res.data.image = cdnUrl + res.data.image
				}
			}
			callback(res.data)
		} else {
			modelShow(
				'错误提示',
				res.msg,
				() => {
					uni.navigateBack({})
				},
				false
			);
		}
	})
}
/**
 * 初始化页面
 * @param {Function} callback 回调函数
 * @param {String} route	页面路径/默认当前路径
 */
function initPages(callback = function() {}, route) {
	
	if (!route) {
		let pages = getCurrentPages();
		let page = pages[pages.length - 1];
		route = page.route
	}
	let init_data = db.get('init_' + route)
	
	setTimeout(() => {
		api.getInit({
			route: route
		}, res => {
			console.log(res);
			if (res.code) {
				
				res.data.slide.forEach(function(ele, index) {
					if (ele.image.substring(0, 9) == '/uploads/') {
						ele.image = cdnUrl + ele.image
					}
				});
				res.data.content.forEach(function(ele, index) {
					ele.content = ele.content.replace(new RegExp(`style="width: 100%;`, 'g'),
						'style="width: 100%;margin-top: -3px;') //	无缝连接图片;
					ele.content = ele.content.replace(new RegExp(`src="/uploads`, 'g'),
						'src="' + cdnUrl + '/uploads')
					ele.content = htmlParser(ele.content); //	设置资源cdn;
				});
				db.set('init_' + route, res.data)
				callback(res.data)
			}
		});
	}, 1000);
	if (init_data) {
		callback(init_data)
	} else {
		// loadToShow('初始化页面...');
	}
}

/**
 * 跳转链接
 * @param {String} url 跳转链接
 */
function toUrl(url) {
	if (url) {
		
		var objExp = new RegExp(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/);
		if (objExp.test(url) != true) {
			// #ifdef APP-PLUS
			url = baseUrl + url
			// #endif
			// #ifdef H5
			url = baseUrl + url
			// #endif
		}
		//console.log(url)
		//return false
		// #ifdef MP
		uni.navigateTo({
			url: url
		})
		// #endif

		// #ifdef APP-PLUS
		plus.runtime.openURL(url);
		// #endif

		// #ifdef H5
		window.open(url);
		// #endif

	}
}


/**
 * 保存登陆状态
 * @param {Array} data 用户数据
 */
function saveLogin(data) {
	db.set('auth', data.auth)

	if (data.user.avatar.substring(0, 9) == '/uploads/') {
		data.user.avatar = cdnUrl + data.user.avatar
	}
	db.set('user', data.user)
}


/**
 * 刷新用户
 * @param {Function} callback 回调函数（用户数据）
 * @example 
 * refreshUser((user)=>{_this.user = user})
 */
function refreshUser(callback = function() {}) {
	let user = db.get('user');
	if (user.id) {
		api.refreshUser({}, res => {
			if (res.code==1) {
				saveLogin(res.data)
				callback(res.data)
			} else {
				db.del('user');
				toLogin()
			}
		})
	}else{
		db.del('user');
		db.del('auth');
		toLogin()	
	}
}



/**
 * 清理用户
 * @param {String} page 清理后跳转页面
 */
function cleanUser(page) {
	db.del('user')
	db.del('auth')
	if (page) {
		redirectTo(page)
	}
}




/**
 * 字符串校验
 * @param {String} str 字符串
 * @param {String} model = [number|mobile|name|idcard|] 模式
 * @example 
 * testString('17080057443','mobile')
 * testString('17080057443','mobile')
 */

function testString(str, model = null) {
	if (typeof(model) == 'number') {
		if (str.length >= model) {
			return true
		}
	} else {
		switch (model) {
			case null:
				return false
				break
			case 'idcard':
				return RegExp(/^[1-9]\d{5}(19|20)\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/).test(str)
				break
			case 'mobile':
				return RegExp(/^1[0-9]{10}$/).test(str)
				break
			case 'name':
				return RegExp(/^[\u4E00-\u9FA5\uf900-\ufa2d·s]{2,20}$/).test(str)
				break
			case 'mail':
				return RegExp(/^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,5}$/).test(str)
				break
			default:
				return false
				break
		}
	}
	return false
}




/**
 * 时间戳转时差
 * @param {String} date 时间戳
 */

function timeToDate(time) {
	time = time * 1000
	var interval = new Date().getTime() - time;
	//计算出相差天数
	var returnTime = "";
	var days = Math.floor(interval / (24 * 3600 * 1000))
	if (days == 0) {
		//计算出小时数
		var leaveTime = interval % (24 * 3600 * 1000) //计算天数后剩余的毫秒数
		var hours = Math.floor(leaveTime / (3600 * 1000))
		if (hours == 0) {
			//计算相差分钟数
			leaveTime = leaveTime % (3600 * 1000) //计算小时数后剩余的毫秒数
			var minutes = Math.floor(leaveTime / (60 * 1000))
			if (minutes == 0) {
				//计算相差秒数
				leaveTime = leaveTime % (60 * 1000) //计算分钟数后剩余的毫秒数
				var seconds = Math.round(leaveTime / 1000)
				return seconds + "秒前";
			}
			return minutes + "分钟前";
		}
		return hours + "小时前";
	}
	return days + "天前";
}



/**
 * 基本信息
 */
function baseInfo() {
	return {
		bgClass: bgClass,
		title: title,
		baseLogo: baseLogo,
	}
}
//校验邮箱格式
function checkEmail(email){
    return RegExp(/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/).test(email);
}
//校验手机格式
function checkMobile(mobile){
    return RegExp(/^1[0-9]{10}$/).test(mobile);
}
/**
 * 用户信息
 */
function userInfo() {
	let user = db.get('user')
	if (user) {
		return user
	} else {
		return 
	}

	// return {
	// 	bgClass: bgClass,
	// 	title: title,
	// 	baseLogo: baseLogo,
	// }
}


/**
 * 上传图片
 * @param {Array} data 附带数据
 * @param {Function} callback 回调函数
 * @param {int} num 限制数量
 * @param {String} type 类型
 * @return {String} url 
 */
function uploadImage(data = {}, callback = function() {}, num = 1, type) {
	api.uploadImage(
		'common/upload', data, (res) => {
			if (res.code) {
				if (res.data.url.substring(0, 9) == '/uploads/') {
					res.data.url = cdnUrl + res.data.url
				}
				callback(res.data.url)
			} else {
				errorToShow(res.msg)
			}
		}, type)

}




export {
	toLogin,
	isLogin,
	normalToShow,
	successToShow,
	errorToShow,
	isArray,
	loadToShow,
	loadToHide,
	navigateTo,
	navigateBack,
	redirectTo,
	modelShow,
	isWeiXinBrowser,
	initPages,
	toUrl,
	saveLogin,
	refreshUser,
	cleanUser,
	testString,
	timeToDate,
	cmsGetList,
	cmsGetDetails,
	baseInfo,
	userInfo,
	uploadImage,
	checkEmail,
	checkMobile
}
