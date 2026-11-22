<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Тексты скинов (config atrium-theme.skin). Само содержимое берётся из
// модулей; здесь заголовки и подписи вокруг него.
return [
    'rating' => ':rating из 5',
    'review' => 'Отзыв',

    'travel' => [
        'modules' => ['catalog' => 'Туры', 'news' => 'Новости', 'stores' => 'Офисы', 'faq' => 'Вопросы'],
        'price' => 'от :price',
        'eyebrow' => 'Туры по России и за границу',
        'catalog_title' => 'Куда едут этим летом',
        'catalog_all' => 'Все направления',
        'reviews_title' => 'Что говорят после поездки',
        'faq_title' => 'Частые вопросы',
        'callback_title' => 'Не нашли подходящий тур?',
        'callback_text' => 'Оставьте телефон, перезвоним в течение часа в рабочее время.',
        'tagline' => 'туристическое агентство',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Меню', 'news' => 'События', 'stores' => 'Адреса', 'faq' => 'Вопросы'],
        'price' => ':price',
        'eyebrow' => 'Ресторан домашней кухни',
        'hero_menu' => 'Смотреть меню',
        'menu_title' => 'Меню',
        'menu_text' => 'Меняем раз в сезон, блюда дня — на доске у входа.',
        'stores_title' => 'Где нас найти',
        'directions' => 'Как добраться',
        'booking_title' => 'Забронировать стол',
        'booking_text' => 'Подтвердим бронь звонком или сообщением.',
        'book' => 'Забронировать стол',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Услуги', 'news' => 'Новости', 'stores' => 'Филиалы', 'faq' => 'Вопросы'],
        'price' => 'от :price',
        'services_title' => 'Услуги',
        'services_all' => 'Все услуги',
        'doctors_all' => 'Все врачи',
        'news_title' => 'Новости центра',
        'faq_title' => 'Вопросы и ответы',
        'book' => 'Записаться',
        'disclaimer' => 'Есть противопоказания, нужна консультация специалиста.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Каталог', 'news' => 'Новости', 'stores' => 'Магазины', 'faq' => 'Покупателям'],
        'price' => ':price',
        'eyebrow' => 'Новая коллекция',
        'catalog_button' => 'Смотреть каталог',
        'products_title' => 'Товары',
        'sections' => 'Разделы каталога',
        'all' => 'Всё',
        'brands_title' => 'Бренды',
        'stores_title' => 'Магазины',
        'tagline' => 'Магазин вещей для дома',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Каталог'],
        'price' => ':price',
        'in_stock' => 'В наличии',
        'out_of_stock' => 'Нет в наличии',
        'hero_title' => 'Весь сайт — из одной панели',
        'hero_text' => 'Товары, новости, магазины, вопросы и формы: каждая страница здесь управляется в Atrium, на 15 языках.',
        'hero_catalog' => 'Смотреть каталог',
        'hero_stores' => 'Найти магазин',
        'brands_title' => 'Бренды в ассортименте',
        'sections_title' => 'Покупки по разделам',
        'products_title' => 'Новое в каталоге',
        'products_all' => 'Все товары',
        'news_title' => 'Последние новости',
        'news_all' => 'Все новости',
        'stores_title' => 'Загляните в магазин',
        'stores_all' => 'Все магазины',
        'team_title' => 'Люди за проектом',
        'team_all' => 'Познакомиться с командой',
        'reviews_title' => 'Что говорят покупатели',
        'faq_title' => 'Вопросы и ответы',
        'contact_title' => 'Напишите нам',
        'contact_text' => 'Оставьте свои данные, и мы ответим в течение одного рабочего дня.',
        'count' => 'Всего: :count',
        'sections' => 'Разделы каталога',
        'filters' => 'Фильтры',
        'filter_brand' => 'Бренд',
        'filter_all' => 'Все',
        'filter_stock' => 'Только в наличии',
        'sort' => 'Сортировка',
        'sort_default' => 'Рекомендуемые',
        'sort_cheap' => 'Цена: по возрастанию',
        'sort_dear' => 'Цена: по убыванию',
        'shown' => 'Показано :shown из :total',
        'filter_none' => 'По этим фильтрам ничего не найдено. Попробуйте другой бренд или отключите фильтр наличия.',
        'map_show' => 'Показать карту',
        'map_open' => 'Открыть в OpenStreetMap',
        'map_title' => 'Карта: :store',
        'tagline' => 'Демо админ-панели',
        'footer_menu' => 'Сайт',
        'contact_footer' => 'Контакты',
        'footer_note' => 'Демо-сайт, созданный на Atrium.',
        'photo' => 'Фото :number',
        'order' => 'Заказать товар',
        'back_to_catalog' => 'Назад в каталог',
        'available_in' => 'Есть в',
        'description' => 'О товаре',
        'details' => 'Подробности',
        'related' => 'Хорошо сочетается с',
    ],
];
