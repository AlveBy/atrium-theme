<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

return [
    'home' => 'Ir para a página inicial',
    'search' => 'Pesquisar no site',
    'search_button' => 'Pesquisar',
    '403' => [
        'title' => 'Acesso negado',
        'message' => 'Você não tem permissão para abrir esta página.',
    ],
    '404' => [
        'title' => 'Página não encontrada',
        'message' => 'O endereço pode estar digitado errado, ou a página foi movida ou removida.',
    ],
    '419' => [
        'title' => 'Página expirada',
        'message' => 'A página ficou aberta por muito tempo. Volte, recarregue-a e tente de novo.',
    ],
    '429' => [
        'title' => 'Muitas solicitações',
        'message' => 'Aguarde um minuto e tente de novo.',
    ],
    '500' => [
        'title' => 'Algo deu errado',
        'message' => 'O erro foi registrado. Tente de novo em alguns minutos.',
    ],
    '503' => [
        'title' => 'Voltamos logo',
        'message' => 'O site está sendo atualizado. Volte em alguns minutos.',
    ],
    '4xx' => [
        'title' => 'Não foi possível concluir a solicitação',
        'message' => 'Confira o endereço ou comece pela página inicial.',
    ],
    '5xx' => [
        'title' => 'O servidor não conseguiu responder',
        'message' => 'Tente de novo em alguns minutos.',
    ],
];
