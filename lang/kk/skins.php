<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Скиндердің мәтіндері (config atrium-theme.skin). Мазмұнның өзі
// модульдерден алынады; мұнда оның айналасындағы тақырыптар мен жазулар.
return [
    'rating' => '5-тен :rating',
    'review' => 'Пікір',

    'travel' => [
        'modules' => ['catalog' => 'Турлар', 'news' => 'Жаңалықтар', 'stores' => 'Кеңселер', 'faq' => 'Сұрақтар'],
        'price' => ':price бастап',
        'eyebrow' => 'Елімізде және шетелде турлар',
        'catalog_title' => 'Биыл жазда қайда барады',
        'catalog_all' => 'Барлық бағыттар',
        'reviews_title' => 'Саяхатшылар не дейді',
        'faq_title' => 'Жиі қойылатын сұрақтар',
        'callback_title' => 'Лайықты тур таппадыңыз ба?',
        'callback_text' => 'Нөміріңізді қалдырыңыз, біз жұмыс уақытында бір сағат ішінде қоңырау шаламыз.',
        'tagline' => 'туристік агенттік',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Мәзір', 'news' => 'Іс-шаралар', 'stores' => 'Мекенжайлар', 'faq' => 'Сұрақтар'],
        'price' => ':price',
        'eyebrow' => 'Үй тағамдары',
        'hero_menu' => 'Мәзірді қарау',
        'menu_title' => 'Мәзір',
        'menu_text' => 'Оны әр маусым сайын жаңартамыз, күн тағамдары кіреберістегі тақтада.',
        'stores_title' => 'Бізді қайдан табуға болады',
        'directions' => 'Қалай жетуге болады',
        'booking_title' => 'Үстел брондау',
        'booking_text' => 'Броньды қоңырау шалып немесе хабарлама арқылы растаймыз.',
        'book' => 'Үстел брондау',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Қызметтер', 'news' => 'Жаңалықтар', 'stores' => 'Филиалдар', 'faq' => 'Сұрақтар'],
        'price' => ':price бастап',
        'services_title' => 'Қызметтер',
        'services_all' => 'Барлық қызметтер',
        'doctors_all' => 'Барлық дәрігерлер',
        'news_title' => 'Клиника жаңалықтары',
        'faq_title' => 'Сұрақтар мен жауаптар',
        'book' => 'Қабылдауға жазылу',
        'disclaimer' => 'Кез келген емдеуден бұрын маманмен кеңесіңіз.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Каталог', 'news' => 'Жаңалықтар', 'stores' => 'Дүкендер', 'faq' => 'Көмек'],
        'price' => ':price',
        'eyebrow' => 'Жаңа топтама',
        'catalog_button' => 'Каталогты қарау',
        'products_title' => 'Тауарлар',
        'sections' => 'Каталог бөлімдері',
        'all' => 'Барлығы',
        'brands_title' => 'Брендтер',
        'stores_title' => 'Дүкендер',
        'tagline' => 'Үйге арналған заттар',
    ],
];
