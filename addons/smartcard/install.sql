CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_case` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '图片集',
  `maincontent` text NOT NULL COMMENT '介绍',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联企业',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='成功案例';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
  `picimage` varchar(255) NOT NULL DEFAULT '' COMMENT '分类图片',
  `introtext` varchar(200) NOT NULL DEFAULT '' COMMENT '分类介绍',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联企业id',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业产品分类';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_company` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '企业名称',
  `url` varchar(100) NOT NULL DEFAULT '' COMMENT '企业网址',
  `intro` varchar(255) NOT NULL DEFAULT '' COMMENT '企业简介',
  `shortname` varchar(30) NOT NULL DEFAULT '' COMMENT '企业简称',
  `begintime` bigint(16) NOT NULL DEFAULT '0' COMMENT '企业营业起始时间',
  `endtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '企业营业结束时间',
  `licenseimage` varchar(255) NOT NULL DEFAULT '' COMMENT '企业营业执执照',
  `licensenumber` varchar(100) NOT NULL DEFAULT '' COMMENT '企业营业执照号',
  `content` text NOT NULL COMMENT '企业详细介绍',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '企业照片集',
  `videofiles` varchar(2000) NOT NULL DEFAULT '' COMMENT '企业视频',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '联系地址',
  `phone` varchar(11) NOT NULL COMMENT '联系电话',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `latlng` varchar(50) NOT NULL DEFAULT '' COMMENT '经纬度',
  `partner` varchar(2000) NOT NULL DEFAULT '' COMMENT '合作伙伴',
  `administrators_ids` varchar(1000) NOT NULL DEFAULT '' COMMENT '企业管理员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='企业列表';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_design` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `pdffiles` varchar(1000) DEFAULT '' COMMENT 'pdf地址',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '图集',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业宣传册';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_favor` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `tags_id` int(10) NOT NULL DEFAULT '0' COMMENT '标签id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞访客id',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞员工',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='标签点赞列表';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '商品图',
  `recommenddata` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否推荐:0=不推荐,1=推荐',
  `category_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品分类',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品所属企业',
  `maincontent` text NOT NULL COMMENT '商品详情',
  `tags` varchar(100) NOT NULL DEFAULT '' COMMENT '商品标签',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联公司',
  `invite_id` int(10) NOT NULL DEFAULT '0' COMMENT '邀请人用户id',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '员工id',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '邀请时间',
  `position` varchar(255) NOT NULL DEFAULT '' COMMENT '职位',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '回复时间',
  `statusdata` enum('1','2','3') NOT NULL DEFAULT '1' COMMENT '回复状态:1=待确认,2=已同意,3=已拒绝',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息表';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_news` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `intro` text NOT NULL COMMENT '介绍',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '图集',
  `maincontent` text NOT NULL COMMENT '详情',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='企业动态';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_sc` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '相关员工id',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `status` enum('1','2','3','4') NOT NULL DEFAULT '1' COMMENT '状态:1=已确认(在企员工）,2=待确认(已同意邀请),3=待同意(等待用户同意),4=已解除(离职)',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='员工与企业关系表';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_staff` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '姓名',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `company_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属企业',
  `position` varchar(20) NOT NULL DEFAULT '' COMMENT '职位',
  `tags_ids` varchar(10) NOT NULL DEFAULT '' COMMENT '个人标签',
  `mobile` varchar(11) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '电话',
  `wechat` varchar(50) NOT NULL DEFAULT '' COMMENT '微信',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `visit` int(10) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `favor` int(10) NOT NULL DEFAULT '0' COMMENT '点赞',
  `address` varchar(100) NOT NULL DEFAULT '' COMMENT '地址',
  `picimages` varchar(2000) NOT NULL DEFAULT '' COMMENT '我的照片',
  `videofiles` varchar(2000) NOT NULL DEFAULT '' COMMENT '我的视频',
  `introcontent` text NOT NULL COMMENT '我的介绍',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `statusdata` enum('1','2','3','4') NOT NULL DEFAULT '2' COMMENT '状态:1=审核通过,2=待确认,3=审核失败,4=待同意',
  `theme_id` int(3) NOT NULL DEFAULT '1' COMMENT '名片主题ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='企业员工';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '标签名称',
  `statusdata` enum('1','2') NOT NULL DEFAULT '1' COMMENT '是否显示:1=显示,2=不显示',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `typedata` enum('0','1') NOT NULL DEFAULT '0' COMMENT '创建类型:0=用户创建,1=系统创建',
  `staff_id` int(10) NOT NULL DEFAULT '0' COMMENT '给员工的标签',
  `isrecommend` enum('1','2') CHARACTER SET utf8mb4 NOT NULL DEFAULT '2' COMMENT '是否推荐:1=推荐,2=不推荐',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '创建者id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户标签';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_theme` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `colour` varchar(255) NOT NULL DEFAULT '' COMMENT '主题颜色',
  `backgroundimage` varchar(255) NOT NULL DEFAULT '' COMMENT '主题背景',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` bigint(16) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `statusdata` enum('1','2') NOT NULL DEFAULT '1' COMMENT '状态:1=启用,2=关闭',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '主题名称',
  `cardimage` varchar(255) NOT NULL DEFAULT '' COMMENT '名片背景',
  `fontcolor` varchar(255) NOT NULL DEFAULT '' COMMENT '名片字体颜色',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='名片主题表';

CREATE TABLE IF NOT EXISTS `__PREFIX__smartcard_visitors` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '访客id',
  `staff_id` int(10) DEFAULT '0' COMMENT '员工id',
  `visittime` bigint(16) NOT NULL DEFAULT '0' COMMENT '访问时间',
  `createtime` bigint(16) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `updatetime` bigint(16) DEFAULT '0' COMMENT '更新时间',
  `typedata` enum('1','2','3','4','5','6','7','8','9') NOT NULL DEFAULT '1' COMMENT '访问类型:1=访问员工主页,2=访问企业主页,3=访问企业宣传册,4=访问案例,5=查看企业商品,6=查看企业动态,7=点赞员工,8=点赞动态,9=点赞其他备用2',
  `company_id` int(10) DEFAULT '0' COMMENT '企业id',
  `news_id` int(10) NOT NULL DEFAULT '0' COMMENT '动态id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访客';

ALTER TABLE `__PREFIX__admin`  ADD COLUMN  company_id bigint(16)  DEFAULT'0' COMMENT'企业id' AFTER status;
ALTER TABLE `__PREFIX__admin` ADD COLUMN   administrators_id bigint(16) DEFAULT'0' COMMENT'企业管理者id' AFTER status;
INSERT INTO `__PREFIX__smartcard_theme` (`id`, `colour`, `backgroundimage`, `weigh`, `createtime`, `updatetime`, `statusdata`, `name`, `cardimage`, `fontcolor`) VALUES
(1, '#3B3B3B ','/assets/addons/smartcard/images/001.jpg','1','1646214239','1646214239','1','高级黑','/assets/addons/smartcard/images/001-1.png','#ffffff'),(2, '#3399ff','/assets/addons/smartcard/images/002.jpg','2 ','1646214239','1646214239','1','蓝色','/assets/addons/smartcard/images/002-1.jpg','#000000'),(3, '#8B5A00','/assets/addons/smartcard/images/003.jpg','3 ','1646214239','1646214239','1','棕色','/assets/addons/smartcard/images/003-1.jpg','#333333'),(4, '#FF0000','/assets/addons/smartcard/images/004.jpg','4','1646214239','1646214239','1','红色','/assets/addons/smartcard/images/004-1.jpg','#ffffff'),(5, '#a4c2f4','/assets/addons/smartcard/images/005.jpg','5','1646214239','1646214239','1','浅蓝色','/assets/addons/smartcard/images/005-1.jpg','#333333');