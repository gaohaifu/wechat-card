<?php 
 return array (
  'table_name' => 'fa_smartcard_case,fa_smartcard_category,fa_smartcard_company,fa_smartcard_design,fa_smartcard_favor,fa_smartcard_goods,fa_smartcard_message,fa_smartcard_news,fa_smartcard_sc,fa_smartcard_staff,fa_smartcard_tags,fa_smartcard_theme,fa_smartcard_visitors',
  'self_path' => 'application/api/controller/smartcard/
application/api/lang/zh-cn/smartcard/
application/admin/lang/zh-cn/smartcard/
public/assets/addons/smartcard/',
  'update_data' => 'ALTER TABLE `__PREFIX__admin`  ADD COLUMN  company_id bigint(16)  DEFAULT\'0\' COMMENT\'企业id\' AFTER status;
ALTER TABLE `__PREFIX__admin` ADD COLUMN   administrators_id bigint(16) DEFAULT\'0\' COMMENT\'企业管理者id\' AFTER status;
INSERT INTO `__PREFIX__smartcard_theme` (`id`, `colour`, `backgroundimage`, `weigh`, `createtime`, `updatetime`, `statusdata`, `name`, `cardimage`, `fontcolor`) VALUES
(1, \'#3B3B3B \',\'/assets/addons/smartcard/images/001.jpg\',\'1\',\'1646214239\',\'1646214239\',\'1\',\'高级黑\',\'/assets/addons/smartcard/images/001-1.png\',\'#ffffff\'),(2, \'#3399ff\',\'/assets/addons/smartcard/images/002.jpg\',\'2 \',\'1646214239\',\'1646214239\',\'1\',\'蓝色\',\'/assets/addons/smartcard/images/002-1.jpg\',\'#000000\'),(3, \'#8B5A00\',\'/assets/addons/smartcard/images/003.jpg\',\'3 \',\'1646214239\',\'1646214239\',\'1\',\'棕色\',\'/assets/addons/smartcard/images/003-1.jpg\',\'#333333\'),(4, \'#FF0000\',\'/assets/addons/smartcard/images/004.jpg\',\'4\',\'1646214239\',\'1646214239\',\'1\',\'红色\',\'/assets/addons/smartcard/images/004-1.jpg\',\'#ffffff\'),(5, \'#a4c2f4\',\'/assets/addons/smartcard/images/005.jpg\',\'5\',\'1646214239\',\'1646214239\',\'1\',\'浅蓝色\',\'/assets/addons/smartcard/images/005-1.jpg\',\'#333333\');',
);