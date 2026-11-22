<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Skinlar matnlari (config atrium-theme.skin). Kontentning oʻzi
// modullardan olinadi; bu yerda uning atrofidagi sarlavha va yozuvlar.
return [
    'rating' => '5 balldan :rating',
    'review' => 'Sharh',

    'travel' => [
        'modules' => ['catalog' => 'Turlar', 'news' => 'Yangiliklar', 'stores' => 'Ofislar', 'faq' => 'Savollar'],
        'price' => ':price dan',
        'eyebrow' => 'Mamlakat ichida va xorijga turlar',
        'catalog_title' => 'Bu yozda qayerga borishadi',
        'catalog_all' => 'Barcha yoʻnalishlar',
        'reviews_title' => 'Sayohatchilar nima deydi',
        'faq_title' => 'Koʻp beriladigan savollar',
        'callback_title' => 'Kerakli turni topa olmadingizmi?',
        'callback_text' => 'Telefon raqamingizni qoldiring, ish vaqtida bir soat ichida sizga qoʻngʻiroq qilamiz.',
        'tagline' => 'turistik agentlik',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Menyu', 'news' => 'Tadbirlar', 'stores' => 'Manzillar', 'faq' => 'Savollar'],
        'price' => ':price',
        'eyebrow' => 'Uy taomlari',
        'hero_menu' => 'Menyuni koʻrish',
        'menu_title' => 'Menyu',
        'menu_text' => 'Uni har mavsumda yangilaymiz, kun taomlari kirish yonidagi doskada.',
        'stores_title' => 'Bizni qayerdan topasiz',
        'directions' => 'Qanday borish mumkin',
        'booking_title' => 'Stol band qilish',
        'booking_text' => 'Bronni qoʻngʻiroq yoki xabar orqali tasdiqlaymiz.',
        'book' => 'Stol band qilish',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Xizmatlar', 'news' => 'Yangiliklar', 'stores' => 'Filiallar', 'faq' => 'Savollar'],
        'price' => ':price dan',
        'services_title' => 'Xizmatlar',
        'services_all' => 'Barcha xizmatlar',
        'doctors_all' => 'Barcha shifokorlar',
        'news_title' => 'Klinika yangiliklari',
        'faq_title' => 'Savol va javoblar',
        'book' => 'Qabulga yozilish',
        'disclaimer' => 'Har qanday davolanishdan oldin mutaxassis bilan maslahatlashing.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Katalog', 'news' => 'Yangiliklar', 'stores' => 'Doʻkonlar', 'faq' => 'Yordam'],
        'price' => ':price',
        'eyebrow' => 'Yangi kolleksiya',
        'catalog_button' => 'Katalogni koʻrish',
        'products_title' => 'Mahsulotlar',
        'sections' => 'Katalog boʻlimlari',
        'all' => 'Hammasi',
        'brands_title' => 'Brendlar',
        'stores_title' => 'Doʻkonlar',
        'tagline' => 'Uy uchun buyumlar',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Katalog'],
        'price' => ':price',
        'in_stock' => 'Mavjud',
        'out_of_stock' => 'Mavjud emas',
        'hero_title' => 'Butun sayt bitta paneldan boshqariladi',
        'hero_text' => 'Mahsulotlar, yangiliklar, doʻkonlar, savollar va shakllar: bu yerdagi har bir sahifa Atrium orqali boshqariladi, 15 tilda.',
        'hero_catalog' => 'Katalogni koʻrish',
        'hero_stores' => 'Doʻkon topish',
        'brands_title' => 'Bizdagi brendlar',
        'sections_title' => 'Boʻlimlar boʻyicha xarid',
        'products_title' => 'Katalogdagi yangiliklar',
        'products_all' => 'Barcha mahsulotlar',
        'news_title' => 'Soʻnggi yangiliklar',
        'news_all' => 'Barcha yangiliklar',
        'stores_title' => 'Doʻkonga tashrif buyuring',
        'stores_all' => 'Barcha doʻkonlar',
        'team_title' => 'Loyiha ortidagi odamlar',
        'team_all' => 'Jamoa bilan tanishing',
        'reviews_title' => 'Mijozlar fikri',
        'faq_title' => 'Savol va javoblar',
        'contact_title' => 'Biz bilan bogʻlaning',
        'contact_text' => 'Maʼlumotlaringizni qoldiring, biz bir ish kuni ichida javob beramiz.',
        'count' => 'Jami: :count',
        'sections' => 'Katalog boʻlimlari',
        'filters' => 'Filtrlar',
        'filter_brand' => 'Brend',
        'filter_all' => 'Hammasi',
        'filter_stock' => 'Faqat mavjudlari',
        'sort' => 'Saralash',
        'sort_default' => 'Tavsiya etilganlar',
        'sort_cheap' => 'Narx: arzondan qimmatga',
        'sort_dear' => 'Narx: qimmatdan arzonga',
        'shown' => ':total tadan :shown tasi koʻrsatilmoqda',
        'filter_none' => 'Bu filtrlarga mos mahsulot topilmadi. Boshqa brendni tanlang yoki mavjudlik filtrini oʻchiring.',
        'map_show' => 'Xaritani koʻrsatish',
        'map_open' => 'OpenStreetMap orqali ochish',
        'map_title' => 'Xarita: :store',
        'tagline' => 'Admin panel demosi',
        'footer_menu' => 'Sayt',
        'contact_footer' => 'Aloqa',
        'footer_note' => 'Atrium bilan yaratilgan demo sayt.',
        'photo' => 'Surat :number',
        'order' => 'Mahsulotga buyurtma berish',
        'back_to_catalog' => 'Katalogga qaytish',
        'available_in' => 'Mavjud doʻkonlar',
        'description' => 'Mahsulot haqida',
        'details' => 'Tafsilotlar',
        'related' => 'Bunga yaxshi mos keladi',
    ],
];
