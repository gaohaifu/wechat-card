<template>
	<view>
		<!--侧滑的主体内容-->
		<scroll-view  class="scrollB"  :class="{'scrollB_active':isShowLeft==true&&isShowLeftFri==true}"   @touchmove.stop.prevent="moveHandle"  scroll-y >
			<view>001</view>
			<view>002</view>
			<view>003</view>
			<view>004</view>
			<view>005</view>
			<view>006</view>
			<view>007</view>
			<view>008</view>
			<view>009</view>
			<view>010</view>
			<view>011</view>
			<view>012</view>
			<view>013</view>
			<view>014</view>
			<view>015</view>
			<view>016</view>
			<view>017</view>
			<view>018</view>
			<view>019</view>
			<view>020</view>
			<view>021</view>
			<view>022</view>
			<view>023</view>
			<view>024</view>
			<view>025</view>
			<view>026</view>
			<view>027</view>
			<view>028</view>
			<view>029</view>
			<view>030</view>
			<view>031</view>
			<view>032</view>
			<view>033</view>
			<view>034</view>
			<view>035</view>
			<view>036</view>
			<view>037</view>
			<view>038</view>
			<view>039</view>
			<view>040</view>
			<view>041</view>
			<view>042</view>
			<view>043</view>
			<view>044</view>
			<view>045</view>
			<view>046</view>
			<view>047</view>
			<view>048</view>
			<view>049</view>
			<view>050</view>
			<view>051</view>
			<view>052</view>
			<view>053</view>
			<view>054</view>
			<view>055</view>
			<view>056</view>
			<view>057</view>
			<view>058</view>
			<view>059</view>
			<view>060</view>
			<view>061</view>
			<view>062</view>
			<view>063</view>
			<view>064</view>
			<view>065</view>
			<view>066</view>
			<view>067</view>
			<view>068</view>
			<view>069</view>
			<view>070</view>
			<view>071</view>
			<view>072</view>
			<view>073</view>
			<view>074</view>
			<view>075</view>
			<view>076</view>
			<view>077</view>
			<view>078</view>
			<view>079</view>
			<view>080</view>
			<view>081</view>
			<view>082</view>
			<view>083</view>
			<view>084</view>
			<view>085</view>
			<view>086</view>
			<view>087</view>
			<view>088</view>
			<view>089</view>
			<view>090</view>
			<view>091</view>
			<view>092</view>
			<view>093</view>
			<view>094</view>
			<view>095</view>
			<view>096</view>
			<view>097</view>
			<view>098</view>
			<view>099</view>
			<view>100</view>
		</scroll-view>
		<!-- 侧滑的阴影 -->
		<scroll-view   :class="{scrollC_active:isShowLeft}"  class="scrollC"  @touchmove="closeDraw()"  @click="closeDraw()"  ></scroll-view>
	</view>
</template>

<script>
	var _this;
	export default{
		name : "left-scroll",
		props:{
			isShowLeft    : Boolean,
			isShowLeftFri : Boolean
		},
		methods:{
			closeDraw(){				//关闭侧滑
				this.$emit('closeLeft');
			},
			moveHandle(){				//禁止底层下面的拖动
				
			}
		}
	}
</script>

<style>
	/* 侧滑容器-默认从右到左，如果想右往左，下面css的所有left改为right就可以了
	    480upx根据你自己设计稿的宽度自定义,0.3s自己定义长短动画时长; 
	*/
	.scrollB{
		width: 480upx;
		height: 100%;
		position: fixed;
		top: 0upx; 
		left: -480upx;				
		background: white;
		transition: all  0.3s;
		z-index: 1004;
		text-align: center;
	}
	.scrollC{
		width: 100%;
		height: 100%;
		position: fixed;
		top: 0upx;
		left: 100%;
		background: rgba(0,0,0,.4);
		z-index:1003;
	}
	/* 内容 */
	.scrollC_active{
		left:0% !important;
	}
	/* 阴影容器 */
	.scrollB_active{
		left: 0upx !important;
	}
	/* 侧滑结束 */
</style>
