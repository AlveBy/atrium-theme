<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Тексты скинов (config atrium-theme.skin). Само содержимое берётся из
// модулей; здесь заголовки и подписи вокруг него.
return [
    'rating' => ':rating из 5',
    'review' => 'Отзыв',

    'travel' => [
        'modules' => ['catalog' => 'Туры', 'news' => 'Новости', 'stores' => 'Офисы', 'faq' => 'Вопросы'],
        'price' => 'от :price',
        'eyebrow' => 'Туры по России и за границу',
        'catalog_title' => 'Куда едут этим летом',
        'catalog_all' => 'Все направления',
        'reviews_title' => 'Что говорят после поездки',
        'faq_title' => 'Частые вопросы',
        'callback_title' => 'Не нашли подходящий тур?',
        'callback_text' => 'Оставьте телефон, перезвоним в течение часа в рабочее время.',
        'tagline' => 'туристическое агентство',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Меню', 'news' => 'События', 'stores' => 'Адреса', 'faq' => 'Вопросы'],
        'price' => ':price',
        'eyebrow' => 'Ресторан домашней кухни',
        'hero_menu' => 'Смотреть меню',
        'menu_title' => 'Меню',
        'menu_text' => 'Меняем раз в сезон, блюда дня — на доске у входа.',
        'stores_title' => 'Где нас найти',
        'directions' => 'Как добраться',
        'booking_title' => 'Забронировать стол',
        'booking_text' => 'Подтвердим бронь звонком или сообщением.',
        'book' => 'Забронировать стол',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Услуги', 'news' => 'Новости', 'stores' => 'Филиалы', 'faq' => 'Вопросы'],
        'price' => 'от :price',
        'services_title' => 'Услуги',
        'services_all' => 'Все услуги',
        'doctors_all' => 'Все врачи',
        'news_title' => 'Новости центра',
        'faq_title' => 'Вопросы и ответы',
        'book' => 'Записаться',
        'disclaimer' => 'Есть противопоказания, нужна консультация специалиста.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Каталог', 'news' => 'Новости', 'stores' => 'Магазины', 'faq' => 'Покупателям'],
        'price' => ':price',
        'eyebrow' => 'Новая коллекция',
        'catalog_button' => 'Смотреть каталог',
        'products_title' => 'Товары',
        'sections' => 'Разделы каталога',
        'all' => 'Всё',
        'brands_title' => 'Бренды',
        'stores_title' => 'Магазины',
        'tagline' => 'Магазин вещей для дома',
    ],
];
