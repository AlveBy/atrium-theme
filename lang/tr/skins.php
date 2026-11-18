<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Skin metinleri (config atrium-theme.skin). İçeriğin kendisi
// modüllerden gelir; burada çevresindeki başlıklar ve etiketler yer alır.
return [
    'rating' => '5 üzerinden :rating',
    'review' => 'Yorum',

    'travel' => [
        'modules' => ['catalog' => 'Turlar', 'news' => 'Haberler', 'stores' => 'Ofisler', 'faq' => 'Sorular'],
        'price' => 'Başlangıç: :price',
        'eyebrow' => 'Yurt içi ve yurt dışı turlar',
        'catalog_title' => 'Bu yaz herkes nereye gidiyor',
        'catalog_all' => 'Tüm destinasyonlar',
        'reviews_title' => 'Yolcularımız ne diyor',
        'faq_title' => 'Sık sorulan sorular',
        'callback_title' => 'Size uygun turu bulamadınız mı?',
        'callback_text' => 'Numaranızı bırakın, mesai saatlerinde bir saat içinde sizi arayalım.',
        'tagline' => 'seyahat acentesi',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Menü', 'news' => 'Etkinlikler', 'stores' => 'Şubeler', 'faq' => 'Sorular'],
        'price' => ':price',
        'eyebrow' => 'Ev yemekleri',
        'hero_menu' => 'Menüyü görün',
        'menu_title' => 'Menü',
        'menu_text' => 'Her sezon değiştiriyoruz; günün yemekleri girişteki tahtada.',
        'stores_title' => 'Bizi nerede bulabilirsiniz',
        'directions' => 'Nasıl gelinir',
        'booking_title' => 'Masa ayırtın',
        'booking_text' => 'Rezervasyonunuzu telefon veya mesajla onaylayacağız.',
        'book' => 'Masa ayırtın',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Hizmetler', 'news' => 'Haberler', 'stores' => 'Şubeler', 'faq' => 'Sorular'],
        'price' => 'Başlangıç: :price',
        'services_title' => 'Hizmetler',
        'services_all' => 'Tüm hizmetler',
        'doctors_all' => 'Tüm doktorlar',
        'news_title' => 'Klinikten haberler',
        'faq_title' => 'Sorular ve cevaplar',
        'book' => 'Randevu alın',
        'disclaimer' => 'Herhangi bir tedaviden önce bir uzmana danışın.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Katalog', 'news' => 'Haberler', 'stores' => 'Mağazalar', 'faq' => 'Yardım'],
        'price' => ':price',
        'eyebrow' => 'Yeni koleksiyon',
        'catalog_button' => 'Kataloğa göz atın',
        'products_title' => 'Ürünler',
        'sections' => 'Katalog bölümleri',
        'all' => 'Tümü',
        'brands_title' => 'Markalar',
        'stores_title' => 'Mağazalar',
        'tagline' => 'Ev için eşyalar',
    ],
];
