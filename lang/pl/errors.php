<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Przejdź do strony głównej',
    'search' => 'Szukaj w serwisie',
    'search_button' => 'Szukaj',
    '403' => [
        'title' => 'Odmowa dostępu',
        'message' => 'Nie masz uprawnień do otwarcia tej strony.',
    ],
    '404' => [
        'title' => 'Nie znaleziono strony',
        'message' => 'Adres może zawierać literówkę albo strona została przeniesiona lub usunięta.',
    ],
    '419' => [
        'title' => 'Strona wygasła',
        'message' => 'Strona była otwarta zbyt długo. Wróć, odśwież ją i spróbuj ponownie.',
    ],
    '429' => [
        'title' => 'Zbyt wiele żądań',
        'message' => 'Poczekaj minutę i spróbuj ponownie.',
    ],
    '500' => [
        'title' => 'Coś poszło nie tak',
        'message' => 'Błąd został zapisany. Spróbuj ponownie za kilka minut.',
    ],
    '503' => [
        'title' => 'Zaraz wracamy',
        'message' => 'Trwa aktualizacja serwisu. Zajrzyj za kilka minut.',
    ],
    '4xx' => [
        'title' => 'Nie udało się wykonać żądania',
        'message' => 'Sprawdź adres albo zacznij od strony głównej.',
    ],
    '5xx' => [
        'title' => 'Serwer nie mógł odpowiedzieć',
        'message' => 'Spróbuj ponownie za kilka minut.',
    ],
];
