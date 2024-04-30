
-- ----------------------------
-- Table structure for fa_fastscrm_channel_code
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_channel_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '活码名称',
  `group_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属分组',
  `config_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '联系我配置ID',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '类型:1=单人,2=多人',
  `scene` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '使用场景:1=在小程序中联系,2=通过二维码联系',
  `style` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '小程序样式:1=样式1,2=样式2,3=样式3',
  `remark` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注信息',
  `skip_verify` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '无需验证:0=否,1=是',
  `is_exclusive` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '统一跟进人:0=否,1=是',
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '二维码',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='渠道码管理';

-- ----------------------------
-- Table structure for fa_fastscrm_channel_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_channel_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for fa_fastscrm_channel_tags
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_channel_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道码ID',
  `tag_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='渠道码标签';

-- ----------------------------
-- Table structure for fa_fastscrm_channel_workers
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_channel_workers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道码ID',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '使用员工',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='渠道码使用员工';

-- ----------------------------
-- Table structure for fa_fastscrm_customer
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_customer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `external_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'UserID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客户名称',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职务信息',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `corp_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业的简称',
  `corp_full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业的主体名称',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '类型:1=微信用户,2=企业微信用户',
  `gender` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '性别:0=未知,1=男性,2=女性',
  `unionid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'unionid',
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '渠道活码',
  `fl_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业成员userid',
  `fl_remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `fl_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `fl_createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `fl_tags` text COLLATE utf8mb4_unicode_ci COMMENT '标签',
  `fl_remark_corp_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业名称备注',
  `fl_remark_mobiles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号码备注',
  `fl_add_way` enum('0','1','2','3','4','5','6','7','8','9','10','201','202') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '客户的来源:0=未知来源,1=扫描二维码,2=搜索手机号,3=名片分享,4=群聊,5=手机通讯录,6=微信联系人,7=未知,8=安装第三方应用时自动添加的客服人员,9=搜索邮箱,10=视频号添加,201=内部成员共享,202=管理员/负责人分配',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户管理';

-- ----------------------------
-- Table structure for fa_fastscrm_customer_batch
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_customer_batch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mobile` varchar(11) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分配员工',
  `tags` text COLLATE utf8mb4_unicode_ci COMMENT '客户标签',
  `status` enum('0','1','2','3') CHARACTER SET utf8 DEFAULT '0' COMMENT '状态:0=未分配,1=待添加,2=待通过,3=已添加',
  `branchnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分配次数',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `addtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='批量添加客户';

-- ----------------------------
-- Table structure for fa_fastscrm_customer_data
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_customer_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `new_apply_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '主动向客户发起的好友申请数量',
  `new_contact_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '新增客户数(成员新添加的客户数量)',
  `chat_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '聊天总数(成员有主动发送过消息的单聊总数)',
  `message_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '发送消息数(成员在单聊中发送的消息总数)',
  `reply_percentage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '已回复聊天占比',
  `avg_reply_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '平均首次回复时长(分)',
  `negative_feedback_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '删除/拉黑成员的客户数',
  `stat_time` bigint(16) DEFAULT NULL COMMENT '数据日期',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户会话统计';

-- ----------------------------
-- Table structure for fa_fastscrm_customer_lose
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_customer_lose` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `external_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'UserID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客户名称',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职务信息',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `corp_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业的简称',
  `corp_full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业的主体名称',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '类型:1=微信用户,2=企业微信用户',
  `gender` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '性别:0=未知,1=男性,2=女性',
  `unionid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'unionid',
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '渠道活码',
  `fl_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业成员userid',
  `fl_remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `fl_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '描述',
  `fl_createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `fl_tags` text COLLATE utf8mb4_unicode_ci COMMENT '标签',
  `fl_remark_corp_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业名称备注',
  `fl_remark_mobiles` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号码备注',
  `fl_add_way` enum('0','1','2','3','4','5','6','7','8','9','10','201','202') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '客户的来源:0=未知来源,1=扫描二维码,2=搜索手机号,3=名片分享,4=群聊,5=手机通讯录,6=微信联系人,7=未知,8=安装第三方应用时自动添加的客服人员,9=搜索邮箱,10=视频号添加,201=内部成员共享,202=管理员/负责人分配',
  `del_type` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '流失方式:0=员工删除,1=客户删除',
  `createtime` bigint(16) DEFAULT NULL COMMENT '流失时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流失客户统计';

-- ----------------------------
-- Table structure for fa_fastscrm_customer_sale
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_customer_sale` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '任务名称',
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '素材附件',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择员工',
  `depart_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择部门',
  `store_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '选择门店',
  `typedata` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '营销人群:1=按员工,2=按部门,3=按门店',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '发布文案',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=未执行,1=已执行',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户营销';

