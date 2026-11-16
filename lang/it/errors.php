<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Vai alla home page',
    'search' => 'Cerca nel sito',
    'search_button' => 'Cerca',
    '403' => [
        'title' => 'Accesso negato',
        'message' => 'Non hai il permesso di aprire questa pagina.',
    ],
    '404' => [
        'title' => 'Pagina non trovata',
        'message' => 'L’indirizzo potrebbe essere errato, oppure la pagina è stata spostata o eliminata.',
    ],
    '419' => [
        'title' => 'Pagina scaduta',
        'message' => 'La pagina è rimasta aperta troppo a lungo. Torna indietro, ricaricala e riprova.',
    ],
    '429' => [
        'title' => 'Troppe richieste',
        'message' => 'Attendi un minuto e riprova.',
    ],
    '500' => [
        'title' => 'Qualcosa è andato storto',
        'message' => 'L’errore è stato registrato. Riprova tra qualche minuto.',
    ],
    '503' => [
        'title' => 'Torniamo presto',
        'message' => 'Il sito è in aggiornamento. Torna tra qualche minuto.',
    ],
    '4xx' => [
        'title' => 'Impossibile completare la richiesta',
        'message' => 'Controlla l’indirizzo o riparti dalla home page.',
    ],
    '5xx' => [
        'title' => 'Il server non ha potuto rispondere',
        'message' => 'Riprova tra qualche minuto.',
    ],
];
