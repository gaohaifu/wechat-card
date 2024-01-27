<template>
  <view class="imglistbx">
    <!-- <view :class="['imglistItem',columnNum==3?'column3':'column4']" v-for="(item,index) in showList" :key='index'>
      <image :src="item" class="itemImg" @click="previewImage(index)" mode="aspectFill"></image>
      <icon size="18" type="cancel" class="cancelBtn" @click="deleteImg(index)" v-if="deleteBtn"></icon>
    </view> -->
		<block v-if="type=='bill'">
			<view class="bill_img" @click="uploadImg('')">
				<view class="bill_content">
					<block v-if="imgList.length>0">
						<view class="uploadimg_bill" v-for="(item,index) in imgList" :key='index'>
							<image :src="cndUrl+item" class="itemImg" mode="aspectFill"></image>
						</view>
					</block>
					<block v-else>
						<view class="bill_btn">
							<image src="../../static/images/uplode.png" mode="aspectFill"></image>
							<!-- <text>请上传照片</text>
							<view>与企业名称保持一致</view> -->
						</view>
					</block>
				</view>
			</view>
		</block>
		<block v-if="type=='card'">
			<view :class="styleType==1?'positive_negative':'identification_card'" @click="uploadImg('')">
				<view class="itemImg uploadControl">
					<block v-if="imgList.length>0">
						<view class="uploadimg_list" v-for="(item,index) in imgList" :key='index'>
							<image :src="cndUrl+item" class="itemImg" mode="aspectFill"></image>
						</view>
					</block>
					<block v-else>
						<view class="uploadImg_btn">
							<view>
							<image :src="background" mode="aspectFill"></image>
							<text v-if="describe">{{describe}}</text>
							<!-- <text>请上传照片</text>
							<view>与企业名称保持一致</view> -->
							</view>
						</view>
					</block>
				</view>
			</view>
		</block>
		<block v-else>
			<block v-if="type=='header'">
				<view class="imglistItem">
					<block v-if="imgList.length>0">
						<view class="itemImg flex_layout">
							<view class="uploadimg_list" v-for="(item,index) in imgList" :key='index'>
								<image @click="previewImage(index)" :src="cndUrl+item" class="itemImg" mode="aspectFill"></image>
								<view class="deleteIcon" @click="deleteImg(index)">X</view>
							</view>
						</view>
					</block>
					<!--合作不限制上传次数-->
					<block v-if="cooperate==1">
						<view class="uploadImg_btn uploadControl" @click="uploadImg('header')">
							<image src="../../static/images/uplode.png" mode="aspectFill"></image>
						</view>
					</block>
					<block v-else>
						<block v-if="imgList.length<maxCount">
							<view class="uploadImg_btn uploadControl" @click="uploadImg('header')">
								<image src="../../static/images/uplode.png" mode="aspectFill"></image>
							</view>
						</block>
					</block>
				</view>
			</block>
			<block v-else>
				<block v-if="type=='avatar'">
					<!-- 上传头像（不需要带cndUrl） -->
					<view class="imglistItem" @click="uploadImg('avatar')" style="border-radius: 50%;">
					  <view class="itemImg uploadControl" style="border-radius: 50%;">
							<block v-if="imgList.length>0">
								<view class="uploadimg_list" v-for="(item,index) in imgList" :key='index' style="border-radius: 50%;">
								  <image :src="item" class="itemImg" mode="aspectFill"></image>
								</view>
							</block>
							<block v-else>
								<view class="uploadImg_btn">
									<image src="../../static/images/uplode.png" mode="aspectFill"></image>
								</view>
							</block>
						</view>
					</view>
				</block>
				<block v-else>
					<!-- 上传控件 -->
					<view class="imglistItem commonStyle" @click="uploadImg('')">
					  <view class="itemImg uploadControl">
							<block v-if="imgList.length>0">
								<view class="uploadimg_list" v-for="(item,index) in imgList" :key='index'>
								  <image :src="cndUrl+item" class="itemImg" mode="aspectFill"></image>
								</view>
							</block>
							<block v-else>
								<view class="uploadImg_btn">
									<image src="../../static/images/uplode.png" mode="aspectFill"></image>
								</view>
							</block>
						</view>
					</view>
				</block>
			</block>
			
		</block>
    
    <!-- <view class="clear"></view> -->
  </view>