-- ----------------------------
-- Table structure for fa_fastscrm_depart
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_depart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `depart_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '部门名称',
  `department_leader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '部门负责人ID',
  `parentid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父部门ID',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='部门管理';

-- ----------------------------
-- Table structure for fa_fastscrm_group_admin
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) DEFAULT '0' COMMENT '关联群ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群成员ID',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群管理员';

-- ----------------------------
-- Table structure for fa_fastscrm_group_chat
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群名',
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群主ID',
  `notice` longtext COLLATE utf8mb4_unicode_ci COMMENT '群公告',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `status` enum('0','1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '群跟进状态:0=跟进人正常,1=跟进人离职,2=离职继承中,3=离职继承完成',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群列表';

-- ----------------------------
-- Table structure for fa_fastscrm_group_chat_sale
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_chat_sale` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '任务名称',
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '素材附件',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择群主',
  `depart_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择部门',
  `store_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '选择门店',
  `typedata` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '营销人群:1=按群主,2=按部门,3=按门店',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '发布文案',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=未执行,1=已执行',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客户群群发';

-- ----------------------------
-- Table structure for fa_fastscrm_group_data
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群主ID',
  `new_chat_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '新增客户群数量',
  `chat_total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '截至当天客户群总数量',
  `chat_has_msg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '截至当天有发过消息的客户群数量',
  `new_member_cnt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客户群新增群人数',
  `member_total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '截至当天客户群总人数',
  `member_has_msg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '截至当天有发过消息的群成员数',
  `msg_total` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '截至当天客户群消息总数',
  `stat_time` bigint(16) DEFAULT NULL COMMENT '数据日期',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群数据';

-- ----------------------------
-- Table structure for fa_fastscrm_group_user
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) DEFAULT '0' COMMENT '关联群ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群成员ID',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '成员类型:1=企业成员,2=外部联系人',
  `unionid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '微信unionid',
  `join_time` bigint(16) DEFAULT NULL COMMENT '入群时间',
  `join_scene` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '入群方式:1=直接邀请,2=邀请链接,3=扫描群二维码',
  `invitor` longtext COLLATE utf8mb4_unicode_ci COMMENT '邀请者',
  `group_nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群昵称',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名字',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群成员';

