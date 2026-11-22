<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Texte der Skins (config atrium-theme.skin). Die Inhalte selbst kommen
// aus den Modulen; hier stehen die Überschriften und Beschriftungen.
return [
    'rating' => ':rating von 5',
    'review' => 'Bewertung',

    'travel' => [
        'modules' => ['catalog' => 'Reisen', 'news' => 'Neuigkeiten', 'stores' => 'Büros', 'faq' => 'Fragen'],
        'price' => 'ab :price',
        'eyebrow' => 'Reisen im In- und Ausland',
        'catalog_title' => 'Wohin es diesen Sommer geht',
        'catalog_all' => 'Alle Reiseziele',
        'reviews_title' => 'Was Reisende sagen',
        'faq_title' => 'Häufige Fragen',
        'callback_title' => 'Nicht die passende Reise gefunden?',
        'callback_text' => 'Hinterlassen Sie Ihre Nummer – wir rufen während der Geschäftszeiten innerhalb einer Stunde zurück.',
        'tagline' => 'Reisebüro',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Speisekarte', 'news' => 'Veranstaltungen', 'stores' => 'Standorte', 'faq' => 'Fragen'],
        'price' => ':price',
        'eyebrow' => 'Hausmannskost',
        'hero_menu' => 'Zur Speisekarte',
        'menu_title' => 'Speisekarte',
        'menu_text' => 'Wir wechseln sie jede Saison, die Tagesgerichte stehen auf der Tafel am Eingang.',
        'stores_title' => 'Hier finden Sie uns',
        'directions' => 'Anfahrt',
        'booking_title' => 'Tisch reservieren',
        'booking_text' => 'Wir bestätigen die Reservierung per Anruf oder Nachricht.',
        'book' => 'Tisch reservieren',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Leistungen', 'news' => 'Neuigkeiten', 'stores' => 'Standorte', 'faq' => 'Fragen'],
        'price' => 'ab :price',
        'services_title' => 'Leistungen',
        'services_all' => 'Alle Leistungen',
        'doctors_all' => 'Alle Ärztinnen und Ärzte',
        'news_title' => 'Neues aus dem Zentrum',
        'faq_title' => 'Fragen und Antworten',
        'book' => 'Termin buchen',
        'disclaimer' => 'Vor jeder Behandlung fachärztlichen Rat einholen.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Katalog', 'news' => 'Neuigkeiten', 'stores' => 'Geschäfte', 'faq' => 'Hilfe'],
        'price' => ':price',
        'eyebrow' => 'Neue Kollektion',
        'catalog_button' => 'Zum Katalog',
        'products_title' => 'Produkte',
        'sections' => 'Katalogbereiche',
        'all' => 'Alles',
        'brands_title' => 'Marken',
        'stores_title' => 'Geschäfte',
        'tagline' => 'Dinge für Zuhause',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Katalog'],
        'price' => ':price',
        'in_stock' => 'Auf Lager',
        'out_of_stock' => 'Nicht auf Lager',
        'hero_title' => 'Die ganze Website aus einem Panel',
        'hero_text' => 'Produkte, Neuigkeiten, Geschäfte, Fragen und Formulare: Jede Seite wird hier in Atrium verwaltet, in 15 Sprachen.',
        'hero_catalog' => 'Zum Katalog',
        'hero_stores' => 'Geschäft finden',
        'brands_title' => 'Unsere Marken',
        'sections_title' => 'Nach Bereich einkaufen',
        'products_title' => 'Neu im Katalog',
        'products_all' => 'Alle Produkte',
        'news_title' => 'Aktuelle Neuigkeiten',
        'news_all' => 'Alle Neuigkeiten',
        'stores_title' => 'Geschäft besuchen',
        'stores_all' => 'Alle Geschäfte',
        'team_title' => 'Die Menschen dahinter',
        'team_all' => 'Das Team kennenlernen',
        'reviews_title' => 'Das sagen Kunden',
        'faq_title' => 'Fragen und Antworten',
        'contact_title' => 'Sprechen Sie uns an',
        'contact_text' => 'Hinterlassen Sie Ihre Daten, wir melden uns innerhalb eines Werktags.',
        'count' => 'Gesamt: :count',
        'sections' => 'Katalogbereiche',
        'filters' => 'Filter',
        'filter_brand' => 'Marke',
        'filter_all' => 'Alle',
        'filter_stock' => 'Nur auf Lager',
        'sort' => 'Sortieren nach',
        'sort_default' => 'Empfohlen',
        'sort_cheap' => 'Preis: aufsteigend',
        'sort_dear' => 'Preis: absteigend',
        'shown' => ':shown von :total angezeigt',
        'filter_none' => 'Keine Treffer für diese Filter. Wählen Sie eine andere Marke oder deaktivieren Sie den Lagerfilter.',
        'map_show' => 'Karte anzeigen',
        'map_open' => 'In OpenStreetMap öffnen',
        'map_title' => 'Karte: :store',
        'tagline' => 'Demo des Admin-Panels',
        'footer_menu' => 'Website',
        'contact_footer' => 'Kontakt',
        'footer_note' => 'Eine Demoseite, gebaut mit Atrium.',
        'photo' => 'Foto :number',
        'order' => 'Produkt bestellen',
        'back_to_catalog' => 'Zurück zum Katalog',
        'available_in' => 'Erhältlich in',
        'description' => 'Über dieses Produkt',
        'details' => 'Details',
        'related' => 'Passt gut dazu',
    ],
];
