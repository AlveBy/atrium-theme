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

    'atrium' => [
        'modules' => ['catalog' => 'Каталог'],
        'price' => ':price',
        'in_stock' => 'Қолда бар',
        'out_of_stock' => 'Қолда жоқ',
        'hero_title' => 'Бүкіл сайт — бір панельден',
        'hero_text' => 'Тауарлар, жаңалықтар, дүкендер, сұрақтар мен нысандар: мұндағы әр бет Atrium-да басқарылады, 15 тілде.',
        'hero_catalog' => 'Каталогты қарау',
        'hero_stores' => 'Дүкенді табу',
        'brands_title' => 'Біздегі брендтер',
        'sections_title' => 'Бөлімдер бойынша сатып алу',
        'products_title' => 'Каталогтағы жаңалар',
        'products_all' => 'Барлық тауарлар',
        'news_title' => 'Соңғы жаңалықтар',
        'news_all' => 'Барлық жаңалықтар',
        'stores_title' => 'Дүкенге кіріңіз',
        'stores_all' => 'Барлық дүкендер',
        'team_title' => 'Жобаның артындағы адамдар',
        'team_all' => 'Топпен танысу',
        'reviews_title' => 'Сатып алушылар не дейді',
        'faq_title' => 'Сұрақ-жауап',
        'contact_title' => 'Бізбен хабарласыңыз',
        'contact_text' => 'Деректеріңізді қалдырыңыз, біз бір жұмыс күні ішінде жауап береміз.',
        'count' => 'Барлығы: :count',
        'sections' => 'Каталог бөлімдері',
        'filters' => 'Сүзгілер',
        'filter_brand' => 'Бренд',
        'filter_all' => 'Барлығы',
        'filter_stock' => 'Тек қолда барлары',
        'sort' => 'Сұрыптау',
        'sort_default' => 'Ұсынылатындар',
        'sort_cheap' => 'Баға: арзаннан қымбатқа',
        'sort_dear' => 'Баға: қымбаттан арзанға',
        'shown' => ':total ішінен :shown көрсетілді',
        'filter_none' => 'Бұл сүзгілер бойынша ештеңе табылмады. Басқа брендті таңдаңыз немесе қолда барын сүзуді өшіріңіз.',
        'map_show' => 'Картаны көрсету',
        'map_open' => 'OpenStreetMap-та ашу',
        'map_title' => 'Карта: :store',
        'tagline' => 'Әкімші панелінің демосы',
        'footer_menu' => 'Сайт',
        'contact_footer' => 'Байланыс',
        'footer_note' => 'Atrium-да жасалған демо сайт.',
        'photo' => 'Фото :number',
        'order' => 'Тауарға тапсырыс беру',
        'back_to_catalog' => 'Каталогқа оралу',
        'available_in' => 'Мына жерде бар',
        'description' => 'Тауар туралы',
        'details' => 'Мәліметтер',
        'related' => 'Мынаған жарасады',
    ],
];