-- ----------------------------
-- Table structure for fa_fastscrm_group_user_lose
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_group_user_lose` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) DEFAULT '0' COMMENT '关联群ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群成员ID',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '成员类型:1=企业成员,2=外部联系人',
  `unionid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '微信unionid',
  `join_time` bigint(16) DEFAULT NULL COMMENT '入群时间',
  `join_scene` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '入群方式:1=直接邀请,2=邀请链接,3=扫描群二维码',
  `invitor` longtext COLLATE utf8mb4_unicode_ci COMMENT '邀请者',
  `group_nickname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群昵称',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名字',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '流失时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群流失';

-- ----------------------------
-- Table structure for fa_fastscrm_material_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_material_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='素材分组';

-- ----------------------------
-- Table structure for fa_fastscrm_material_item
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_material_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` enum('0','1','2','3','4','5','6','7') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '素材类型:0=消息,1=图片,2=文章,3=链接,4=音频,5=视频,6=小程序,7=文件',
  `group_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `image_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片id',
  `media_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '媒体id',
  `sptime` bigint(16) DEFAULT NULL COMMENT '过期时间',
  `fj_media_id` text COLLATE utf8mb4_unicode_ci COMMENT '媒体id',
  `fj_sptime` bigint(16) DEFAULT NULL COMMENT '过期时间',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '消息内容',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片',
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '视频',
  `file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件',
  `voice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '音频',
  `link_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '链接地址',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `appid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '小程序appid',
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '小程序路径',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='素材管理';

-- ----------------------------
-- Table structure for fa_fastscrm_moments_sale
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_moments_sale` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '任务名称',
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '素材附件',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择员工',
  `depart_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择部门',
  `store_id` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '选择门店',
  `typedata` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '营销人群:1=按员工,2=按部门,3=按门店',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '发布文案',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=未执行,1=已执行',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='朋友圈营销';

-- ----------------------------
-- Table structure for fa_fastscrm_moments_sale_log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_moments_sale_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sale_id` int(10) DEFAULT '0' COMMENT '关联任务',
  `jobid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '异步任务ID',
  `moment_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '朋友圈ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '执行员工',
  `status` enum('0','1','2','3') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=失败,1=开始创建任务,2=正在创建任务中,3=创建任务已完成',
  `publish_status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '员工发表状态:0=未发表,1=已发表',
  `createtime` bigint(16) DEFAULT NULL COMMENT '操作时间',
  `send_time` bigint(16) DEFAULT NULL COMMENT '发送时间',
  `message` longtext COLLATE utf8mb4_unicode_ci COMMENT '消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='朋友圈营销日志表';

-- ----------------------------
-- Table structure for fa_fastscrm_reply_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_reply_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='话术分组';

-- ----------------------------
-- Table structure for fa_fastscrm_reply_item
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_reply_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `typedata` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '类型:1=文本,2=素材',
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '素材附件',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '话术内容',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='话术管理';

-- ----------------------------
-- Table structure for fa_fastscrm_reply_log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_reply_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `reply_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '话术ID',
  `entry` enum('single_chat_tools','group_chat_tools','chat_attachment') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型:single_chat_tools=单聊会话,group_chat_tools=群聊会话,chat_attachment=聊天附件栏',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '外部联系人ID',
  `chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '群ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '分享时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='话术分享记录';

-- ----------------------------
-- Table structure for fa_fastscrm_sale_log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_sale_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sale_id` int(10) DEFAULT '0' COMMENT '关联任务',
  `msgid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '消息ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `external_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客户ID',
  `chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客户群ID',
  `type` enum('1','2','3') CHARACTER SET utf8 DEFAULT '1' COMMENT '任务类型:1=客户群群发,2=客户消息',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=失败,1=成功',
  `send_status` enum('0','1','2','3') CHARACTER SET utf8 DEFAULT '0' COMMENT '发送状态:0=未发送,1=已发送,2=因客户不是好友导致发送失败,3=因客户已经收到其他群发消息导致发送失败',
  `createtime` bigint(16) DEFAULT NULL COMMENT '操作时间',
  `send_time` bigint(16) DEFAULT NULL COMMENT '发送时间',
  `message` longtext COLLATE utf8mb4_unicode_ci COMMENT '消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群发营销日志表';

-- ----------------------------
-- Table structure for fa_fastscrm_sale_welcome
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_sale_welcome` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '欢迎语名称',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '欢迎语内容',
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '选择素材',
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '选择员工',
  `store_id` int(10) unsigned DEFAULT NULL COMMENT '门店',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='欢迎语';

-- ----------------------------
-- Table structure for fa_fastscrm_store
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '门店名称',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系号码',
  `status` enum('1','0') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '门店状态:0=已关店,1=营业中',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '店铺编码',
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所在地区',
  `lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '经度',
  `lng` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '纬度',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '详细地址',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='门店管理';

-- ----------------------------
-- Table structure for fa_fastscrm_store_bind
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_store_bind` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '门店id',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '员工_id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='门店绑定';

