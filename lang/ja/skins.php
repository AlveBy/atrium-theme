<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// スキンの文言（config atrium-theme.skin）。コンテンツ自体は
// モジュールから取得され、ここにはその周囲の見出しやラベルを置く。
return [
    'rating' => ':rating点（5点満点）',
    'review' => 'レビュー',

    'travel' => [
        'modules' => ['catalog' => 'ツアー', 'news' => 'ニュース', 'stores' => '店舗', 'faq' => 'ご質問'],
        'price' => ':priceから',
        'eyebrow' => '国内・海外ツアー',
        'catalog_title' => '今年の夏、みんなが行く先',
        'catalog_all' => 'すべての目的地',
        'reviews_title' => 'お客様の声',
        'faq_title' => 'よくあるご質問',
        'callback_title' => 'お探しのツアーが見つかりませんか？',
        'callback_text' => 'お電話番号をお知らせください。営業時間内に1時間以内にご連絡いたします。',
        'tagline' => '旅行代理店',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'メニュー', 'news' => 'イベント', 'stores' => '店舗', 'faq' => 'ご質問'],
        'price' => ':price',
        'eyebrow' => '家庭の味',
        'hero_menu' => 'メニューを見る',
        'menu_title' => 'メニュー',
        'menu_text' => '季節ごとに入れ替えます。本日のおすすめは入口の黒板に書いてあります。',
        'stores_title' => 'お店の場所',
        'directions' => 'アクセス方法',
        'booking_title' => 'テーブルを予約',
        'booking_text' => 'ご予約はお電話またはメッセージで確認いたします。',
        'book' => 'テーブルを予約する',
    ],

    'clinic' => [
        'modules' => ['catalog' => '診療案内', 'news' => 'ニュース', 'stores' => '医院', 'faq' => 'ご質問'],
        'price' => ':priceから',
        'services_title' => '診療案内',
        'services_all' => 'すべての診療',
        'doctors_all' => 'すべての医師',
        'news_title' => '医院からのお知らせ',
        'faq_title' => 'ご質問と回答',
        'book' => '診察を予約する',
        'disclaimer' => '治療を受ける前に、必ず専門医にご相談ください。',
    ],

    'shop' => [
        'modules' => ['catalog' => 'カタログ', 'news' => 'ニュース', 'stores' => '店舗', 'faq' => 'ヘルプ'],
        'price' => ':price',
        'eyebrow' => '新コレクション',
        'catalog_button' => 'カタログを見る',
        'products_title' => '商品',
        'sections' => 'カテゴリー',
        'all' => 'すべて',
        'brands_title' => 'ブランド',
        'stores_title' => '店舗',
        'tagline' => '暮らしの雑貨',
    ],
];
