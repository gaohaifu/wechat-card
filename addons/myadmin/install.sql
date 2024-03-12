CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_addon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `begintime` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `endtime` bigint(16) DEFAULT NULL COMMENT '到期时间',
  `forever` int(11) DEFAULT '0' COMMENT '是否长期',
  `company_id` int(11) DEFAULT NULL COMMENT '关联商家ID',
  `config` text COLLATE utf8mb4_unicode_ci COMMENT '插件配置',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='应用购买配置表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_addons` (
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT 'ID',
  `title` varchar(255) DEFAULT NULL COMMENT '名称',
  `intro` varchar(255) DEFAULT NULL COMMENT '介绍',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='开启插件应用表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` bigint(16) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  `company_id` int(11) DEFAULT NULL COMMENT '商家ID',
  `is_founder` int(11) DEFAULT '0' COMMENT '创始人',
  `group_ids` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0' COMMENT 'ID组',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '日志标题',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内容',
  `ip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'User-Agent',
  `createtime` bigint(16) DEFAULT NULL COMMENT '操作时间',
  `company_id` int(11) DEFAULT NULL COMMENT '商家ID',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `name` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员日志表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类别',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片类型',
  `imageframes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件名称',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '透传数据',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建日期',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` bigint(16) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件 sha1编码',
  `company_id` int(11) DEFAULT NULL COMMENT '商家ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='附件表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  `company_id` int(11) DEFAULT NULL COMMENT '商家ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='分组表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID',
  `company_id` int(11) DEFAULT NULL COMMENT '商家ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='权限分组表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_auth_player` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `label` varchar(255) DEFAULT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则ID',
  `content` text COMMENT '协议内容',
  `status` enum('normal','hidden','expired') DEFAULT 'normal' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图标',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '规则URL',
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '条件',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `menutype` enum('addtabs','blank','dialog','ajax') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '菜单类型',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `py` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '拼音首字母',
  `pinyin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '拼音',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `weigh` (`weigh`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='节点表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `identifier` varchar(255) DEFAULT NULL COMMENT '唯一标识',
  `type` varchar(255) DEFAULT NULL COMMENT '机构分组',
  `group_id` int(11) DEFAULT NULL COMMENT '分组ID',
  `label` varchar(255) DEFAULT NULL COMMENT '企业标签',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '机构名称',
  `logo` varchar(255) DEFAULT NULL COMMENT '企业logo',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `address_area` varchar(255) DEFAULT NULL COMMENT '地区名称',
  `address_code` varchar(255) DEFAULT NULL COMMENT '地区编码',
  `address` varchar(200) CHARACTER SET utf8 DEFAULT '' COMMENT '定点地址',
  `longitude` varchar(30) CHARACTER SET utf8 DEFAULT '' COMMENT '定点经度',
  `latitude` varchar(30) CHARACTER SET utf8 DEFAULT '' COMMENT '定点纬度',
  `handrate` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '手续费率',
  `taxerate` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '税费率',
  `admin_limit` int(11) DEFAULT '1' COMMENT '限制管理员数量',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '服务价格',
  `paystatus` enum('yes','no') DEFAULT 'no' COMMENT '支付状态',
  `paytime` bigint(16) DEFAULT NULL COMMENT '支付时间',
  `status` enum('created','normal','hidden','expired') DEFAULT 'created' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `player` varchar(255) DEFAULT '' COMMENT '角色组',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='机构表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_agreement` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `agreement_no` varchar(50) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `player` varchar(255) DEFAULT NULL,
  `player_id` int(11) DEFAULT '0',
  `company_id` int(11) DEFAULT '0',
  `starttime` bigint(16) DEFAULT NULL COMMENT '开始时间',
  `expiredtime` bigint(16) DEFAULT NULL COMMENT '过期时间',
  `content` text COMMENT '协议内容',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业角色协议表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `name` varchar(30) NOT NULL DEFAULT '',
  `label` text COMMENT '分类',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `company_id` int(11) DEFAULT '0' COMMENT '所属公司ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业分组表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员余额变动表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_player` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `player` varchar(255) DEFAULT NULL,
  `player_id` int(11) DEFAULT '0',
  `company_id` int(11) DEFAULT '0',
  `expiredtime` bigint(16) DEFAULT NULL COMMENT '过期时间',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业角色表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '变更积分',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '变更前积分',
  `after` int(10) NOT NULL DEFAULT '0' COMMENT '变更后积分',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员积分变动表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_company_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT '0' COMMENT '企业ID',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '提现金额',
  `settledmoney` decimal(10,2) DEFAULT NULL COMMENT '到账金额',
  `handrate` decimal(10,1) unsigned DEFAULT '0.0' COMMENT '手续费率',
  `handfee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '手续费',
  `taxerate` decimal(10,1) unsigned DEFAULT '0.0' COMMENT '税费率',
  `taxefee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '税费',
  `type` varchar(50) DEFAULT '' COMMENT '类型',
  `account` varchar(100) DEFAULT '' COMMENT '提现账户',
  `name` varchar(100) DEFAULT '' COMMENT '真实姓名',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `reply` varchar(255) DEFAULT NULL COMMENT '回复',
  `orderid` varchar(50) DEFAULT '' COMMENT '订单号',
  `transactionid` varchar(50) DEFAULT '' COMMENT '流水号',
  `status` enum('created','successed','rejected') DEFAULT 'created' COMMENT '状态:created=申请中,successed=成功,rejected=已拒绝',
  `transfertime` bigint(16) DEFAULT NULL COMMENT '转账时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='提现表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `group` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '变量值',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '变量字典数据',
  `rule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配置',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_config_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '变量值',
  `company_id` int(11) DEFAULT NULL COMMENT '关联商家ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '域名',
  `ssl_certificate` varchar(255) DEFAULT NULL,
  `ssl_certificate_key` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL COMMENT '机构ID',
  `install` enum('yes','no') DEFAULT 'no' COMMENT '是否安装',
  `install_dir` varchar(255) DEFAULT NULL COMMENT '安装路径',
  `status` varchar(255) DEFAULT 'normal',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='域名解析表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '组别ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '积分',
  `joinip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '加入IP',
  `jointime` bigint(16) DEFAULT NULL COMMENT '加入时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Token',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '状态',
  `company_id` int(11) DEFAULT NULL,
  `handrate` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '手续费率',
  `taxerate` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '税费率',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '组名',
  `rules` text COLLATE utf8mb4_unicode_ci COMMENT '权限节点',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员组表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员余额变动表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) DEFAULT NULL COMMENT '父ID',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '标题',
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `ismenu` tinyint(1) DEFAULT NULL COMMENT '是否菜单',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `status` enum('normal','hidden') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员规则表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '变更积分',
  `before` int(10) NOT NULL DEFAULT '0' COMMENT '变更前积分',
  `after` int(10) NOT NULL DEFAULT '0' COMMENT '变更后积分',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '备注',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='会员积分变动表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_user_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned DEFAULT '0' COMMENT '企业ID',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '金额',
  `settledmoney` decimal(10,2) DEFAULT NULL COMMENT '到账金额',
  `handrate` decimal(10,1) unsigned DEFAULT '0.0' COMMENT '手续费率',
  `handfee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '手续费',
  `taxerate` decimal(10,1) unsigned DEFAULT '0.0' COMMENT '税费率',
  `taxefee` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '税费',
  `type` varchar(50) DEFAULT '' COMMENT '类型',
  `account` varchar(100) DEFAULT '' COMMENT '提现账户',
  `name` varchar(100) DEFAULT '' COMMENT '真实姓名',
  `memo` varchar(255) DEFAULT NULL COMMENT '备注',
  `reply` varchar(255) DEFAULT NULL COMMENT '回复',
  `orderid` varchar(50) DEFAULT '' COMMENT '订单号',
  `transactionid` varchar(50) DEFAULT '' COMMENT '流水号',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `company_user_id` int(11) DEFAULT '0' COMMENT '企业会员ID',
  `status` enum('created','successed','rejected') DEFAULT 'created' COMMENT '状态:created=申请中,successed=成功,rejected=已拒绝',
  `transfertime` bigint(16) DEFAULT NULL COMMENT '转账时间',
  `createtime` bigint(16) DEFAULT NULL COMMENT '添加时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='提现表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `mould_id` int(11) DEFAULT NULL COMMENT '模块ID',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `name` varchar(30) NOT NULL DEFAULT '',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `company_id` int(11) DEFAULT '0' COMMENT '所属公司ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-分类表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `mould_id` int(11) DEFAULT '0' COMMENT '模型ID',
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `type` varchar(255) DEFAULT 'image',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面图',
  `images` text COMMENT '图片',
  `video` varchar(255) DEFAULT NULL COMMENT '视频',
  `audio` text COMMENT '音频',
  `description` varchar(1000) DEFAULT NULL COMMENT '描述',
  `content` text COMMENT '内容',
  `views` int(11) DEFAULT '0' COMMENT '浏览次数',
  `company_id` int(11) DEFAULT '0' COMMENT '关联商家ID',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-内容表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_mould` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `name` varchar(30) NOT NULL DEFAULT '',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-模型表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `category_id` int(11) DEFAULT NULL COMMENT '分类',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) DEFAULT 'image' COMMENT '标题',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面图',
  `images` text COMMENT '图片',
  `video` varchar(255) DEFAULT NULL COMMENT '视频',
  `content` text COMMENT '内容',
  `views` int(11) DEFAULT '0' COMMENT '浏览量',
  `company_id` int(11) DEFAULT '0' COMMENT '关联商家ID',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格',
  `unitnum` int(11) DEFAULT '1' COMMENT '单位数量',
  `unitname` varchar(10) DEFAULT '个' COMMENT '单位名称',
  `labelname` varchar(255) DEFAULT '购买' COMMENT '标签名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-产品表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_product_category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `name` varchar(30) NOT NULL DEFAULT '',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `company_id` int(11) DEFAULT '0' COMMENT '所属公司ID',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-产品分类表';