-- ----------------------------
-- Table structure for fa_fastscrm_tag
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) DEFAULT '0' COMMENT '所属标签组',
  `tag_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签名称',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签管理';

-- ----------------------------
-- Table structure for fa_fastscrm_tag_group
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_tag_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签组ID',
  `group_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标签组名称',
  `order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='标签组管理';

-- ----------------------------
-- Table structure for fa_fastscrm_task_log
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_task_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名字',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '任务名称',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '状态:0=失败,1=成功',
  `createtime` bigint(16) DEFAULT NULL COMMENT '操作时间',
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='同步任务日志表';

-- ----------------------------
-- Table structure for fa_fastscrm_worker
-- ----------------------------
CREATE TABLE IF NOT EXISTS `__PREFIX__fastscrm_worker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'UserID',
  `fauser_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '绑定会员id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '成员名称',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属部门',
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属门店',
  `store_leader` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '是否店长:0=否,1=是',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号码',
  `order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '权重',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职务信息',
  `gender` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '性别:0=未定义,1=男性,2=女性',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮箱',
  `biz_mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业邮箱',
  `is_leader_in_dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '负责人',
  `direct_leader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '直属上级',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `thumb_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '座机',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户token',
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '别名',
  `extattr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `status` enum('1','2','4','5') COLLATE utf8mb4_unicode_ci DEFAULT '4' COMMENT '激活状态:1=已激活,2=已禁用,4=未激活,5=退出企业',
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '二维码',
  `external_profile` text COLLATE utf8mb4_unicode_ci COMMENT '对外属性',
  `external_position` text COLLATE utf8mb4_unicode_ci COMMENT '对外职务',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地址',
  `open_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'openid',
  `main_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '主部门',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='员工管理';


-- ----------------------------
-- Table structure for fa_fastscrm_access_token
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_access_token` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `corpid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业ID',
  `corpsecret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '应用密钥',
  `access_token` text COLLATE utf8mb4_unicode_ci COMMENT '凭证',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `expiretime` bigint(16) DEFAULT NULL COMMENT '过期时间',
  `ticket` text COLLATE utf8mb4_unicode_ci COMMENT 'jsapi_ticket',
  `tickettime` bigint(16) DEFAULT NULL COMMENT 'ticket过期时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='token表';

-- ----------------------------
-- Table structure for fa_fastscrm_intercept
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_intercept` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属分组',
  `rule_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `rule_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则ID',
  `typedata` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '生效类型:1=按员工,2=按部门',
  `word_list` text COLLATE utf8mb4_unicode_ci COMMENT '敏感词',
  `semantics_list` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '额外的拦截语义规则:1=手机号码,2=邮箱地址,3=红包',
  `intercept_type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '拦截方式:1=警告并拦截发送,2=仅发警告',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='敏感词管理';

-- ----------------------------
-- Table structure for fa_fastscrm_intercept_departs
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_intercept_departs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `intercept_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '敏感词ID',
  `depart_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '部门ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='敏感词生效部门';

-- ----------------------------
-- Table structure for fa_fastscrm_intercept_group
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_intercept_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='敏感词分组';

