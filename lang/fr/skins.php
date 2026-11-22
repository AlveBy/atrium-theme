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

    'atrium' => [
        'modules' => ['catalog' => 'Catalogue'],
        'price' => ':price',
        'in_stock' => 'En stock',
        'out_of_stock' => 'Rupture de stock',
        'hero_title' => 'Tout le site, depuis un seul panneau',
        'hero_text' => 'Produits, actualités, boutiques, questions et formulaires : chaque page est gérée ici dans Atrium, en 15 langues.',
        'hero_catalog' => 'Parcourir le catalogue',
        'hero_stores' => 'Trouver une boutique',
        'brands_title' => 'Nos marques',
        'sections_title' => 'Acheter par rubrique',
        'products_title' => 'Nouveautés du catalogue',
        'products_all' => 'Tous les produits',
        'news_title' => 'Dernières actualités',
        'news_all' => 'Toutes les actualités',
        'stores_title' => 'Passez en boutique',
        'stores_all' => 'Toutes les boutiques',
        'team_title' => 'Les gens derrière le site',
        'team_all' => 'Rencontrer l’équipe',
        'reviews_title' => 'Ce que disent nos clients',
        'faq_title' => 'Questions et réponses',
        'contact_title' => 'Contactez-nous',
        'contact_text' => 'Laissez vos coordonnées, nous vous répondrons sous un jour ouvré.',
        'count' => 'Total : :count',
        'sections' => 'Rubriques du catalogue',
        'filters' => 'Filtres',
        'filter_brand' => 'Marque',
        'filter_all' => 'Toutes',
        'filter_stock' => 'En stock uniquement',
        'sort' => 'Trier par',
        'sort_default' => 'Sélection',
        'sort_cheap' => 'Prix : croissant',
        'sort_dear' => 'Prix : décroissant',
        'shown' => ':shown sur :total affichés',
        'filter_none' => 'Aucun résultat pour ces filtres. Essayez une autre marque ou désactivez le filtre de stock.',
        'map_show' => 'Afficher la carte',
        'map_open' => 'Ouvrir dans OpenStreetMap',
        'map_title' => 'Carte : :store',
        'tagline' => 'Démo de panneau d’administration',
        'footer_menu' => 'Site',
        'contact_footer' => 'Contact',
        'footer_note' => 'Un site de démonstration créé avec Atrium.',
        'photo' => 'Photo :number',
        'order' => 'Commander ce produit',
        'back_to_catalog' => 'Retour au catalogue',
        'available_in' => 'Disponible à',
        'description' => 'À propos de ce produit',
        'details' => 'Détails',
        'related' => 'À associer avec',
    ],
];
