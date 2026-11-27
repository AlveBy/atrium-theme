<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'types' => [
        'text' => 'Text',
        'picture' => 'Picture',
        'gallery' => 'Gallery',
        'button' => 'Button',
        'map' => 'Map',
    ],
    'fields' => [
        'html' => 'Text',
        'image' => 'Picture',
        'alt' => 'Description for screen readers',
        'caption' => 'Caption',
        'link' => 'Link',
        'images' => 'Pictures',
        'label' => 'Button text',
        'style' => 'Look',
        'new_tab' => 'Open in a new tab',
        'point' => 'Place (latitude, longitude)',
        'zoom' => 'Zoom',
        'address' => 'Address',
    ],
    'styles' => [
        'primary' => 'Filled',
        'secondary' => 'Outlined',
    ],
    'image' => 'Picture :number',
    'map' => [
        'invalid_point' => 'Write the place as latitude and longitude, for example 48.8584, 2.2945.',
        'open' => 'Open the map',
        'title' => 'Map',
    ],
];
