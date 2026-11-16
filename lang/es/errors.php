<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Ir a la página de inicio',
    'search' => 'Buscar en el sitio',
    'search_button' => 'Buscar',
    '403' => [
        'title' => 'Acceso denegado',
        'message' => 'No tienes permiso para abrir esta página.',
    ],
    '404' => [
        'title' => 'Página no encontrada',
        'message' => 'Puede que la dirección esté mal escrita o que la página se haya movido o eliminado.',
    ],
    '419' => [
        'title' => 'La página ha caducado',
        'message' => 'La página estuvo abierta demasiado tiempo. Vuelve atrás, recárgala e inténtalo de nuevo.',
    ],
    '429' => [
        'title' => 'Demasiadas solicitudes',
        'message' => 'Espera un minuto e inténtalo de nuevo.',
    ],
    '500' => [
        'title' => 'Algo salió mal',
        'message' => 'El error ha quedado registrado. Inténtalo de nuevo dentro de unos minutos.',
    ],
    '503' => [
        'title' => 'Volvemos pronto',
        'message' => 'Estamos actualizando el sitio. Vuelve dentro de unos minutos.',
    ],
    '4xx' => [
        'title' => 'No se pudo completar la solicitud',
        'message' => 'Comprueba la dirección o empieza desde la página de inicio.',
    ],
    '5xx' => [
        'title' => 'El servidor no pudo responder',
        'message' => 'Inténtalo de nuevo dentro de unos minutos.',
    ],
];
