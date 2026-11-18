<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Textes des skins (config atrium-theme.skin). Le contenu vient des
// modules ; ici, les titres et libellés qui l’entourent.
return [
    'rating' => ':rating sur 5',
    'review' => 'Avis',

    'travel' => [
        'modules' => ['catalog' => 'Voyages', 'news' => 'Actualités', 'stores' => 'Agences', 'faq' => 'Questions'],
        'price' => 'à partir de :price',
        'eyebrow' => 'Voyages en France et à l’étranger',
        'catalog_title' => 'Où l’on part cet été',
        'catalog_all' => 'Toutes les destinations',
        'reviews_title' => 'Ce que disent nos voyageurs',
        'faq_title' => 'Questions fréquentes',
        'callback_title' => 'Vous n’avez pas trouvé le voyage qu’il vous faut ?',
        'callback_text' => 'Laissez-nous votre numéro et nous vous rappellerons dans l’heure, pendant les heures d’ouverture.',
        'tagline' => 'agence de voyages',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Carte', 'news' => 'Événements', 'stores' => 'Adresses', 'faq' => 'Questions'],
        'price' => ':price',
        'eyebrow' => 'Cuisine maison',
        'hero_menu' => 'Voir la carte',
        'menu_title' => 'Carte',
        'menu_text' => 'Elle change à chaque saison ; les plats du jour sont sur l’ardoise près de l’entrée.',
        'stores_title' => 'Où nous trouver',
        'directions' => 'Comment venir',
        'booking_title' => 'Réserver une table',
        'booking_text' => 'Nous confirmons votre réservation par téléphone ou par message.',
        'book' => 'Réserver une table',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Prestations', 'news' => 'Actualités', 'stores' => 'Centres', 'faq' => 'Questions'],
        'price' => 'à partir de :price',
        'services_title' => 'Prestations',
        'services_all' => 'Toutes les prestations',
        'doctors_all' => 'Tous les médecins',
        'news_title' => 'Actualités de la clinique',
        'faq_title' => 'Questions et réponses',
        'book' => 'Prendre rendez-vous',
        'disclaimer' => 'Consultez un spécialiste avant tout traitement.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Catalogue', 'news' => 'Actualités', 'stores' => 'Boutiques', 'faq' => 'Aide'],
        'price' => ':price',
        'eyebrow' => 'Nouvelle collection',
        'catalog_button' => 'Parcourir le catalogue',
        'products_title' => 'Produits',
        'sections' => 'Rubriques du catalogue',
        'all' => 'Tout',
        'brands_title' => 'Marques',
        'stores_title' => 'Boutiques',
        'tagline' => 'Des objets pour la maison',
    ],
];
