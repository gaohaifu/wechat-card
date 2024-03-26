CREATE TABLE IF NOT EXISTS `__PREFIX__posters` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `title` varchar(50) NOT NULL COMMENT '名称',
    `config` text NOT NULL COMMENT '配置',
    `create_time` int(11) NOT NULL DEFAULT 0 COMMENT '创建时间',
    `update_time` int(11) NOT NULL DEFAULT 0 COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='自定义海报配置';

INSERT INTO `__PREFIX__posters` (`id`, `title`, `config`, `create_time`, `update_time`) VALUES (1, '自定义海报', '{\"bg\":{\"color\":\"rgb(255, 255, 255)\",\"width\":424,\"height\":745},\"materials\":[{\"type\":\"image\",\"generate\":true,\"zIndex\":1,\"config\":{\"image\":null,\"left\":0,\"top\":0,\"width\":424,\"height\":423,\"radius\":0,\"opacity\":100}},{\"type\":\"qr\",\"generate\":true,\"zIndex\":2,\"config\":{\"text\":\"https:\\/\\/baidu.com\\/s?wd={:id}\",\"left\":18,\"top\":518,\"width\":112,\"margin\":6,\"opacity\":100}},{\"type\":\"text\",\"generate\":true,\"zIndex\":3,\"config\":{\"text\":\"我是{:name},\\n我爱{:title}\",\"left\":149,\"top\":525,\"width\":275,\"fontSize\":20,\"lineHeight\":50,\"overflow\":\"ellipsis\",\"overflow_text\":\"\",\"color\":\"rgba(0, 0, 0, 1)\"}},{\"type\":\"text\",\"generate\":true,\"zIndex\":4,\"config\":{\"text\":\"自定义文本\",\"left\":20,\"top\":669,\"width\":235,\"fontSize\":20,\"lineHeight\":20,\"overflow\":\"space\",\"overflow_text\":\"\",\"color\":\"rgba(0, 0, 0, 1)\"}}]}', 1630043327, 1661695691);

CREATE TABLE IF NOT EXISTS `__PREFIX__posters_record` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
    `name` varchar(50) NOT NULL COMMENT '名称',
    `size` double(6,2) NOT NULL DEFAULT '1.00' COMMENT '显示比例',
    `posters_id` int NOT NULL,
    `params` text NOT NULL COMMENT '配置',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 ;

INSERT INTO `__PREFIX__posters_record` (`id`, `posters_id`, `params`, `name`, `size`) VALUES (1, 1, '{\"text_3\":\"替换全部文本消息\",\"text_2\":\"name=啦啦啦\\ntitle=写代码\",\"qr_1\":\"id=5\",\"image_0\":\"\\/assets\\/addons\\/posters\\/img\\/image.png\"}', '海报 - 啦啦啦', 1.50);

