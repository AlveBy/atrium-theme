<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Тексти скінів (config atrium-theme.skin). Сам вміст береться з
// модулів; тут заголовки та підписи навколо нього.
return [
    'rating' => ':rating з 5',
    'review' => 'Відгук',

    'travel' => [
        'modules' => ['catalog' => 'Тури', 'news' => 'Новини', 'stores' => 'Офіси', 'faq' => 'Запитання'],
        'price' => 'від :price',
        'eyebrow' => 'Тури Україною та за кордон',
        'catalog_title' => 'Куди їдуть цього літа',
        'catalog_all' => 'Усі напрямки',
        'reviews_title' => 'Що кажуть мандрівники',
        'faq_title' => 'Часті запитання',
        'callback_title' => 'Не знайшли потрібний тур?',
        'callback_text' => 'Залиште номер телефону, і ми зателефонуємо протягом години в робочий час.',
        'tagline' => 'туристичне агентство',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Меню', 'news' => 'Події', 'stores' => 'Адреси', 'faq' => 'Запитання'],
        'price' => ':price',
        'eyebrow' => 'Домашня кухня',
        'hero_menu' => 'Переглянути меню',
        'menu_title' => 'Меню',
        'menu_text' => 'Змінюємо його щосезону, страви дня — на дошці біля входу.',
        'stores_title' => 'Де нас знайти',
        'directions' => 'Як дістатися',
        'booking_title' => 'Забронювати столик',
        'booking_text' => 'Підтвердимо бронювання телефоном або повідомленням.',
        'book' => 'Забронювати столик',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Послуги', 'news' => 'Новини', 'stores' => 'Філії', 'faq' => 'Запитання'],
        'price' => 'від :price',
        'services_title' => 'Послуги',
        'services_all' => 'Усі послуги',
        'doctors_all' => 'Усі лікарі',
        'news_title' => 'Новини клініки',
        'faq_title' => 'Запитання та відповіді',
        'book' => 'Записатися на прийом',
        'disclaimer' => 'Перед будь-яким лікуванням зверніться до фахівця.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Каталог', 'news' => 'Новини', 'stores' => 'Магазини', 'faq' => 'Допомога'],
        'price' => ':price',
        'eyebrow' => 'Нова колекція',
        'catalog_button' => 'Переглянути каталог',
        'products_title' => 'Товари',
        'sections' => 'Розділи каталогу',
        'all' => 'Усе',
        'brands_title' => 'Бренди',
        'stores_title' => 'Магазини',
        'tagline' => 'Речі для дому',
    ],
];
