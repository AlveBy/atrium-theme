<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// 皮肤文案（config atrium-theme.skin）。内容本身来自各模块，
// 这里是围绕内容的标题和标签。
return [
    'rating' => ':rating 分（满分 5 分）',
    'review' => '评价',

    'travel' => [
        'modules' => ['catalog' => '旅游线路', 'news' => '新闻', 'stores' => '门店', 'faq' => '常见问题'],
        'price' => ':price 起',
        'eyebrow' => '国内外旅游线路',
        'catalog_title' => '今夏大家去哪儿',
        'catalog_all' => '全部目的地',
        'reviews_title' => '旅客评价',
        'faq_title' => '常见问题',
        'callback_title' => '没找到合适的线路？',
        'callback_text' => '留下您的号码，我们会在营业时间内一小时内给您回电。',
        'tagline' => '旅行社',
    ],

    'restaurant' => [
        'modules' => ['catalog' => '菜单', 'news' => '活动', 'stores' => '门店', 'faq' => '常见问题'],
        'price' => ':price',
        'eyebrow' => '地道家常菜',
        'hero_menu' => '查看菜单',
        'menu_title' => '菜单',
        'menu_text' => '菜单每季更换，当日特色菜写在门口的黑板上。',
        'stores_title' => '如何找到我们',
        'directions' => '交通指引',
        'booking_title' => '预订餐位',
        'booking_text' => '我们会通过电话或消息确认您的预订。',
        'book' => '预订餐位',
    ],

    'clinic' => [
        'modules' => ['catalog' => '诊疗服务', 'news' => '新闻', 'stores' => '分院', 'faq' => '常见问题'],
        'price' => ':price 起',
        'services_title' => '诊疗服务',
        'services_all' => '全部服务',
        'doctors_all' => '全部医生',
        'news_title' => '诊所动态',
        'faq_title' => '常见问答',
        'book' => '预约就诊',
        'disclaimer' => '接受任何治疗前，请先咨询专业医生。',
    ],

    'shop' => [
        'modules' => ['catalog' => '商品目录', 'news' => '新闻', 'stores' => '门店', 'faq' => '帮助'],
        'price' => ':price',
        'eyebrow' => '新品系列',
        'catalog_button' => '浏览商品目录',
        'products_title' => '商品',
        'sections' => '商品分类',
        'all' => '全部',
        'brands_title' => '品牌',
        'stores_title' => '门店',
        'tagline' => '居家好物',
    ],

    'atrium' => [
        'modules' => ['catalog' => '商品目录'],
        'price' => ':price',
        'in_stock' => '有货',
        'out_of_stock' => '缺货',
        'hero_title' => '整个网站，一个后台搞定',
        'hero_text' => '商品、新闻、门店、常见问题和表单：这里的每个页面都由 Atrium 管理，支持 15 种语言。',
        'hero_catalog' => '浏览商品目录',
        'hero_stores' => '查找门店',
        'brands_title' => '在售品牌',
        'sections_title' => '按分类选购',
        'products_title' => '目录新品',
        'products_all' => '全部商品',
        'news_title' => '最新新闻',
        'news_all' => '全部新闻',
        'stores_title' => '走进门店',
        'stores_all' => '全部门店',
        'team_title' => '背后的团队',
        'team_all' => '认识团队',
        'reviews_title' => '顾客评价',
        'faq_title' => '常见问题',
        'contact_title' => '联系我们',
        'contact_text' => '留下您的信息，我们会在一个工作日内回复。',
        'count' => '共 :count 项',
        'sections' => '商品分类',
        'filters' => '筛选',
        'filter_brand' => '品牌',
        'filter_all' => '全部',
        'filter_stock' => '仅看有货',
        'sort' => '排序方式',
        'sort_default' => '推荐',
        'sort_cheap' => '价格从低到高',
        'sort_dear' => '价格从高到低',
        'shown' => '共 :total 件，显示 :shown 件',
        'filter_none' => '没有符合这些筛选条件的商品。请换一个品牌，或关闭“仅看有货”。',
        'map_show' => '显示地图',
        'map_open' => '在 OpenStreetMap 中打开',
        'map_title' => '地图：:store',
        'tagline' => '管理后台演示',
        'footer_menu' => '网站',
        'contact_footer' => '联系方式',
        'footer_note' => '由 Atrium 搭建的演示网站。',
        'photo' => '照片 :number',
        'order' => '订购此商品',
        'back_to_catalog' => '返回商品目录',
        'available_in' => '销售门店',
        'description' => '商品介绍',
        'details' => '详细信息',
        'related' => '搭配推荐',
    ],
];