-- ----------------------------
-- Table structure for fa_fastscrm_intercept_workers
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_intercept_workers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `intercept_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '敏感词ID',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='敏感词生效员工';

-- ----------------------------
-- Table structure for fa_fastscrm_message_template
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_message_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '模版名称',
  `msg_type` enum('text','markdown','image','news','file') COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT '消息类型:text=文本,markdown=markdown,image=图片,news=图文,file=文件',
  `json` longtext COLLATE utf8mb4_unicode_ci COMMENT '消息内容',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '状态:0=禁用,1=正常',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息模版';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `group_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属分组',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '机器人名称',
  `url` text COLLATE utf8mb4_unicode_ci COMMENT 'Webhook地址',
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '状态:0=禁用,1=正常',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='机器人管理';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook_group
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='机器人分组';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook_send
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook_send` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '名称',
  `template_id` int(10) NOT NULL DEFAULT '0' COMMENT '消息模板ID',
  `type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '类型:1=立即发送,2=定时发送',
  `mentioned_type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '接收范围:1=指定员工,2=所有人',
  `creater` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '创建人',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `fixedtime` bigint(16) DEFAULT NULL COMMENT '定时时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息通知';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook_send_log
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook_send_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `send_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知ID',
  `webhook_id` int(10) NOT NULL DEFAULT '0' COMMENT '机器人ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名字',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `status` enum('-1','0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '状态:-1=失败,0=待发送,1=成功',
  `createtime` bigint(16) DEFAULT NULL COMMENT '操作时间',
  `message` longtext COLLATE utf8mb4_unicode_ci COMMENT '消息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='机器人通知日志';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook_send_webhooks
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook_send_webhooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `send_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知ID',
  `webhook_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '机器人ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='通知生效机器人';

-- ----------------------------
-- Table structure for fa_fastscrm_webhook_send_workers
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_webhook_send_workers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `send_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '通知ID',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '员工ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='通知生效群员';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_onjob
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_onjob` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `handover_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '原跟进员工UserID',
  `takeover_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工UserID',
  `handover_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '原跟进员工名称',
  `takeover_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工名称',
  `handover_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '原跟进员工部门',
  `takeover_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工部门',
  `type` enum('customer','groupchat') COLLATE utf8mb4_unicode_ci DEFAULT 'customer' COMMENT '类型:customer=转移客户,groupchat=转移群聊',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=未执行,1=已执行',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='在职继承';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_onjob_customers
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_onjob_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `onjob_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `external_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '转移客户',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `status` enum('1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '接替状态:1=接替完毕,2=等待接替,3=客户拒绝,4=接替成员客户达到上限',
  `takeover_time` bigint(16) DEFAULT NULL COMMENT '接替客户时间',
  `errmsg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '消息描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='在职继承客户';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_onjob_groupchat
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_onjob_groupchat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `onjob_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '转移群聊',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '接替状态:0=失败,1=成功,2=等待接替',
  `errmsg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '消息描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='在职继承群聊';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_resigned
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_resigned` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `handover_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '离职员工UserID',
  `takeover_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工UserID',
  `handover_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '离职员工名称',
  `takeover_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工名称',
  `handover_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '离职员工部门',
  `takeover_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接替员工部门',
  `type` enum('customer','groupchat') COLLATE utf8mb4_unicode_ci DEFAULT 'customer' COMMENT '类型:customer=转移客户,groupchat=转移群聊',
  `status` enum('0','1') CHARACTER SET utf8 DEFAULT '0' COMMENT '执行状态:0=未执行,1=已执行',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='离职继承';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_resigned_customers
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_resigned_customers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `resigned_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `external_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '转移客户',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `status` enum('1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '接替状态:1=接替完毕,2=等待接替,3=客户拒绝,4=接替成员客户达到上限',
  `takeover_time` bigint(16) DEFAULT NULL COMMENT '接替客户时间',
  `errmsg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '消息描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='离职继承客户';

-- ----------------------------
-- Table structure for fa_fastscrm_transfer_resigned_groupchat
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_transfer_resigned_groupchat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `resigned_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '任务ID',
  `chat_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '转移群聊',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `status` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '2' COMMENT '接替状态:0=失败,1=成功,2=等待接替',
  `errmsg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '消息描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='离职继承群聊';

-- ----------------------------
-- Table structure for fa_fastscrm_worker_delete
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_worker_delete` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'UserID',
  `fauser_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '绑定会员id',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '成员名称',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属部门',
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '所属门店',
  `store_leader` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '是否店长:0=否,1=是',
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '手机号码',
  `order` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '权重',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '职务信息',
  `gender` enum('0','1','2') COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT '性别:0=未定义,1=男性,2=女性',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮箱',
  `biz_mail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业邮箱',
  `is_leader_in_dept` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '负责人',
  `direct_leader` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '直属上级',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `thumb_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '座机',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户token',
  `alias` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '别名',
  `extattr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `status` enum('1','2','4','5') COLLATE utf8mb4_unicode_ci DEFAULT '4' COMMENT '激活状态:1=已激活,2=已禁用,4=未激活,5=退出企业',
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '二维码',
  `external_profile` text COLLATE utf8mb4_unicode_ci COMMENT '对外属性',
  `external_position` text COLLATE utf8mb4_unicode_ci COMMENT '对外职务',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '地址',
  `open_userid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'openid',
  `main_department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '主部门',
  `createtime` bigint(16) DEFAULT NULL COMMENT '离职时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='离职员工';

-- ----------------------------
-- Table structure for fa_fastscrm_kf_account
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_kf_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `open_kfid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客服账号ID',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客服名称',
  `media_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '临时素材ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '账号链接',
  `endtime` bigint(16) DEFAULT NULL COMMENT '临时素材过期时间',
  `manage_privilege` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'API管理权限',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='客服账号';

-- ----------------------------
-- Table structure for fa_fastscrm_kf_servicer
-- ----------------------------
CREATE TABLE `__PREFIX__fastscrm_kf_servicer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `open_kfid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '客服账号ID',
  `worker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '接待员工',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='接待人员';

-- ----------------------------
-- 1.2.1 修改群公告字段类型
-- ----------------------------
ALTER TABLE `__PREFIX__fastscrm_group_chat` MODIFY COLUMN `notice`  longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '群公告' AFTER `owner`;