CREATE TABLE IF NOT EXISTS `__PREFIX__myadmin_web_single` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `title` varchar(255) DEFAULT 'image' COMMENT '标题',
  `cover` varchar(255) DEFAULT NULL COMMENT '封面图',
  `images` text COMMENT '图片',
  `video` varchar(255) DEFAULT NULL COMMENT '视频',
  `content` text COMMENT '内容',
  `views` int(11) DEFAULT '0' COMMENT '浏览量',
  `company_id` int(11) DEFAULT '0' COMMENT '关联商家ID',
  `status` enum('normal','hidden','expired') DEFAULT 'hidden' COMMENT '状态',
  `weigh` int(10) DEFAULT '0' COMMENT '权重',
  `createtime` bigint(16) DEFAULT NULL COMMENT '创建时间',
  `updatetime` bigint(16) DEFAULT NULL COMMENT '更新时间',
  `deletetime` bigint(16) DEFAULT NULL COMMENT '删除时间',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业网站-单页表';


ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `handrate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '手续费率' AFTER `latitude`;
ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `taxerate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '税费率' AFTER `handrate`;
ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `player` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '角色组' AFTER `deletetime`;
ALTER TABLE `__PREFIX__myadmin_company_group` DROP COLUMN `type`;
ALTER TABLE `__PREFIX__myadmin_company_group` DROP COLUMN `price`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` DROP COLUMN `handingfee`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` DROP COLUMN `taxes`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` MODIFY COLUMN `money` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '提现金额' AFTER `company_id`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `settledmoney` decimal(10, 2) NULL DEFAULT NULL COMMENT '到账金额' AFTER `money`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `handrate` decimal(10, 1) UNSIGNED NULL DEFAULT 0.0 COMMENT '手续费率' AFTER `settledmoney`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `handfee` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '手续费' AFTER `handrate`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `taxerate` decimal(10, 1) UNSIGNED NULL DEFAULT 0.0 COMMENT '税费率' AFTER `handfee`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `taxefee` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '税费' AFTER `taxerate`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `reply` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '回复' AFTER `memo`;
ALTER TABLE `__PREFIX__myadmin_user` ADD COLUMN `handrate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '手续费率' AFTER `company_id`;
ALTER TABLE `__PREFIX__myadmin_user` ADD COLUMN `taxerate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '税费率' AFTER `handrate`;

