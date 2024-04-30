<?php

$menu = [
    [
        "name" => "fastscrm",
        "title" => "企微SCRM",
        "icon" => "fa fa-list",
        "sublist" => [
            [
                "name" => "fastscrm/system",
                "title" => "系统设置",
                "icon" => "fa fa-tasks",
                "weigh" => "10",
                "sublist" => [
                    [
                        "name" => "fastscrm/system/tasklog",
                        "title" => "同步任务日志",
                        "icon" => "fa fa-clock-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/system/tasklog/index", "title" => "查看"],
                            ["name" => "fastscrm/system/tasklog/add", "title" => "添加"],
                            ["name" => "fastscrm/system/tasklog/edit", "title" => "编辑"],
                            ["name" => "fastscrm/system/tasklog/del", "title" => "删除"],
                            ["name" => "fastscrm/system/tasklog/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "addon/config/name/fastscrm",
                        "title" => "应用配置",
                        "icon" => "fa fa-cog",
                        "weigh" => "1",
                        "sublist" => [
                            ["name" => "addon/config/name/fastscrm/index", "title" => "查看"],
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/company",
                "title" => "企业管理",
                "icon" => "fa fa-bank",
                "weigh" => "9",
                "sublist" => [
                    [
                        "name" => "fastscrm/company/depart",
                        "title" => "部门管理",
                        "icon" => "fa fa-sitemap",
                        "weigh" => "3",
                        "sublist" => [
                            ["name" => "fastscrm/company/depart/index", "title" => "查看"],
                            ["name" => "fastscrm/company/depart/add", "title" => "添加"],
                            ["name" => "fastscrm/company/depart/edit", "title" => "编辑"],
                            ["name" => "fastscrm/company/depart/del", "title" => "删除"],
                            ["name" => "fastscrm/company/depart/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/company/depart/sync", "title" => "手工同步"],
                            ["name" => "fastscrm/company/depart/start", "title" => "期初同步"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/company/worker",
                        "title" => "员工管理",
                        "icon" => "fa fa-male",
                        "weigh" => "1",
                        "sublist" => [
                            ["name" => "fastscrm/company/worker/index", "title" => "查看"],
                            ["name" => "fastscrm/company/worker/add", "title" => "添加"],
                            ["name" => "fastscrm/company/worker/edit", "title" => "编辑"],
                            ["name" => "fastscrm/company/worker/del", "title" => "删除"],
                            ["name" => "fastscrm/company/worker/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/company/worker/inviter", "title" => "邀请"],
                            ["name" => "fastscrm/company/worker/download", "title" => "下载二维码"],
                            ["name" => "fastscrm/company/worker/query", "title" => "绑定门店"],
                            ["name" => "fastscrm/company/worker/sync", "title" => "手工同步"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/company/store",
                        "title" => "门店管理",
                        "icon" => "fa fa-shopping-bag",
                        "weigh" => "2",
                        "sublist" => [
                            ["name" => "fastscrm/company/store/index", "title" => "查看"],
                            ["name" => "fastscrm/company/store/add", "title" => "添加"],
                            ["name" => "fastscrm/company/store/edit", "title" => "编辑"],
                            ["name" => "fastscrm/company/store/del", "title" => "删除"],
                            ["name" => "fastscrm/company/store/multi", "title" => "批量更新"]
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/crm",
                "title" => "客户CRM",
                "icon" => "fa fa-handshake-o",
                "weigh" => "8",
                "sublist" => [
                    [
                        "name" => "fastscrm/crm/taggroup",
                        "title" => "标签组管理",
                        "icon" => "fa fa-tags",
                        "weigh" => "6",
                        "sublist" => [
                            ["name" => "fastscrm/crm/taggroup/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/taggroup/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/taggroup/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/taggroup/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/taggroup/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/crm/tag",
                        "title" => "标签管理",
                        "icon" => "fa fa-tag",
                        "weigh" => "5",
                        "sublist" => [
                            ["name" => "fastscrm/crm/tag/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/tag/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/tag/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/tag/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/tag/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/crm/tag/sync", "title" => "手工同步"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/crm/customer",
                        "title" => "客户管理",
                        "icon" => "fa fa-user",
                        "weigh" => "4",
                        "sublist" => [
                            ["name" => "fastscrm/crm/customer/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/customer/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/customer/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/customer/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/customer/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/crm/customer/sync", "title" => "手工同步"],
                            ["name" => "fastscrm/crm/customer/addtags", "title" => "打标签"],
                            ["name" => "fastscrm/crm/customer/deltags", "title" => "删标签"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/crm/groupchat",
                        "title" => "群列表",
                        "icon" => "fa fa-users",
                        "weigh" => "2",
                        "sublist" => [
                            ["name" => "fastscrm/crm/groupchat/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/groupchat/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/groupchat/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/groupchat/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/groupchat/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/crm/groupchat/sync", "title" => "手工同步"],
                            ["name" => "fastscrm/crm/groupuser/index", "title" => "群成员查看"],
                            ["name" => "fastscrm/crm/groupadmin/index", "title" => "群管理查看"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/crm/resigned",
                        "title" => "离职继承",
                        "icon" => "fa fa-frown-o",
                        "weigh" => "1",
                        "sublist" => [
                            ["name" => "fastscrm/crm/resigned/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/resigned/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/resigned/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/resigned/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/resigned/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/crm/resigned/action", "title" => "执行任务"],
                            ["name" => "fastscrm/crm/resigned/sync", "title" => "同步客户接替状态"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/crm/onjob",
                        "title" => "在职继承",
                        "icon" => "fa fa-smile-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/crm/onjob/index", "title" => "查看"],
                            ["name" => "fastscrm/crm/onjob/add", "title" => "添加"],
                            ["name" => "fastscrm/crm/onjob/edit", "title" => "编辑"],
                            ["name" => "fastscrm/crm/onjob/del", "title" => "删除"],
                            ["name" => "fastscrm/crm/onjob/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/crm/onjob/action", "title" => "执行任务"],
                            ["name" => "fastscrm/crm/onjob/sync", "title" => "同步客户接替状态"]
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/material",
                "title" => "内容管理",
                "icon" => "fa fa-cube",
                "weigh" => "7",
                "sublist" => [
                    [
                        "name" => "fastscrm/material/group",
                        "title" => "素材分组",
                        "icon" => "fa fa-medium",
                        "weigh" => "1",
                        "sublist" => [
                            ["name" => "fastscrm/material/group/index", "title" => "查看"],
                            ["name" => "fastscrm/material/group/add", "title" => "添加"],
                            ["name" => "fastscrm/material/group/edit", "title" => "编辑"],
                            ["name" => "fastscrm/material/group/del", "title" => "删除"],
                            ["name" => "fastscrm/material/group/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/material/item",
                        "title" => "素材管理",
                        "icon" => "fa fa-modx",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/material/item/index", "title" => "查看"],
                            ["name" => "fastscrm/material/item/add", "title" => "添加"],
                            ["name" => "fastscrm/material/item/edit", "title" => "编辑"],
                            ["name" => "fastscrm/material/item/del", "title" => "删除"],
                            ["name" => "fastscrm/material/item/multi", "title" => "批量更新"]
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/report",
                "title" => "数据运营",
                "icon" => "fa fa-area-chart",
                "weigh" => "6",
                "sublist" => [
                    [
                        "name" => "fastscrm/report/groupdata",
                        "title" => "群分析",
                        "icon" => "fa fa-bar-chart-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/report/groupdata/index", "title" => "查看"],
                            ["name" => "fastscrm/report/groupdata/detail", "title" => "详情"],
                            ["name" => "fastscrm/report/groupdata/add", "title" => "添加"],
                            ["name" => "fastscrm/report/groupdata/edit", "title" => "编辑"],
                            ["name" => "fastscrm/report/groupdata/del", "title" => "删除"],
                            ["name" => "fastscrm/report/groupdata/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/report/groupcustomer",
                        "title" => "群会话统计",
                        "icon" => "fa fa-tty",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/report/groupcustomer/index", "title" => "查看"],
                            ["name" => "fastscrm/report/groupcustomer/table1", "title" => "按日期"],
                            ["name" => "fastscrm/report/groupcustomer/table2", "title" => "按员工"],
                            ["name" => "fastscrm/report/groupcustomer/add", "title" => "添加"],
                            ["name" => "fastscrm/report/groupcustomer/edit", "title" => "编辑"],
                            ["name" => "fastscrm/report/groupcustomer/del", "title" => "删除"],
                            ["name" => "fastscrm/report/groupcustomer/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/report/customerdata",
                        "title" => "客户会话统计",
                        "icon" => "fa fa-volume-control-phone",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/report/customerdata/index", "title" => "查看"],
                            ["name" => "fastscrm/report/customerdata/table1", "title" => "按日期"],
                            ["name" => "fastscrm/report/customerdata/table2", "title" => "按员工"],
                            ["name" => "fastscrm/report/customerdata/add", "title" => "添加"],
                            ["name" => "fastscrm/report/customerdata/edit", "title" => "编辑"],
                            ["name" => "fastscrm/report/customerdata/del", "title" => "删除"],
                            ["name" => "fastscrm/report/customerdata/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/report/losedata",
                        "title" => "流失客戶统计",
                        "icon" => "fa fa-frown-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/report/losedata/index", "title" => "查看"],
                            ["name" => "fastscrm/report/losedata/table1", "title" => "按日期"],
                            ["name" => "fastscrm/report/losedata/table2", "title" => "按员工"],
                            ["name" => "fastscrm/report/losedata/add", "title" => "添加"],
                            ["name" => "fastscrm/report/losedata/edit", "title" => "编辑"],
                            ["name" => "fastscrm/report/losedata/del", "title" => "删除"],
                            ["name" => "fastscrm/report/losedata/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/report/customer",
                        "title" => "客户分析",
                        "icon" => "fa fa-connectdevelop",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/report/customer/index", "title" => "查看"],
                            ["name" => "fastscrm/report/customer/table1", "title" => "按日期"],
                            ["name" => "fastscrm/report/customer/table2", "title" => "按员工"],
                            ["name" => "fastscrm/report/customer/add", "title" => "添加"],
                            ["name" => "fastscrm/report/customer/edit", "title" => "编辑"],
                            ["name" => "fastscrm/report/customer/del", "title" => "删除"],
                            ["name" => "fastscrm/report/customer/multi", "title" => "批量更新"]
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/sale",
                "title" => "社群营销",
                "icon" => "fa fa-yelp",
                "weigh" => "5",
                "sublist" => [
                    [
                        "name" => "fastscrm/sale/welcome",
                        "title" => "欢迎语",
                        "icon" => "fa fa-heart",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/sale/welcome/index", "title" => "查看"],
                            ["name" => "fastscrm/sale/welcome/add", "title" => "添加"],
                            ["name" => "fastscrm/sale/welcome/edit", "title" => "编辑"],
                            ["name" => "fastscrm/sale/welcome/del", "title" => "删除"],
                            ["name" => "fastscrm/sale/welcome/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/sale/groupmessage",
                        "title" => "客户群群发",
                        "icon" => "fa fa-users",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/sale/groupmessage/index", "title" => "查看"],
                            ["name" => "fastscrm/sale/groupmessage/recyclebin", "title" => "回收站"],
                            ["name" => "fastscrm/sale/groupmessage/add", "title" => "添加"],
                            ["name" => "fastscrm/sale/groupmessage/edit", "title" => "编辑"],
                            ["name" => "fastscrm/sale/groupmessage/del", "title" => "删除"],
                            ["name" => "fastscrm/sale/groupmessage/destroy", "title" => "真实删除"],
                            ["name" => "fastscrm/sale/groupmessage/restore", "title" => "还原"],
                            ["name" => "fastscrm/sale/groupmessage/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/sale/groupmessage/action", "title" => "执行任务"],
                            ["name" => "fastscrm/sale/chatreport/index", "title" => "效果分析"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/sale/customermessage",
                        "title" => "客户营销",
                        "icon" => "fa fa-twitch",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/sale/customermessage/index", "title" => "查看"],
                            ["name" => "fastscrm/sale/customermessage/recyclebin", "title" => "回收站"],
                            ["name" => "fastscrm/sale/customermessage/add", "title" => "添加"],
                            ["name" => "fastscrm/sale/customermessage/edit", "title" => "编辑"],
                            ["name" => "fastscrm/sale/customermessage/del", "title" => "删除"],
                            ["name" => "fastscrm/sale/customermessage/destroy", "title" => "真实删除"],
                            ["name" => "fastscrm/sale/customermessage/restore", "title" => "还原"],
                            ["name" => "fastscrm/sale/customermessage/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/sale/customermessage/action", "title" => "执行任务"],
                            ["name" => "fastscrm/sale/customerreport/index", "title" => "效果分析"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/sale/momentsmessage",
                        "title" => "朋友圈营销",
                        "icon" => "fa fa-support",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/sale/momentsmessage/index", "title" => "查看"],
                            ["name" => "fastscrm/sale/momentsmessage/recyclebin", "title" => "回收站"],
                            ["name" => "fastscrm/sale/momentsmessage/add", "title" => "添加"],
                            ["name" => "fastscrm/sale/momentsmessage/edit", "title" => "编辑"],
                            ["name" => "fastscrm/sale/momentsmessage/del", "title" => "删除"],
                            ["name" => "fastscrm/sale/momentsmessage/destroy", "title" => "真实删除"],
                            ["name" => "fastscrm/sale/momentsmessage/restore", "title" => "还原"],
                            ["name" => "fastscrm/sale/momentsmessage/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/sale/momentsmessage/action", "title" => "执行任务"],
                            ["name" => "fastscrm/sale/momentsreport/index", "title" => "效果分析"]
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/convert",
                "title" => "客户转化",
                "icon" => "fa fa-retweet",
                "weigh" => "4",
                "sublist" => [
                    [
                        "name" => "fastscrm/convert/replygroup",
                        "title" => "话术分组",
                        "icon" => "fa fa-stack-overflow",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/convert/replygroup/index", "title" => "查看"],
                            ["name" => "fastscrm/convert/replygroup/add", "title" => "添加"],
                            ["name" => "fastscrm/convert/replygroup/edit", "title" => "编辑"],
                            ["name" => "fastscrm/convert/replygroup/del", "title" => "删除"],
                            ["name" => "fastscrm/convert/replygroup/multi", "title" => "批量更新"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/convert/replyitem",
                        "title" => "话术管理",
                        "icon" => "fa fa-mixcloud",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/convert/replyitem/index", "title" => "查看"],
                            ["name" => "fastscrm/convert/replyitem/add", "title" => "添加"],
                            ["name" => "fastscrm/convert/replyitem/edit", "title" => "编辑"],
                            ["name" => "fastscrm/convert/replyitem/del", "title" => "删除"],
                            ["name" => "fastscrm/convert/replyitem/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/convert/replylog/index", "title" => "分享记录"],
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/guide",
                "title" => "引流获客",
                "icon" => "fa fa-street-view",
                "weigh" => "3",
                "sublist" => [
                    [
                        "name" => "fastscrm/guide/batch",
                        "title" => "批量添加客户",
                        "icon" => "fa fa-user-plus",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/guide/batch/index", "title" => "查看"],
                            ["name" => "fastscrm/guide/batch/add", "title" => "添加"],
                            ["name" => "fastscrm/guide/batch/edit", "title" => "编辑"],
                            ["name" => "fastscrm/guide/batch/del", "title" => "删除"],
                            ["name" => "fastscrm/guide/batch/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/guide/batch/send", "title" => "提醒员工"],
                            ["name" => "fastscrm/guide/batch/export", "title" => "下载导入模板"]
                        ]
                    ],
                    [
                        "name" => "fastscrm/guide/channelgroup",
                        "title" => "渠道码分组",
                        "icon" => "fa fa-object-group",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/guide/channelgroup/index", "title" => "查看"],
                            ["name" => "fastscrm/guide/channelgroup/add", "title" => "添加"],
                            ["name" => "fastscrm/guide/channelgroup/edit", "title" => "编辑"],
                            ["name" => "fastscrm/guide/channelgroup/del", "title" => "删除"],
                            ["name" => "fastscrm/guide/channelgroup/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/guide/channelgroup/searchfind", "title" => "分组查询"],

                        ]
                    ],
                    [
                        "name" => "fastscrm/guide/channelcode",
                        "title" => "渠道活码",
                        "icon" => "fa fa-qrcode",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/guide/channelcode/index", "title" => "查看"],
                            ["name" => "fastscrm/guide/channelcode/add", "title" => "添加"],
                            ["name" => "fastscrm/guide/channelcode/edit", "title" => "编辑"],
                            ["name" => "fastscrm/guide/channelcode/del", "title" => "删除"],
                            ["name" => "fastscrm/guide/channelcode/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/guide/channelcode/download", "title" => "下载渠道码"],

                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/risk",
                "title" => "企业风控",
                "icon" => "fa fa-connectdevelop",
                "weigh" => "2",
                "sublist" => [
                    [
                        "name" => "fastscrm/risk/interceptgroup",
                        "title" => "敏感词分组",
                        "icon" => "fa fa-assistive-listening-systems",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/risk/interceptgroup/index", "title" => "查看"],
                            ["name" => "fastscrm/risk/interceptgroup/add", "title" => "添加"],
                            ["name" => "fastscrm/risk/interceptgroup/edit", "title" => "编辑"],
                            ["name" => "fastscrm/risk/interceptgroup/del", "title" => "删除"],
                            ["name" => "fastscrm/risk/interceptgroup/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/risk/interceptgroup/searchfind", "title" => "分组查询"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/risk/intercept",
                        "title" => "敏感词管理",
                        "icon" => "fa fa-deaf",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/risk/intercept/index", "title" => "查看"],
                            ["name" => "fastscrm/risk/intercept/add", "title" => "添加"],
                            ["name" => "fastscrm/risk/intercept/edit", "title" => "编辑"],
                            ["name" => "fastscrm/risk/intercept/del", "title" => "删除"],
                            ["name" => "fastscrm/risk/intercept/multi", "title" => "批量更新"],
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/message",
                "title" => "消息中心",
                "icon" => "fa fa-bullseye",
                "weigh" => "1",
                "sublist" => [
                    [
                        "name" => "fastscrm/message/webhookgroup",
                        "title" => "机器人分组",
                        "icon" => "fa fa-slideshare",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/message/webhookgroup/index", "title" => "查看"],
                            ["name" => "fastscrm/message/webhookgroup/add", "title" => "添加"],
                            ["name" => "fastscrm/message/webhookgroup/edit", "title" => "编辑"],
                            ["name" => "fastscrm/message/webhookgroup/del", "title" => "删除"],
                            ["name" => "fastscrm/message/webhookgroup/multi", "title" => "批量更新"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/message/webhook",
                        "title" => "机器人管理",
                        "icon" => "fa fa-twitch",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/message/webhook/index", "title" => "查看"],
                            ["name" => "fastscrm/message/webhook/recyclebin", "title" => "回收站"],
                            ["name" => "fastscrm/message/webhook/add", "title" => "添加"],
                            ["name" => "fastscrm/message/webhook/edit", "title" => "编辑"],
                            ["name" => "fastscrm/message/webhook/del", "title" => "删除"],
                            ["name" => "fastscrm/message/webhook/destroy", "title" => "真实删除"],
                            ["name" => "fastscrm/message/webhook/restore", "title" => "还原"],
                            ["name" => "fastscrm/message/webhook/multi", "title" => "批量更新"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/message/former",
                        "title" => "消息模版",
                        "icon" => "fa fa-wpforms",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/message/former/index", "title" => "查看"],
                            ["name" => "fastscrm/message/former/recyclebin", "title" => "回收站"],
                            ["name" => "fastscrm/message/former/add", "title" => "添加"],
                            ["name" => "fastscrm/message/former/edit", "title" => "编辑"],
                            ["name" => "fastscrm/message/former/del", "title" => "删除"],
                            ["name" => "fastscrm/message/former/destroy", "title" => "真实删除"],
                            ["name" => "fastscrm/message/former/restore", "title" => "还原"],
                            ["name" => "fastscrm/message/former/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/message/former/searchfind", "title" => "模版查询"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/message/send",
                        "title" => "消息通知",
                        "icon" => "fa fa-volume-down",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/message/send/index", "title" => "查看"],
                            ["name" => "fastscrm/message/send/add", "title" => "添加"],
                            ["name" => "fastscrm/message/send/edit", "title" => "编辑"],
                            ["name" => "fastscrm/message/send/del", "title" => "删除"],
                            ["name" => "fastscrm/message/send/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/message/send/execute", "title" => "执行任务"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/message/sendlog",
                        "title" => "通知日志",
                        "icon" => "fa fa-clock-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/message/sendlog/index", "title" => "查看"],
                            ["name" => "fastscrm/message/sendlog/add", "title" => "添加"],
                            ["name" => "fastscrm/message/sendlog/edit", "title" => "编辑"],
                            ["name" => "fastscrm/message/sendlog/del", "title" => "删除"],
                            ["name" => "fastscrm/message/sendlog/multi", "title" => "批量更新"],
                        ]
                    ]
                ]
            ],
            [
                "name" => "fastscrm/kf",
                "title" => "客服管理",
                "icon" => "fa fa-tty",
                "weigh" => "0",
                "sublist" => [
                    [
                        "name" => "fastscrm/kf/account",
                        "title" => "客服账号",
                        "icon" => "fa fa-circle-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/kf/account/index", "title" => "查看"],
                            ["name" => "fastscrm/kf/account/add", "title" => "添加"],
                            ["name" => "fastscrm/kf/account/edit", "title" => "编辑"],
                            ["name" => "fastscrm/kf/account/del", "title" => "删除"],
                            ["name" => "fastscrm/kf/account/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/kf/account/sync", "title" => "手工同步"],
                            ["name" => "fastscrm/kf/account/getUrl", "title" => "获取链接"],
                            ["name" => "fastscrm/kf/account/searchfind", "title" => "账号查询"],
                        ]
                    ],
                    [
                        "name" => "fastscrm/kf/servicer",
                        "title" => "接待人员",
                        "icon" => "fa fa-circle-o",
                        "weigh" => "0",
                        "sublist" => [
                            ["name" => "fastscrm/kf/servicer/index", "title" => "查看"],
                            ["name" => "fastscrm/kf/servicer/add", "title" => "添加"],
                            ["name" => "fastscrm/kf/servicer/edit", "title" => "编辑"],
                            ["name" => "fastscrm/kf/servicer/del", "title" => "删除"],
                            ["name" => "fastscrm/kf/servicer/multi", "title" => "批量更新"],
                            ["name" => "fastscrm/kf/servicer/sync", "title" => "手工同步"],
                        ]
                    ],
                ]
            ]
        ]
    ]

];
return $menu;