</template>

<script>
	import {
		  cdnUrl,
			baseUrl,
			baseApiUrl
		} from '../../config/config.js';
  export default {
    props: {
      //是否显示上传控件
      control: {
        type: Boolean,
        default: true
      },
	  cooperate: {
        type: [Number, String],
        default: 0
      },
      //是否显示上删除按钮
      deleteBtn: {
        type: Boolean,
        default: true
      },
      //行数量 
      columnNum: {
        type: [Number, String],
        default: 4
      },
      //album 从相册选图，camera 使用相机
      sourceType: {
        type: Array,
        default: function() {
          return ['camera', 'album']
        }
      },
			background: {
				type:String,
				default: ''
			},
			describe: {
				type:String,
				default: ''
			},
			styleType: {
				type:String,
				default: '0'
			},
      //最大上传数量
      maxCount: {
        type: [Number, String],
        default: 5
      },
			//上传数量
			lookNum:{
				type: [Number, String],
				default: 3
			},
      //服务返回回调的图片数组--回填
      mode: {
        type: Array,
        default: function() {
          return []
        }
      },
			type: {
				type:String,
				default: ''
			}
    },
    data() {
      return {
		cndUrl:cdnUrl,
        imgList: [],
        showList: [],
        showUploadControl: true,
				uploadNum:this.maxCount
      }
    },
    watch: {
      mode(v) {
        this.init(v)
      },
      control(v) {
        this.showUploadControl = v
      }
    },
    created() {
			// #ifdef H5
			setTimeout(()=>{
				this.init(this.mode)
			},1500)
			// #endif
    },
    methods: {
      init(v) {
				if (this.mode.length != 0) {
					this.imgList = v;
					if(this.type=='header'){
						this.uploadNum = this.maxCount-v.length
					}else{
						this.uploadNum = this.maxCount
					}
					return
				}else{
					this.uploadNum = this.maxCount
				}
				console.log('this.uploadNum',this.uploadNum)
        //this.showList = this.imgList;
      },
      // 上传图片
      uploadImg(type) {
				let userToken = '';
				let auth = this.$db.get("auth");
				userToken = auth.token;
        uni.chooseImage({
          sizeType: ['compressed'],
          sourceType: this.sourceType,
          count: this.type=='avatar'?1:this.uploadNum,
          success: (res) => {
						console.log(res);
						const tempFilePaths = res.tempFilePaths;
						// this.imgList=tempFilePaths.map(item=>{
						// 	return item
						// })
						//this.$emit("chooseFile", this.imgList, tempFilePaths[0]) 
						for(var i=0; i<tempFilePaths.length; i++){
							uni.uploadFile({
								url: baseApiUrl + 'common/upload?token='+userToken,
								filePath: tempFilePaths[i],
								fileType: 'image',
								name: 'file',
								headers: {
									'Accept': 'application/json',
									'Content-Type': 'multipart/form-data',
									'token': userToken
								},
								formData: {},
								success: (uploadFileRes) => {
									var dataimg=JSON.parse(uploadFileRes.data);
									if(type=='header'){
										this.imgList.push(dataimg.data.url);
										this.uploadNum=this.maxCount-this.imgList.length
									}else if(type=='avatar'){
										this.imgList=[]
										//上传头像
										this.imgList.push(dataimg.data.fullurl);
									}else{
										this.imgList=[]
										this.imgList.push(dataimg.data.url);
									}
									this.$emit("chooseFile", this.imgList, tempFilePaths) 
								},
								fail: (error) => {
									if (error && error.response) {
										this.$common.showError(error.response);
									}
								},
								complete: () => {
									setTimeout(function () {
										uni.hideLoading();
									}, 250);
								},
							});
							//
						}
						
          }
        });
      },
      //删除图片
      deleteImg(eq) {
        let getUrl = this.handleImg();
        uni.showModal({
          title: '提示',
          content: '您确定删除吗？',
          success: (res)=> {
            if (res.confirm) {
             getUrl.splice(eq, 1);
             this.$emit("imgDelete", getUrl, eq);
             //this.isMaxNum();
            }
          }
        });
      },
      // 预览图片
      previewImage(eq) {
        let getUrl = this.handleImg().map(item=>{
					return this.cndUrl+item
				});
        uni.previewImage({
          current: getUrl[eq],
          urls: getUrl
        })
      },
      //返回需要操作的图片数组
      //如果是回调了则操作回填后的数组 否则操作临时路径的图片数组
      handleImg() {
        return this.imgList
      },
      //判断图片数量是否已经到最大数量
      isMaxNum() {
        if (this.imgList.length >= this.maxCount) {
          this.showUploadControl = false
        } else {
          this.showUploadControl = true
        }
      }
    }
  }
