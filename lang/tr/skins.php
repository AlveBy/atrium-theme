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

    'atrium' => [
        'modules' => ['catalog' => 'Katalog'],
        'price' => ':price',
        'in_stock' => 'Stokta',
        'out_of_stock' => 'Stokta yok',
        'hero_title' => 'Tüm site tek panelden yönetilir',
        'hero_text' => 'Ürünler, haberler, mağazalar, sorular ve formlar: buradaki her sayfa Atrium\'da yönetilir, 15 dilde.',
        'hero_catalog' => 'Kataloğa göz atın',
        'hero_stores' => 'Mağaza bulun',
        'brands_title' => 'Sattığımız markalar',
        'sections_title' => 'Bölümlere göre alışveriş',
        'products_title' => 'Katalogda yeni olanlar',
        'products_all' => 'Tüm ürünler',
        'news_title' => 'Son haberler',
        'news_all' => 'Tüm haberler',
        'stores_title' => 'Bir mağazayı ziyaret edin',
        'stores_all' => 'Tüm mağazalar',
        'team_title' => 'Arkasındaki insanlar',
        'team_all' => 'Ekiple tanışın',
        'reviews_title' => 'Müşteriler ne diyor',
        'faq_title' => 'Sorular ve cevaplar',
        'contact_title' => 'Bize ulaşın',
        'contact_text' => 'Bilgilerinizi bırakın, bir iş günü içinde size dönelim.',
        'count' => 'Toplam: :count',
        'sections' => 'Katalog bölümleri',
        'filters' => 'Filtreler',
        'filter_brand' => 'Marka',
        'filter_all' => 'Tümü',
        'filter_stock' => 'Yalnızca stokta olanlar',
        'sort' => 'Sırala',
        'sort_default' => 'Öne çıkanlar',
        'sort_cheap' => 'Fiyat: düşükten yükseğe',
        'sort_dear' => 'Fiyat: yüksekten düşüğe',
        'shown' => ':total üründen :shown tanesi gösteriliyor',
        'filter_none' => 'Bu filtrelere uyan ürün yok. Başka bir marka deneyin veya stok filtresini kapatın.',
        'map_show' => 'Haritayı göster',
        'map_open' => 'OpenStreetMap\'te aç',
        'map_title' => 'Harita: :store',
        'tagline' => 'Yönetim paneli demosu',
        'footer_menu' => 'Site',
        'contact_footer' => 'İletişim',
        'footer_note' => 'Atrium ile hazırlanmış bir demo site.',
        'photo' => 'Fotoğraf :number',
        'order' => 'Bu ürünü sipariş et',
        'back_to_catalog' => 'Kataloğa dön',
        'available_in' => 'Bulunduğu mağazalar',
        'description' => 'Ürün hakkında',
        'details' => 'Ayrıntılar',
        'related' => 'Şununla iyi gider',
    ],
];
