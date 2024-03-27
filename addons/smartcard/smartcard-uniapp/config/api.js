import {
	baseUrl,
	baseApiUrl
} from './config.js';
import * as common from './common.js' //引入common
import * as db from './db.js' //引入common
// 需要登陆的，都写到这里，否则就是不需要登陆的接口
const methodsToken = [
	"favorOptionData",
	"tagsData",
	"companyInfo",
	"tagsFavorOptionData",
	"tagAddData",
	"tagDelData",
	"visitorsOptionData",
	"addGoods",
	"editGoods",
	"findGoods",
	"addNews",
	"editNews",
	"findNews",
	"addCases",
	"editCases",
	"findCases",
	"getMyInfo",
	"userlist",
	"stafffind",
	"staffEdit",
	"messageList",
	"messageEdit",
	'applyStaffAdd',
	"refreshUser",
	"smartcardfind",
	"themeList",
	"themeEdit",
	"companyStaffAdd",
	'companyStaffEdit',
	'logout',
	// ========================new apis
	'index',
	'indexShare',
	'industryCategoryList',
	'cardHolder',
	'agreeExchange',
	'myCardVisit',
	'myCardList',
	'myCardSearch',
	'resendCard',
	'myCompany',
	'getMemberList',
	'enterpriseCertified',
	'realnameCertified',
	'sendCard',
	'saveCard',
	'shareCardInfo',
	'saveCustomGreetings',
	'saveShareInfo'
];
const post = (method, data, callback,type, failCB) => {
	let userToken = '';
	let auth = '';
	// 判断token是否存在
	if (methodsToken.indexOf(method) >= 0) {
		// 获取用户token
		let auth = db.get("auth");
		console.info(auth, '<=====auth====method====>', method, '====>user', db.get("user"));
		let nowdate = (new Date()) / 1000; //当前时间戳
		//新增用户判断是否登录逻辑begin
		common.isLogin()
		// if (!common.isLogin()) {
		// 	return;
		// }
		//新增用户判断是否登录逻辑end
		//console.log('auth',auth);
		// if (!auth || auth.createtime+auth.expires_in < nowdate) {
		// 	common.toLogin();
		// 	return false;
		// } else {
		// 	userToken = auth.token;
		// }
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
							url: '/pages/index/index',
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
			failCB && typeof failCB === 'function' && failCB(error)
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
		sizeType: ['compressed'],
		sourceType: ['camera', 'album'],
		success: (res) => {
			uni.showLoading({
				title: '上传中...'
			});
			let tempFilePaths = res.tempFilePaths
			for (var i = 0; i < tempFilePaths.length; i++) {
				data.file = tempFilePaths[i]
				uni.uploadFile({
					url: baseApiUrl + method + '?token=' + userToken,
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
//注
export const register = (data, callback) => post('register', data, callback,'smartcard/User');
//注册所需公司姓名
export const companylist = (data, callback) => post('companylist', data, callback,'smartcard/User');
//判断是否登录
export const refreshUser = (data, callback) => post('refreshUser', data, callback,'smartcard/User');
// 登录
export const third = (data, callback) => post('third', data, callback,'smartcard/User');
// 登录
export const login = (data, callback) => post('login', data, callback,'smartcard/User');
// 登录
export const logout = (data, callback) => post('logout', data, callback,'smartcard/User');
//获取验证码
export const sendSmsVerify = (data, callback) => post('sendSmsVerify', data, callback,'smartcard/User');
//个信息
export const getMyInfo = (data, callback) => post('getMyInfo', data, callback,'smartcard/User');
//编辑员工
export const companyStaffEdit = (data, callback) => post('companyStaffEdit', data, callback,'smartcard/Common');
 //smartcard开头的表，查看字段说明
 /**
* 获取首页数据，员工数据+其他展示数据
* @param string $staff_id     员工id
**/
// $data['staffInfo']=$staffInfo;//员工基本信息
// $data['visitStaffNum']=$visitStaffNum;//访问员工主页数量
// $data['visitStaffLists']=$visitStaffLists;//访问员工主页人员的记录信息，最多10条返回
// $data['visitCompanyNum']=$visitCompanyNum;//访问公司主页人数
// $data['visitCompanyLists']=$visitCompanyLists;//访问公司主页人员信息列表
// $data['visitDesignNum']=$visitDesignNum;//访问公司宣传册数量
// $data['visitDesignLists']=$visitDesignLists;//访问公司宣传册人员记录
// $data['visitCasesNum']=$visitCasesNum;//访问公司案例人数
// $data['visitCasesLists']=$visitCasesLists;//访问公司案例人员列表
// $data['visitGoodsNum']=$visitGoodsNum;//访问公司产品人员数量
// $data['visitGoodsLists']=$visitGoodsLists;//访问公司产品数量人员列表
// $data['visitCompanyNewsNum']=$visitCompanyNum;//访问公司动态数量
// $data['visitCompanyNewsLists']=$visitCompanyLists;//访问公司动态人员列表
// $data['favorStaffNum']=$visitStaffNum;//点赞员工的数量
// $data['favorStaffLists']=$favorStaffLists;//点赞员工的人员列表（最多10个）
export const indexData = (data, callback) => post('index', data, callback,'smartcard/Common');
 /**
* 获取员工基本数据
* @param string $staff_id     员工id
*/
export const staffInfoData = (data, callback) => post('staffInfo', data, callback,'smartcard/Common');
/**
* 获取公司基本数据 以及各个模块的信息
* @param string $company_id     员工id
* @param string $type     type值为design 宣传册列表数据| goods 企业商品列表数据 | news 公司新闻数据 | staff 公司员工列表数据
* @param string $page     页数
* @param string $limit     每页数量
*/
export const companyInfoData = (data, callback) => post('companyInfo', data, callback,'smartcard/Common');
/**
* 首页点赞和取消
* @param string staff_id     员工id
* 需要登陆操作
*/
export const favorOptionData = (data, callback) => post('favorOptionData', data, callback,'smartcard/Common');
/**
* 员工页面标签数据
* @param string $staff_id     员工id
*/
export const tagsData = (data, callback) => post('tagsData', data, callback,'smartcard/Common');
/**
* 员工页面标签点赞
* @param string $staff_id     员工id
* @param string $tags_id     标签id
*/
export const tagsFavorOptionData = (data, callback) => post('tagsFavorOptionData', data, callback,'smartcard/Common');
/**
* 新增标签
* @param string $staff_id     员工id
* @param string $tags_name     标签名称
*/
export const tagAddData = (data, callback) => post('tagAddData', data, callback,'smartcard/Common');
/**
* 删除标签
* @param string $tags_id     标签id
*/
export const tagDelData = (data, callback) => post('tagDelData', data, callback,'smartcard/Common');
/**
* 公司基本信息
* @param string cid     公司id
*/
export const mycompanyInfoData = (data, callback) => post('myCompanyInfo', data, callback,'smartcard/Common');
/**
* 企业动态点赞
* @param string $staff_id     员工id
* @param string $typedata     动态点赞传 8
* @param string $company_id     公司id
*/
export const visitorsOptionData = (data, callback) => post('visitorsOptionData', data, callback,'smartcard/Common');
/**
* 获取公司基本数据 以及各个模块的信息
* @param string $company_id     员工id
* @param string $id      具体id的数据
* @param string $type      type值为design 宣传册列表数据| goods 企业商品列表数据 | news 公司新闻数据 | staff 公司员工列表数据 | cases 案例
*/
export const departInfo = (data, callback) => post('departInfo', data, callback,'smartcard/Common');
//新增商品
export const addGoods = (data, callback) => post('addGoods', data, callback,'smartcard/Common');
//编辑商品
export const editGoods = (data, callback) => post('editGoods', data, callback,'smartcard/Common');
//商品商品
export const findGoods = (data, callback) => post('findGoods', data, callback,'smartcard/Common');
   
//新增动态
export const addNews = (data, callback) => post('addNews', data, callback,'smartcard/Common');
//编辑动态
export const editNews = (data, callback) => post('editNews', data, callback,'smartcard/Common');
//商品动态
export const findNews = (data, callback) => post('findNews', data, callback,'smartcard/Common'); 
//新增案例
export const addCases = (data, callback) => post('addCases', data, callback,'smartcard/Common');
//编辑案例
export const editCases = (data, callback) => post('editCases', data, callback,'smartcard/Common');
//新增员工
export const companyStaffAdd = (data, callback) => post('companyStaffAdd', data, callback,'smartcard/Common');
//商品案例
export const findCases = (data, callback) => post('findCases', data, callback,'smartcard/Common');
//用户列表
export const userlist = (data, callback) => post('userlist', data, callback,'smartcard/Common');
//用户信息
export const stafffind = (data, callback) => post('stafffind', data, callback,'smartcard/Common');
//编辑个人信息
export const staffEdit = (data, callback) => post('staffEdit', data, callback,'smartcard/Common');
//个人信息
export const smartcardfind = (data, callback) => post('smartcardfind', data, callback,'smartcard/Common');
//消息列表
export const messageList = (data, callback) => post('messageList', data, callback,'smartcard/Common');
//同意或拒绝消息
export const messageEdit = (data, callback) => post('messageEdit', data, callback,'smartcard/Common');
export const themeList = (data, callback) => post('themeList', data, callback,'smartcard/Common');
//选择模板
export const themeEdit = (data, callback) => post('themeEdit', data, callback,'smartcard/Common');
//新增用户信息
export const applyStaffAdd = (data, callback) => post('applyStaffAdd', data, callback,'smartcard/Common');



// ==============================New Apis========================================================================
// 首页
export const doIndex = (data, callback) => post('index', data, callback,'smartcard/Common');
// 首页 - 分享
export const doIndexShare = (data, callback) => post('indexShare', data, callback,'smartcard/Common');
// 获取行业列表
export const industryCategoryList = (data, callback) => post('industryCategoryList', data, callback,'smartcard/Common');
// 获取名片夹
export const cardHolder = (data, callback) => post('cardHolder', data, callback,'smartcard/Common');
// 同意交换名片
export const agreeExchange = (data, callback) => post('agreeExchange', data, callback,'smartcard/Common');
// 我的名片数据--汇总
export const myCardVisit = (data, callback) => post('myCardVisit', data, callback,'smartcard/Common');
// 我的名片数据列表
export const myCardList = (data, callback, failCB) => post('myCardList', data, callback,'smartcard/Common', failCB);
// 名片夹--搜索
export const myCardSearch = (data, callback) => post('myCardSearch', data, callback,'smartcard/Common');
// 回递名片
export const resendCard = (data, callback) => post('resendCard', data, callback,'smartcard/Common');
// 模板列表
// export const themeList1 = (data, callback) => post('resendCard', data, callback,'smartcard/Common');
// 企业名片
export const myCompany = (data, callback) => post('myCompany', data, callback,'smartcard/Common');
// 成员列表
export const getMemberList = (data, callback, failCB) => post('getMemberList', data, callback, 'smartcard/Common', failCB)
// 企业认证
export const enterpriseCertified = (data, callback) => post('enterpriseCertified', data, callback, 'smartcard/Common')
// 实名认证
export const realnameCertified = (data, callback) => post('realnameCertified', data, callback, 'smartcard/Common')
// 保存名片
export const saveCard = (data, callback) => post('saveCard', data, callback, 'smartcard/Common')
// 发名片回调
export const sendCard = (data, callback) => post('sendCard', data, callback, 'smartcard/Common')
// 分享卡片信息
export const shareCardInfo = (data, callback) => post('shareCardInfo', data, callback, 'smartcard/Common')
// 保存自定义招呼语
export const saveCustomGreetings = (data, callback) => post('saveCustomGreetings', data, callback, 'smartcard/Common')
// 保存分享信息
export const saveShareInfo = (data, callback) => post('saveShareInfo', data, callback, 'smartcard/Common')
