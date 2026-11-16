<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => '返回首页',
    'search' => '站内搜索',
    'search_button' => '搜索',
    '403' => [
        'title' => '拒绝访问',
        'message' => '您没有打开此页面的权限。',
    ],
    '404' => [
        'title' => '页面未找到',
        'message' => '地址可能输入有误，或者页面已被移动或删除。',
    ],
    '419' => [
        'title' => '页面已过期',
        'message' => '页面打开时间过长。请返回、刷新后重试。',
    ],
    '429' => [
        'title' => '请求过多',
        'message' => '请稍等一分钟后重试。',
    ],
    '500' => [
        'title' => '出错了',
        'message' => '错误已被记录。请几分钟后重试。',
    ],
    '503' => [
        'title' => '马上回来',
        'message' => '网站正在更新，请几分钟后再来。',
    ],
    '4xx' => [
        'title' => '请求无法完成',
        'message' => '请检查地址，或从首页开始。',
    ],
    '5xx' => [
        'title' => '服务器无法响应',
        'message' => '请几分钟后重试。',
    ],
];