</script>

<style scoped>
  /* 上传  str */
  .imglistbx {
		border-radius: 10px;
		/* box-shadow: 0 0 10px #ccc; */
  }
	.identification_card{position: relative;
    width: 240rpx;
    height: 150rpx;
		background: #f6f6f6;}
	.positive_negative{width: 300rpx;height: 200rpx;background: #f6f6f6;border-radius: 10px;overflow: hidden;}
  .imglistItem {
    position: relative;
		display: flex;
		align-items: center;
		flex-wrap: wrap;
    /* width: 120rpx;
    height: 120rpx;
		background: #f6f6f6; */
  }

  .column3 {
    width: 33.3333%;
    height: 160rpx;
  }

  .column4 {
    width: 25%;
    height: 130rpx;
  }

  .itemImg {
		border-radius: 5px;
    display: block;
    border-radius: 10rpx;
		display: flex;
		align-items: center;
  }

  .cancelBtn {
    position: absolute;
    top: -10rpx;
    right: 10rpx;
  }

  /* 上传控件 */
  .uploadControl {
    font-size: 50rpx;
    color: #888;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  /*  上传  str end*/
  .clear {
    clear: both;
  }
	
	.uploadImg_btn{width: 120rpx; height: 120rpx; background: #f6f6f6; border-radius: 5px; display: flex; align-items: center; justify-content: center;flex-wrap: wrap;}
	.identification_card .uploadImg_btn{width: 240rpx;height: 150rpx;}
	.identification_card .uploadImg_btn image{width: 240rpx; height: 150rpx; display: block; margin: 0 auto;}
	.positive_negative .uploadImg_btn{width: 300rpx;height: 200rpx;}
	.positive_negative .uploadImg_btn image{width: 50rpx;height: 50rpx;}
	.positive_negative .uploadImg_btn text{display: block;margin-top: 20rpx;text-align: center;color: #999;font-size: 30rpx;width: 100%;}
	.uploadImg_btn image{width: 36rpx; height: 36rpx; display: block; margin: 0 auto;}
	.uploadImg_btn text{display: block; color: #333; font-size: 30rpx; margin-top: 20rpx;}
	.uploadImg_btn view{display: block; color: #999; font-size: 24rpx; margin-top: 20rpx;}
	.uploadimg_list{width: 120rpx; height: 120rpx;margin: 10rpx;position: relative;}
	.uploadimg_list image{width: 100%; height: 100%;}
	.deleteIcon{position: absolute; right: -15rpx; top: -15rpx; background: #ccc; width: 30rpx; height: 30rpx; border-radius: 30rpx; font-size: 20rpx; text-align: center; line-height: 30rpx; z-index: 10; color: #999;}
	
	.identification_card .uploadimg_list{width: 100%; height: 100%;}
	.identification_card .itemImg{width: 100%; height: 100%;}
	.identification_card .uploadImg_btn{width: auto; height: auto; display: block;}
	.positive_negative .uploadimg_list{width: 100%; height: 100%;margin: 0;}
	.positive_negative .itemImg{width: 100%; height: 100%;}
	.commonStyle .itemImg{margin: 0;}
	.bill_btn{width: 180px; height: 130px; background: #f6f6f6; border-radius: 5px; display: flex; align-items: center; justify-content: center;}
	.bill_btn image{width: 36rpx; height: 36rpx; display: block; margin: 0 auto;}
	.uploadimg_bill{width: 180px; height: 130px;}
</style>
