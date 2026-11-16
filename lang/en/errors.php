<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Go to the home page',
    'search' => 'Search the site',
    'search_button' => 'Search',
    '403' => [
        'title' => 'Access denied',
        'message' => 'You do not have permission to open this page.',
    ],
    '404' => [
        'title' => 'Page not found',
        'message' => 'The address may be mistyped, or the page has moved or been removed.',
    ],
    '419' => [
        'title' => 'Page expired',
        'message' => 'The page was open too long. Go back, reload it and try again.',
    ],
    '429' => [
        'title' => 'Too many requests',
        'message' => 'Please wait a minute and try again.',
    ],
    '500' => [
        'title' => 'Something went wrong',
        'message' => 'The error has been recorded. Please try again in a few minutes.',
    ],
    '503' => [
        'title' => 'Back soon',
        'message' => 'The site is being updated. Please come back in a few minutes.',
    ],
    '4xx' => [
        'title' => 'The request could not be completed',
        'message' => 'Check the address or start from the home page.',
    ],
    '5xx' => [
        'title' => 'The server could not answer',
        'message' => 'Please try again in a few minutes.',
    ],
];
