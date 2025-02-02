// JavaScript Document
/**
 * 自定义多列时间选择器
 */
function withData(param) {
  return param < 10 ? '0' + param : '' + param;
}
function getLoopArray(start, end) {
  var start = start || 0;
  var end = end || 1;
  var array = [];
  for (var i = start; i <= end; i++) {
    array.push(withData(i));
  }
  return array;
}
function getMonthDay(year, month) {
  var flag = year % 400 == 0 || (year % 4 == 0 && year % 100 != 0), array = null;
 
  switch (month) {
    case '01':
    case '03':
    case '05':
    case '07':
    case '08':
    case '10':
    case '12':
      array = getLoopArray(1, 31)
      break;
    case '04':
    case '06':
    case '09':
    case '11':
      array = getLoopArray(1, 30)
      break;
    case '02':
      array = flag ? getLoopArray(1, 29) : getLoopArray(1, 28)
      break;
    default:
      array = '月份格式不正确，请重新输入！'
  }
  return array;
}
function getNewDateArry() {
  // 当前时间的处理
  var newDate = new Date();
  var year = withData(newDate.getFullYear()),
    mont = withData(newDate.getMonth() + 1),
    date = withData(newDate.getDate()),
    hour = withData(newDate.getHours()),
    minu = withData(newDate.getMinutes()),
    seco = withData(newDate.getSeconds());
 
  return [year, mont, date, hour, minu, seco];
}
function dateTimePicker(startYear, endYear, date) {
  // 返回默认显示的数组和联动数组的声明
  var dateTime = [], dateTimeArray = [[], [], [], []];
  var start = startYear || 2020;
  var end = endYear || 2100;
  // 默认开始显示数据
  var defaultDate = date ? [...date.split(' ')[0].split('-'), ...date.split(' ')[1].split(':')] : getNewDateArry();
	console.log(defaultDate);
  //console.log("defaultDate: ",defaultDate);
  // 处理联动列表数据
  /*年月日 时分秒*/
  dateTimeArray[0] = getLoopArray(start, end);
  dateTimeArray[1] = getLoopArray(1, 12);
  dateTimeArray[2] = getMonthDay(defaultDate[0], defaultDate[1]);
  //dateTimeArray[3] = getLoopArray(0, 23);
	dateTimeArray[3] =['9时','11时','14时','16时']
  //dateTimeArray[4] = getLoopArray(0, 59);
  //dateTimeArray[5] = getLoopArray(0, 59);
 // console.log(dateTimeArray[0]);
 // console.log(dateTimeArray[1]);
 // console.log(dateTimeArray[2]);
 
  dateTimeArray.forEach((current, index) => {
    dateTime.push(current.indexOf(defaultDate[index]));
  });
  //处理数据加上年月日时的单位
  for(var i=0; i<dateTimeArray[0].length;i++){
  	dateTimeArray[0][i]= dateTimeArray[0][i]+'年';
  }
  for(var j=0; j<dateTimeArray[1].length;j++){
  	dateTimeArray[1][j]= dateTimeArray[1][j]+'月';
  }
  for(var k=0; k<dateTimeArray[2].length;k++){
  	dateTimeArray[2][k]= dateTimeArray[2][k]+'日';
  }
  //
  dateTime[3]=0
  //console.log(dateTime);
  return {
    dateTimeArray: dateTimeArray,
    dateTime: dateTime
  }
}
module.exports = {
  dateTimePicker: dateTimePicker,
  getMonthDay: getMonthDay
}