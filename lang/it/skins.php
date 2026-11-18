<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Testi delle skin (config atrium-theme.skin). Il contenuto vero e
// proprio arriva dai moduli; qui ci sono titoli ed etichette attorno ad esso.
return [
    'rating' => ':rating su 5',
    'review' => 'Recensione',

    'travel' => [
        'modules' => ['catalog' => 'Viaggi', 'news' => 'Novità', 'stores' => 'Uffici', 'faq' => 'Domande'],
        'price' => 'da :price',
        'eyebrow' => 'Viaggi in Italia e all’estero',
        'catalog_title' => 'Dove si va quest’estate',
        'catalog_all' => 'Tutte le destinazioni',
        'reviews_title' => 'Cosa dicono i viaggiatori',
        'faq_title' => 'Domande frequenti',
        'callback_title' => 'Non ha trovato il viaggio giusto?',
        'callback_text' => 'Lasci il suo numero e la richiameremo entro un’ora durante l’orario di apertura.',
        'tagline' => 'agenzia di viaggi',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Menu', 'news' => 'Eventi', 'stores' => 'Sedi', 'faq' => 'Domande'],
        'price' => ':price',
        'eyebrow' => 'Cucina casalinga',
        'hero_menu' => 'Vedi il menu',
        'menu_title' => 'Menu',
        'menu_text' => 'Lo cambiamo a ogni stagione; i piatti del giorno sono sulla lavagna accanto all’ingresso.',
        'stores_title' => 'Dove trovarci',
        'directions' => 'Come arrivare',
        'booking_title' => 'Prenota un tavolo',
        'booking_text' => 'Confermeremo la prenotazione per telefono o con un messaggio.',
        'book' => 'Prenota un tavolo',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Servizi', 'news' => 'Novità', 'stores' => 'Sedi', 'faq' => 'Domande'],
        'price' => 'da :price',
        'services_title' => 'Servizi',
        'services_all' => 'Tutti i servizi',
        'doctors_all' => 'Tutti i medici',
        'news_title' => 'Novità dalla clinica',
        'faq_title' => 'Domande e risposte',
        'book' => 'Prenota una visita',
        'disclaimer' => 'Prima di qualsiasi trattamento consulti uno specialista.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Catalogo', 'news' => 'Novità', 'stores' => 'Negozi', 'faq' => 'Assistenza'],
        'price' => ':price',
        'eyebrow' => 'Nuova collezione',
        'catalog_button' => 'Sfoglia il catalogo',
        'products_title' => 'Prodotti',
        'sections' => 'Sezioni del catalogo',
        'all' => 'Tutto',
        'brands_title' => 'Marchi',
        'stores_title' => 'Negozi',
        'tagline' => 'Cose per la casa',
    ],
];
