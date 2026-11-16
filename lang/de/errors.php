<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Zur Startseite',
    'search' => 'Website durchsuchen',
    'search_button' => 'Suchen',
    '403' => [
        'title' => 'Zugriff verweigert',
        'message' => 'Sie haben keine Berechtigung, diese Seite zu öffnen.',
    ],
    '404' => [
        'title' => 'Seite nicht gefunden',
        'message' => 'Die Adresse ist vielleicht falsch geschrieben, oder die Seite wurde verschoben oder entfernt.',
    ],
    '419' => [
        'title' => 'Seite abgelaufen',
        'message' => 'Die Seite war zu lange geöffnet. Gehen Sie zurück, laden Sie sie neu und versuchen Sie es erneut.',
    ],
    '429' => [
        'title' => 'Zu viele Anfragen',
        'message' => 'Bitte warten Sie eine Minute und versuchen Sie es erneut.',
    ],
    '500' => [
        'title' => 'Etwas ist schiefgelaufen',
        'message' => 'Der Fehler wurde protokolliert. Bitte versuchen Sie es in ein paar Minuten erneut.',
    ],
    '503' => [
        'title' => 'Gleich wieder da',
        'message' => 'Die Website wird gerade aktualisiert. Bitte schauen Sie in ein paar Minuten wieder vorbei.',
    ],
    '4xx' => [
        'title' => 'Die Anfrage konnte nicht ausgeführt werden',
        'message' => 'Prüfen Sie die Adresse oder beginnen Sie auf der Startseite.',
    ],
    '5xx' => [
        'title' => 'Der Server konnte nicht antworten',
        'message' => 'Bitte versuchen Sie es in ein paar Minuten erneut.',
    ],
];
