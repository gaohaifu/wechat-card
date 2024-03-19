<template>
  <view class="imglistbx">
    <view class="imglistItem column3" v-for="(item,index) in imgList" :key='index'>
      <image :src="item" class="itemImg" @click="previewImage(index)" mode="aspectFill"></image>
      <!-- <icon size="18" type="cancel" class="cancelBtn" @click="deleteImg(index)" v-if="deleteBtn"></icon> -->
			<image class="cancelBtn" src="../../static/images/shanchu.png" @click="deleteImg(index)" v-if="showDeleteControl" mode=""></image>
    </view>
    <!-- 上传控件 -->
	<view :class="['imglistItem',columnNum==3?'column3':'column4']" @click="uploadImg" v-if="showUploadControl">
	  <view class="itemImg uploadControl"><image src="../../static/images/select.png" mode=""></image></view>
	</view>
   <!-- <view class="imglistItem column3" v-if="control" @click="uploadImg">
			<block v-if="type">
				<view class="direct"><image src="../../static/images/select.png" mode=""></image></view>
			</block>
			<block v-else>
				<view class="itemImg uploadControl"><image src="../../static/images/select.png" mode=""></image></view>
			</block>
      
    </view> -->
    <view class="clear"></view>
  </view>
</template>

<script>
	import {
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
		type: {
        type: [Number, String],
        default: 1
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
      //最大上传数量
      maxCount: {
        type: [Number, String],
        default: 9
      },
      //服务返回回调的图片数组--回填
      mode: {
        type: Array,
        default: function() {
          return []
        }
      }
    },
    data() {
      return {
        imgList: [],
        //showList: [],
        showUploadControl: true,
		showDeleteControl:true
      }
    },
    watch: {
      mode(v) {
        this.init(v)
      },
      control(v) {
        this.showUploadControl = v
      },
	  deleteBtn(v) {
		  console.log("this.showDeleteControl: ",this.showDeleteControl);
	    this.showDeleteControl = v
	  },
    },
    created() {
			
      this.init(this.mode)
	  this.initdelete(this.deleteBtn)
    },
    methods: {
      init(v) {
        if (this.mode.length != 0) {
          //this.showList = v;
		  this.imgList=v;
		  this.isMaxNum();
          return
        };
      },
	  //是否显示删除按钮
	  initdelete(v) {
		  console.log("v: ",v);
	    if (v) {
	      this.showDeleteControl =true;
	    }else{
			this.showDeleteControl =false;
		};
	  },
      // 上传图片
      uploadImg() {
				let userToken = '';
				let auth = this.$db.get("auth");
				userToken = auth.token;
        uni.chooseImage({
          //sizeType: ['compressed'],
          sourceType: this.sourceType,
          count: Number(this.maxCount)-this.imgList.length,
          success: (chooseImageRes) => {
						console.log(chooseImageRes);
            const tempFilePaths = chooseImageRes.tempFilePaths;
						
						for(var i=0; i<tempFilePaths.length; i++){
							//this.imgList.push(tempFilePaths[i]);
							//
							uni.uploadFile({
								url: baseApiUrl + 'common/upload',
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
									console.log(JSON.parse(uploadFileRes.data))
									var dataimg=JSON.parse(uploadFileRes.data);
									console.log(dataimg.data.fullurl);
									this.imgList.push(dataimg.data.fullurl);
									var imgLength=this.imgList.length;
									console.log('====='+imgLength);
									this.maxCountNum=9-imgLength;
									this.isMaxNum();
									this.$emit("chooseFile", this.imgList,tempFilePaths,this.type, )
									
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
		let type=this.type;
        let getUrl = this.handleImg();
        uni.showModal({
          title: '提示',
          content: '您确定删除吗？',
          success: (res)=> {
            if (res.confirm) {
             getUrl.splice(eq, 1);
             this.$emit("imgDelete", getUrl, eq , type);
             this.isMaxNum();
            }
          }
        });
      },
      // 预览图片
      previewImage(eq) {
        let getUrl = this.handleImg();
        uni.previewImage({
          current: getUrl[eq],
          urls: getUrl
        })
      },
      //返回需要操作的图片数组
      //如果是回调了则操作回填后的数组 否则操作临时路径的图片数组
      handleImg() {
		  console.log("this.mode.length: ",this.mode.length);
        //return this.mode.length > 0 ? this.showList : this.imgList
		return this.imgList
      },
      //判断图片数量是否已经到最大数量
      isMaxNum() {
		  console.log(this.maxCount);
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
    width: 100%;
    height: 100%;
  }

  .imglistItem {
    position: relative;
    float: left;
    margin-bottom: 20rpx;
    border-radius: 10rpx;
  }

  .column3 {
    width: 33.3333%;
    height: 200rpx;
  }

  .column4 {
    width: 25%;
    height: 200rpx;
  }

  .itemImg {
    width: 200upx;
    height: 200upx;
    margin: 0 auto;
    display: block;
    border-radius: 10rpx;
  }
  
	.direct{display: flex; align-items: center; justify-content: center; width: 200upx; height: 200upx; margin: 0 auto; border: 2px dashed #dbdbda; border-radius: 10px;}
	.direct image{display: block; width: 60upx; height: 60upx;}
	
  .cancelBtn {
    position: absolute;
    top: -5px;
    right: 0px;
		color: #999;
		width: 36upx;
		height: 36upx;
		border-radius: 50%;
		background: #fff;
		display: block;
  }

  /* 上传控件 */
  .uploadControl {
    font-size: 50rpx;
    color: #888;
    background-color: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
		border-radius: 10px;
  }
	
	.uploadControl image{width: 60upx; height: 60upx;}

  /*  上传  str end*/
  .clear {
    clear: both;
  }
</style>
