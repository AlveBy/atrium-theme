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

    'atrium' => [
        'modules' => ['catalog' => 'Katalog'],
        'price' => ':price',
        'in_stock' => 'Dostępny',
        'out_of_stock' => 'Niedostępny',
        'hero_title' => 'Cała strona z jednego panelu',
        'hero_text' => 'Produkty, aktualności, sklepy, pytania i formularze: każda strona jest tu zarządzana w Atrium, w 15 językach.',
        'hero_catalog' => 'Przeglądaj katalog',
        'hero_stores' => 'Znajdź sklep',
        'brands_title' => 'Marki w ofercie',
        'sections_title' => 'Kupuj według działów',
        'products_title' => 'Nowości w katalogu',
        'products_all' => 'Wszystkie produkty',
        'news_title' => 'Najnowsze aktualności',
        'news_all' => 'Wszystkie aktualności',
        'stores_title' => 'Odwiedź sklep',
        'stores_all' => 'Wszystkie sklepy',
        'team_title' => 'Ludzie, którzy za tym stoją',
        'team_all' => 'Poznaj zespół',
        'reviews_title' => 'Co mówią klienci',
        'faq_title' => 'Pytania i odpowiedzi',
        'contact_title' => 'Napisz do nas',
        'contact_text' => 'Zostaw swoje dane, a odezwiemy się w ciągu jednego dnia roboczego.',
        'count' => 'Razem: :count',
        'sections' => 'Działy katalogu',
        'filters' => 'Filtry',
        'filter_brand' => 'Marka',
        'filter_all' => 'Wszystkie',
        'filter_stock' => 'Tylko dostępne',
        'sort' => 'Sortuj według',
        'sort_default' => 'Polecane',
        'sort_cheap' => 'Cena: od najniższej',
        'sort_dear' => 'Cena: od najwyższej',
        'shown' => 'Pokazano :shown z :total',
        'filter_none' => 'Żaden produkt nie pasuje do tych filtrów. Wybierz inną markę lub wyłącz filtr dostępności.',
        'map_show' => 'Pokaż mapę',
        'map_open' => 'Otwórz w OpenStreetMap',
        'map_title' => 'Mapa: :store',
        'tagline' => 'Demo panelu administracyjnego',
        'footer_menu' => 'Strona',
        'contact_footer' => 'Kontakt',
        'footer_note' => 'Strona demo zbudowana w Atrium.',
        'photo' => 'Zdjęcie :number',
        'order' => 'Zamów ten produkt',
        'back_to_catalog' => 'Wróć do katalogu',
        'available_in' => 'Dostępny w',
        'description' => 'O produkcie',
        'details' => 'Szczegóły',
        'related' => 'Dobrze pasuje do',
    ],
];
