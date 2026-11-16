<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Aller à l’accueil',
    'search' => 'Rechercher sur le site',
    'search_button' => 'Rechercher',
    '403' => [
        'title' => 'Accès refusé',
        'message' => 'Vous n’avez pas l’autorisation d’ouvrir cette page.',
    ],
    '404' => [
        'title' => 'Page introuvable',
        'message' => 'L’adresse est peut-être mal saisie, ou la page a été déplacée ou supprimée.',
    ],
    '419' => [
        'title' => 'Page expirée',
        'message' => 'La page est restée ouverte trop longtemps. Revenez en arrière, rechargez-la et réessayez.',
    ],
    '429' => [
        'title' => 'Trop de requêtes',
        'message' => 'Veuillez patienter une minute et réessayer.',
    ],
    '500' => [
        'title' => 'Une erreur est survenue',
        'message' => 'L’erreur a été enregistrée. Réessayez dans quelques minutes.',
    ],
    '503' => [
        'title' => 'De retour bientôt',
        'message' => 'Le site est en cours de mise à jour. Revenez dans quelques minutes.',
    ],
    '4xx' => [
        'title' => 'La requête n’a pas pu aboutir',
        'message' => 'Vérifiez l’adresse ou repartez de la page d’accueil.',
    ],
    '5xx' => [
        'title' => 'Le serveur n’a pas pu répondre',
        'message' => 'Réessayez dans quelques minutes.',
    ],
];
