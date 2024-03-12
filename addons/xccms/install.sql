/*==============================================================*/
/* Table: __PREFIX__xccms_page_info                                    */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_page_info
(
   id                   int not null auto_increment,
   title                varchar(255) comment '标题',
   content              text comment '内容',
   visits               int default 0 comment '访问量',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_page_info comment '单页管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_menu_info                                    */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_menu_info
(
   id                   int not null auto_increment,
   parent_id            int default 0 comment '父级id',
   name                 varchar(50) comment '栏目名称',
   en_name              varchar(50) comment '英文名称',
   menu_type            varchar(50) comment '栏目类型',
   menu_object_id       int comment '菜单绑定对象',
   url                  varchar(255) comment '外部链接',
   is_top_show          tinyint(1) default 1 comment '顶部导航显示',
   is_bottom_show       tinyint(1) default 1 comment '底部导航显示',
   state                tinyint(1) default 0 comment '状态',
   weigh                int comment '权重',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_menu_info comment '菜单管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_content_category                             */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_content_category
(
   id                   int not null auto_increment,
   parent_id            int default 0 comment '父级',
   name                 varchar(50) comment '栏目名称',
   is_recommend         tinyint(1) default 0 comment '推荐',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   state                tinyint(1) default 0 comment '状态',
   weigh                int comment '权重',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_content_category comment '内容栏目管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_content_info                                 */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_content_info
(
   id                   int not null auto_increment,
   category_id          int comment '栏目ID',
   title                varchar(255) comment '标题',
   is_recommend         tinyint(1) default 0 comment '推荐',
   list_image           varchar(100) comment '列表图',
   description          varchar(500) comment '描述',
   content              text comment '内容',
   visits               int default 0 comment '访问量',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   weigh                int default 0 comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_content_info comment '内容管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_product_category                             */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_product_category
(
   id                   int not null auto_increment,
   parent_id            int default 0 comment '父级',
   name                 varchar(50) comment '栏目名称',
   is_recommend         tinyint(1) default 0 comment '推荐',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   state                tinyint(1) default 0 comment '状态',
   weigh                int comment '权重',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_product_category comment '产品栏目管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_product_info                                 */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_product_info
(
   id                   int not null auto_increment,
   category_id          int comment '栏目ID',
   title                varchar(255) comment '标题',
   is_recommend         tinyint(1) default 0 comment '推荐',
   list_image           varchar(100) comment '列表图',
   banners              varchar(500) comment '轮播图',
   summary              varchar(2000) comment '产品参数',
   price                decimal(9,2) comment '价格',
   description          varchar(500) comment '描述',
   content              text comment '内容',
   visits               int default 0 comment '访问量',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   weigh                int default 0 comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_product_info comment '产品管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_friend_link                                  */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_friend_link
(
   id                   int not null auto_increment,
   name                 varchar(50) comment '名称',
   url                  varchar(255) comment 'url',
   weigh                int comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby           int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_friend_link comment '友情链接管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_website_carousel                             */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_website_carousel
(
   id                   int not null auto_increment,
   carousel_type        int comment '轮播类型',
   title                varchar(50) comment '标题',
   list_image           varchar(100) comment '列表图',
   weigh                int comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby           int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_website_carousel comment '站点轮播管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_guestbook                                    */
/*==============================================================*/
create table __PREFIX__xccms_guestbook
(
   id                   int not null auto_increment,
   guest_book_type      varchar(50) comment '类型',
   resource_id          int comment '资源ID',
   realname             varchar(50) comment '姓名',
   tel                  varchar(50) comment '联系电话',
   email                varchar(50) comment '邮箱',
   content              varchar(500) comment '内容',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   primary key (id)
);

alter table __PREFIX__xccms_guestbook comment '留言板管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_partner_link                                 */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_partner_link
(
   id                   int not null auto_increment,
   name                 varchar(50) comment '名称',
   url                  varchar(255) comment 'url',
   list_image           varchar(100) comment '列表图',
   weigh                int comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby           int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_partner_link comment '合作伙伴管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_job_info                                     */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_job_info
(
   id                   int not null auto_increment,
   name                 varchar(50) comment '名称',
   list_image           varchar(100) comment '列表图',
   description          varchar(500) comment '描述',
   content              text comment '内容',
   visits               int default 0 comment '访问量',
   weigh                int comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby           int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_job_info comment '招聘职位管理';



/*==============================================================*/
/* Table: __PREFIX__xccms_news_info                                     */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_news_info
(
   id                   int not null auto_increment,
   title                varchar(255) comment '标题',
   list_image           varchar(100) comment '列表图',
   description          varchar(500) comment '描述',
   content              text comment '内容',
   visits               int default 0 comment '访问量',
   seo_title            varchar(255) comment 'seo标题',
   seo_keywords         varchar(255) comment 'seo关键词',
   seo_description      varchar(500) comment 'seo描述',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_news_info comment '新闻管理';


create table IF NOT EXISTS __PREFIX__xccms_site_config
(
   id                   int not null auto_increment,
   json_data            text comment '配置json',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_site_config comment '站点配置';


/*==============================================================*/
/* Table: __PREFIX__xccms_faq                                          */
/*==============================================================*/
create table IF NOT EXISTS __PREFIX__xccms_faq
(
   id                   int not null auto_increment,
   question             varchar(255) comment '问题',
   answer               varchar(2000) comment '回答',
   weigh                int comment '权重',
   state                tinyint(1) default 0 comment '状态',
   createtime           bigint(16) comment '创建时间',
   creator              int comment '创建人',
   updatetime           bigint(16) comment '最后更新时间',
   updatedby            int comment '最后更新人',
   primary key (id)
);

alter table __PREFIX__xccms_faq comment 'FAQ管理';
