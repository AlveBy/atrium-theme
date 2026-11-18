<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Textos dos skins (config atrium-theme.skin). O conteúdo vem dos
// módulos; aqui ficam os títulos e rótulos ao redor dele.
return [
    'rating' => ':rating de 5',
    'review' => 'Avaliação',

    'travel' => [
        'modules' => ['catalog' => 'Pacotes', 'news' => 'Notícias', 'stores' => 'Escritórios', 'faq' => 'Dúvidas'],
        'price' => 'a partir de :price',
        'eyebrow' => 'Viagens no Brasil e no exterior',
        'catalog_title' => 'Para onde as pessoas vão neste verão',
        'catalog_all' => 'Todos os destinos',
        'reviews_title' => 'O que os viajantes dizem',
        'faq_title' => 'Perguntas frequentes',
        'callback_title' => 'Não encontrou o pacote ideal?',
        'callback_text' => 'Deixe seu número e retornaremos a ligação em até uma hora, em horário comercial.',
        'tagline' => 'agência de viagens',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Cardápio', 'news' => 'Eventos', 'stores' => 'Unidades', 'faq' => 'Dúvidas'],
        'price' => ':price',
        'eyebrow' => 'Comida caseira',
        'hero_menu' => 'Ver o cardápio',
        'menu_title' => 'Cardápio',
        'menu_text' => 'Mudamos a cada estação; os pratos do dia ficam na lousa perto da porta.',
        'stores_title' => 'Onde nos encontrar',
        'directions' => 'Como chegar',
        'booking_title' => 'Reserve uma mesa',
        'booking_text' => 'Confirmaremos sua reserva por telefone ou mensagem.',
        'book' => 'Reservar uma mesa',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Serviços', 'news' => 'Notícias', 'stores' => 'Unidades', 'faq' => 'Dúvidas'],
        'price' => 'a partir de :price',
        'services_title' => 'Serviços',
        'services_all' => 'Todos os serviços',
        'doctors_all' => 'Todos os médicos',
        'news_title' => 'Notícias da clínica',
        'faq_title' => 'Perguntas e respostas',
        'book' => 'Agendar consulta',
        'disclaimer' => 'Consulte um especialista antes de qualquer tratamento.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Catálogo', 'news' => 'Notícias', 'stores' => 'Lojas', 'faq' => 'Ajuda'],
        'price' => ':price',
        'eyebrow' => 'Nova coleção',
        'catalog_button' => 'Ver o catálogo',
        'products_title' => 'Produtos',
        'sections' => 'Seções do catálogo',
        'all' => 'Tudo',
        'brands_title' => 'Marcas',
        'stores_title' => 'Lojas',
        'tagline' => 'Coisas para a casa',
    ],
];
