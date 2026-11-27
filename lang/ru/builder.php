<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'types' => [
        'text' => 'Текст',
        'picture' => 'Картинка',
        'gallery' => 'Галерея',
        'button' => 'Кнопка',
        'map' => 'Карта',
    ],
    'fields' => [
        'html' => 'Текст',
        'image' => 'Картинка',
        'alt' => 'Описание для программ чтения с экрана',
        'caption' => 'Подпись',
        'link' => 'Ссылка',
        'images' => 'Картинки',
        'label' => 'Надпись на кнопке',
        'style' => 'Вид',
        'new_tab' => 'Открывать в новой вкладке',
        'point' => 'Место (широта, долгота)',
        'zoom' => 'Масштаб',
        'address' => 'Адрес',
    ],
    'styles' => [
        'primary' => 'С заливкой',
        'secondary' => 'С контуром',
    ],
    'image' => 'Картинка :number',
    'map' => [
        'invalid_point' => 'Напишите место как широту и долготу, например 48.8584, 2.2945.',
        'open' => 'Открыть карту',
        'title' => 'Карта',
    ],
];
