<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Textos de los skins (config atrium-theme.skin). El contenido viene de
// los módulos; aquí están los títulos y etiquetas que lo rodean.
return [
    'rating' => ':rating de 5',
    'review' => 'Opinión',

    'travel' => [
        'modules' => ['catalog' => 'Viajes', 'news' => 'Noticias', 'stores' => 'Oficinas', 'faq' => 'Preguntas'],
        'price' => 'desde :price',
        'eyebrow' => 'Viajes nacionales e internacionales',
        'catalog_title' => 'Adónde viaja la gente este verano',
        'catalog_all' => 'Todos los destinos',
        'reviews_title' => 'Lo que dicen los viajeros',
        'faq_title' => 'Preguntas frecuentes',
        'callback_title' => '¿No encuentra el viaje que busca?',
        'callback_text' => 'Déjenos su número y le llamaremos en menos de una hora en horario de oficina.',
        'tagline' => 'agencia de viajes',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Carta', 'news' => 'Eventos', 'stores' => 'Locales', 'faq' => 'Preguntas'],
        'price' => ':price',
        'eyebrow' => 'Cocina casera',
        'hero_menu' => 'Ver la carta',
        'menu_title' => 'Carta',
        'menu_text' => 'La cambiamos cada temporada; los platos del día están en la pizarra junto a la puerta.',
        'stores_title' => 'Dónde encontrarnos',
        'directions' => 'Cómo llegar',
        'booking_title' => 'Reservar una mesa',
        'booking_text' => 'Le confirmaremos la reserva por teléfono o mensaje.',
        'book' => 'Reservar una mesa',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Servicios', 'news' => 'Noticias', 'stores' => 'Sedes', 'faq' => 'Preguntas'],
        'price' => 'desde :price',
        'services_title' => 'Servicios',
        'services_all' => 'Todos los servicios',
        'doctors_all' => 'Todos los médicos',
        'news_title' => 'Noticias de la clínica',
        'faq_title' => 'Preguntas y respuestas',
        'book' => 'Pedir cita',
        'disclaimer' => 'Consulte a un especialista antes de cualquier tratamiento.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Catálogo', 'news' => 'Noticias', 'stores' => 'Tiendas', 'faq' => 'Ayuda'],
        'price' => ':price',
        'eyebrow' => 'Nueva colección',
        'catalog_button' => 'Ver el catálogo',
        'products_title' => 'Productos',
        'sections' => 'Secciones del catálogo',
        'all' => 'Todo',
        'brands_title' => 'Marcas',
        'stores_title' => 'Tiendas',
        'tagline' => 'Cosas para el hogar',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Catálogo'],
        'price' => ':price',
        'in_stock' => 'En stock',
        'out_of_stock' => 'Agotado',
        'hero_title' => 'Todo el sitio, desde un solo panel',
        'hero_text' => 'Productos, noticias, tiendas, preguntas y formularios: cada página se gestiona aquí en Atrium, en 15 idiomas.',
        'hero_catalog' => 'Ver el catálogo',
        'hero_stores' => 'Buscar una tienda',
        'brands_title' => 'Marcas que ofrecemos',
        'sections_title' => 'Compra por sección',
        'products_title' => 'Novedades del catálogo',
        'products_all' => 'Todos los productos',
        'news_title' => 'Últimas noticias',
        'news_all' => 'Todas las noticias',
        'stores_title' => 'Visita una tienda',
        'stores_all' => 'Todas las tiendas',
        'team_title' => 'Las personas detrás',
        'team_all' => 'Conoce al equipo',
        'reviews_title' => 'Lo que dicen los clientes',
        'faq_title' => 'Preguntas y respuestas',
        'contact_title' => 'Habla con nosotros',
        'contact_text' => 'Déjanos tus datos y te responderemos en un día laborable.',
        'count' => 'Total: :count',
        'sections' => 'Secciones del catálogo',
        'filters' => 'Filtros',
        'filter_brand' => 'Marca',
        'filter_all' => 'Todas',
        'filter_stock' => 'Solo en stock',
        'sort' => 'Ordenar por',
        'sort_default' => 'Destacados',
        'sort_cheap' => 'Precio: de menor a mayor',
        'sort_dear' => 'Precio: de mayor a menor',
        'shown' => 'Mostrando :shown de :total',
        'filter_none' => 'Ningún producto coincide con estos filtros. Prueba otra marca o desactiva el filtro de stock.',
        'map_show' => 'Mostrar el mapa',
        'map_open' => 'Abrir en OpenStreetMap',
        'map_title' => 'Mapa: :store',
        'tagline' => 'Demo del panel de administración',
        'footer_menu' => 'Sitio',
        'contact_footer' => 'Contacto',
        'footer_note' => 'Un sitio de demostración creado con Atrium.',
        'photo' => 'Foto :number',
        'order' => 'Pedir este producto',
        'back_to_catalog' => 'Volver al catálogo',
        'available_in' => 'Disponible en',
        'description' => 'Sobre este producto',
        'details' => 'Detalles',
        'related' => 'Combina bien con',
    ],
];
