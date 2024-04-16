<?php 
 return array (
  'table_name' => 'fa_myadmin_addon,fa_myadmin_addons,fa_myadmin_admin,fa_myadmin_admin_log,fa_myadmin_attachment,fa_myadmin_auth_group,fa_myadmin_auth_group_access,fa_myadmin_auth_player,fa_myadmin_auth_rule,fa_myadmin_company,fa_myadmin_company_agreement,fa_myadmin_company_group,fa_myadmin_company_money_log,fa_myadmin_company_player,fa_myadmin_company_score_log,fa_myadmin_company_withdraw,fa_myadmin_config,fa_myadmin_config_value,fa_myadmin_domain,fa_myadmin_user,fa_myadmin_user_group,fa_myadmin_user_money_log,fa_myadmin_user_rule,fa_myadmin_user_score_log,fa_myadmin_user_withdraw,fa_myadmin_web_category,fa_myadmin_web_content,fa_myadmin_web_mould,fa_myadmin_web_product,fa_myadmin_web_product_category,fa_myadmin_web_single',
  'self_path' => 'application/admin/lang/zh-cn/myadmin
application/myadmin/common.php
application/myadmin/config.php
application/myadmin/tags.php
application/myadmin/behavior/AdminLog.php
application/myadmin/controller/auth
application/myadmin/controller/general
application/myadmin/controller/user
application/myadmin/controller/web
application/myadmin/controller/store
application/myadmin/controller/Ajax.php
application/myadmin/controller/Common.php
application/myadmin/controller/Dashboard.php
application/myadmin/controller/Index.php
application/myadmin/view/auth
application/myadmin/view/common
application/myadmin/view/dashboard
application/myadmin/view/general
application/myadmin/view/web
application/myadmin/view/store
application/myadmin/view/index
application/myadmin/view/layout
application/myadmin/view/user
application/myadmin/lang/zh-cn/auth
application/myadmin/lang/zh-cn/general
application/myadmin/lang/zh-cn/user
application/myadmin/lang/zh-cn/web
application/myadmin/lang/zh-cn/store
application/myadmin/lang/zh-cn/ajax.php
application/myadmin/lang/zh-cn/dashboard.php
application/myadmin/lang/zh-cn/index.php
application/myadmin/lang/zh-cn.php
public/assets/js/myadmin/auth
public/assets/js/myadmin/general
public/assets/js/myadmin/user
public/assets/js/myadmin/web
public/assets/js/myadmin/store
public/assets/js/myadmin/dashboard.js
public/assets/js/myadmin/index.js
application/index/controller/Myadmin.php
application/index/controller/myadmin
application/index/lang/zh-cn/myadmin.php
application/index/lang/zh-cn/myadmin
application/index/view/myadmin
application/index/view/layout/myadmin.html
public/assets/js/frontend/myadmin.js
public/assets/js/frontend/myadmin
public/assets/js/addons/myadmin',
  'update_data' => 'ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `handrate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'手续费率\' AFTER `latitude`;
ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `taxerate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'税费率\' AFTER `handrate`;
ALTER TABLE `__PREFIX__myadmin_company` ADD COLUMN `player` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT \'\' COMMENT \'角色组\' AFTER `deletetime`;
ALTER TABLE `__PREFIX__myadmin_company_group` DROP COLUMN `type`;
ALTER TABLE `__PREFIX__myadmin_company_group` DROP COLUMN `price`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` DROP COLUMN `handingfee`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` DROP COLUMN `taxes`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` MODIFY COLUMN `money` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'提现金额\' AFTER `company_id`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `settledmoney` decimal(10, 2) NULL DEFAULT NULL COMMENT \'到账金额\' AFTER `money`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `handrate` decimal(10, 1) UNSIGNED NULL DEFAULT 0.0 COMMENT \'手续费率\' AFTER `settledmoney`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `handfee` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'手续费\' AFTER `handrate`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `taxerate` decimal(10, 1) UNSIGNED NULL DEFAULT 0.0 COMMENT \'税费率\' AFTER `handfee`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `taxefee` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'税费\' AFTER `taxerate`;
ALTER TABLE `__PREFIX__myadmin_company_withdraw` ADD COLUMN `reply` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT \'回复\' AFTER `memo`;
ALTER TABLE `__PREFIX__myadmin_user` ADD COLUMN `handrate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'手续费率\' AFTER `company_id`;
ALTER TABLE `__PREFIX__myadmin_user` ADD COLUMN `taxerate` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT \'税费率\' AFTER `handrate`;
ALTER TABLE `__PREFIX__myadmin_addon` ADD COLUMN `isuse` tinyint(2) NULL DEFAULT 1 COMMENT \'是否使用\' AFTER `config`;',
);