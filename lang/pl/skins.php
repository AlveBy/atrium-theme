<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Teksty skórek (config atrium-theme.skin). Sama treść pochodzi z
// modułów; tutaj są nagłówki i etykiety wokół niej.
return [
    'rating' => ':rating z 5',
    'review' => 'Opinia',

    'travel' => [
        'modules' => ['catalog' => 'Wycieczki', 'news' => 'Aktualności', 'stores' => 'Biura', 'faq' => 'Pytania'],
        'price' => 'od :price',
        'eyebrow' => 'Wycieczki po kraju i za granicę',
        'catalog_title' => 'Dokąd jadą tego lata',
        'catalog_all' => 'Wszystkie kierunki',
        'reviews_title' => 'Co mówią podróżni',
        'faq_title' => 'Najczęstsze pytania',
        'callback_title' => 'Nie znalazłeś odpowiedniej wycieczki?',
        'callback_text' => 'Zostaw numer, a oddzwonimy w ciągu godziny w godzinach pracy biura.',
        'tagline' => 'biuro podróży',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Menu', 'news' => 'Wydarzenia', 'stores' => 'Lokalizacje', 'faq' => 'Pytania'],
        'price' => ':price',
        'eyebrow' => 'Domowa kuchnia',
        'hero_menu' => 'Zobacz menu',
        'menu_title' => 'Menu',
        'menu_text' => 'Zmieniamy je co sezon, dania dnia znajdziesz na tablicy przy wejściu.',
        'stores_title' => 'Gdzie nas znajdziesz',
        'directions' => 'Jak dojechać',
        'booking_title' => 'Zarezerwuj stolik',
        'booking_text' => 'Potwierdzimy rezerwację telefonicznie lub wiadomością.',
        'book' => 'Zarezerwuj stolik',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Usługi', 'news' => 'Aktualności', 'stores' => 'Oddziały', 'faq' => 'Pytania'],
        'price' => 'od :price',
        'services_title' => 'Usługi',
        'services_all' => 'Wszystkie usługi',
        'doctors_all' => 'Wszyscy lekarze',
        'news_title' => 'Aktualności z kliniki',
        'faq_title' => 'Pytania i odpowiedzi',
        'book' => 'Umów wizytę',
        'disclaimer' => 'Przed rozpoczęciem leczenia skonsultuj się ze specjalistą.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Katalog', 'news' => 'Aktualności', 'stores' => 'Sklepy', 'faq' => 'Pomoc'],
        'price' => ':price',
        'eyebrow' => 'Nowa kolekcja',
        'catalog_button' => 'Przeglądaj katalog',
        'products_title' => 'Produkty',
        'sections' => 'Działy katalogu',
        'all' => 'Wszystko',
        'brands_title' => 'Marki',
        'stores_title' => 'Sklepy',
        'tagline' => 'Rzeczy do domu',
    ],
];
