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

    'atrium' => [
        'modules' => ['catalog' => 'Каталог'],
        'price' => ':price',
        'in_stock' => 'В наявності',
        'out_of_stock' => 'Немає в наявності',
        'hero_title' => 'Весь сайт — з однієї панелі',
        'hero_text' => 'Товари, новини, магазини, запитання та форми: кожна сторінка тут керується в Atrium, 15 мовами.',
        'hero_catalog' => 'Переглянути каталог',
        'hero_stores' => 'Знайти магазин',
        'brands_title' => 'Бренди в асортименті',
        'sections_title' => 'Покупки за розділами',
        'products_title' => 'Нове в каталозі',
        'products_all' => 'Усі товари',
        'news_title' => 'Останні новини',
        'news_all' => 'Усі новини',
        'stores_title' => 'Завітайте до магазину',
        'stores_all' => 'Усі магазини',
        'team_title' => 'Люди за проєктом',
        'team_all' => 'Познайомитися з командою',
        'reviews_title' => 'Що кажуть покупці',
        'faq_title' => 'Запитання та відповіді',
        'contact_title' => 'Напишіть нам',
        'contact_text' => 'Залиште свої дані, і ми відповімо протягом одного робочого дня.',
        'count' => 'Усього: :count',
        'sections' => 'Розділи каталогу',
        'filters' => 'Фільтри',
        'filter_brand' => 'Бренд',
        'filter_all' => 'Усі',
        'filter_stock' => 'Лише в наявності',
        'sort' => 'Сортування',
        'sort_default' => 'Рекомендовані',
        'sort_cheap' => 'Ціна: від меншої до більшої',
        'sort_dear' => 'Ціна: від більшої до меншої',
        'shown' => 'Показано :shown з :total',
        'filter_none' => 'За цими фільтрами нічого не знайдено. Спробуйте інший бренд або вимкніть фільтр наявності.',
        'map_show' => 'Показати карту',
        'map_open' => 'Відкрити в OpenStreetMap',
        'map_title' => 'Карта: :store',
        'tagline' => 'Демо адмін-панелі',
        'footer_menu' => 'Сайт',
        'contact_footer' => 'Контакти',
        'footer_note' => 'Демосайт, створений на Atrium.',
        'photo' => 'Фото :number',
        'order' => 'Замовити товар',
        'back_to_catalog' => 'Назад до каталогу',
        'available_in' => 'Доступно в',
        'description' => 'Про товар',
        'details' => 'Деталі',
        'related' => 'Добре поєднується з',
    ],
];
