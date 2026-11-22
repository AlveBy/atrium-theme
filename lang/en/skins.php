<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Texts of the skins (config atrium-theme.skin). The content itself comes
// from the modules; these are the headings and labels around it.
return [
    'rating' => ':rating out of 5',
    'review' => 'Review',

    'travel' => [
        'modules' => ['catalog' => 'Tours', 'news' => 'News', 'stores' => 'Offices', 'faq' => 'Questions'],
        'price' => 'from :price',
        'eyebrow' => 'Tours at home and abroad',
        'catalog_title' => 'Where people go this summer',
        'catalog_all' => 'All destinations',
        'reviews_title' => 'What travellers say',
        'faq_title' => 'Frequently asked questions',
        'callback_title' => 'Didn’t find the right tour?',
        'callback_text' => 'Leave your number and we will call you back within an hour during office hours.',
        'tagline' => 'travel agency',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Menu', 'news' => 'Events', 'stores' => 'Locations', 'faq' => 'Questions'],
        'price' => ':price',
        'eyebrow' => 'Home-style cooking',
        'hero_menu' => 'See the menu',
        'menu_title' => 'Menu',
        'menu_text' => 'We change it every season; dishes of the day are on the board by the door.',
        'stores_title' => 'Where to find us',
        'directions' => 'How to get there',
        'booking_title' => 'Book a table',
        'booking_text' => 'We will confirm your booking by phone or message.',
        'book' => 'Book a table',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Services', 'news' => 'News', 'stores' => 'Branches', 'faq' => 'Questions'],
        'price' => 'from :price',
        'services_title' => 'Services',
        'services_all' => 'All services',
        'doctors_all' => 'All doctors',
        'news_title' => 'Clinic news',
        'faq_title' => 'Questions and answers',
        'book' => 'Book an appointment',
        'disclaimer' => 'Consult a specialist before any treatment.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Catalog', 'news' => 'News', 'stores' => 'Stores', 'faq' => 'Help'],
        'price' => ':price',
        'eyebrow' => 'New collection',
        'catalog_button' => 'Browse the catalog',
        'products_title' => 'Products',
        'sections' => 'Catalog sections',
        'all' => 'All',
        'brands_title' => 'Brands',
        'stores_title' => 'Stores',
        'tagline' => 'Things for the home',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Catalog'],
        'price' => ':price',
        'in_stock' => 'In stock',
        'out_of_stock' => 'Out of stock',
        'hero_title' => 'The whole site, run from one panel',
        'hero_text' => 'Products, news, stores, questions and forms: every page here is managed in Atrium, in 15 languages.',
        'hero_catalog' => 'Browse the catalog',
        'hero_stores' => 'Find a store',
        'brands_title' => 'Brands we carry',
        'sections_title' => 'Shop by section',
        'products_title' => 'New in the catalog',
        'products_all' => 'All products',
        'news_title' => 'Latest news',
        'news_all' => 'All news',
        'stores_title' => 'Visit a store',
        'stores_all' => 'All stores',
        'team_title' => 'The people behind it',
        'team_all' => 'Meet the team',
        'reviews_title' => 'What customers say',
        'faq_title' => 'Questions, answered',
        'contact_title' => 'Talk to us',
        'contact_text' => 'Leave your details and we will get back to you within one working day.',
        'count' => 'Total: :count',
        'sections' => 'Catalog sections',
        'filters' => 'Filters',
        'filter_brand' => 'Brand',
        'filter_all' => 'All',
        'filter_stock' => 'In stock only',
        'sort' => 'Sort by',
        'sort_default' => 'Featured',
        'sort_cheap' => 'Price: low to high',
        'sort_dear' => 'Price: high to low',
        'shown' => 'Showing :shown of :total',
        'filter_none' => 'Nothing matches these filters. Try another brand or turn off the stock filter.',
        'map_show' => 'Show the map',
        'map_open' => 'Open in OpenStreetMap',
        'map_title' => 'Map: :store',
        'tagline' => 'Admin panel demo',
        'footer_menu' => 'Site',
        'contact_footer' => 'Contact',
        'footer_note' => 'A demo site built with Atrium.',
        'photo' => 'Photo :number',
        'order' => 'Order this product',
        'back_to_catalog' => 'Back to the catalog',
        'available_in' => 'Available in',
        'description' => 'About this product',
        'details' => 'Details',
        'related' => 'Goes well with',
    ],
];
