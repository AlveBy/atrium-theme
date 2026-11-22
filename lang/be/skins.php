<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

// Тэксты скінаў (config atrium-theme.skin). Сам змест бярэцца з
// модуляў; тут загалоўкі і подпісы вакол яго.
return [
    'rating' => ':rating з 5',
    'review' => 'Водгук',

    'travel' => [
        'modules' => ['catalog' => 'Туры', 'news' => 'Навіны', 'stores' => 'Офісы', 'faq' => 'Пытанні'],
        'price' => 'ад :price',
        'eyebrow' => 'Туры па краіне і за мяжу',
        'catalog_title' => 'Куды едуць гэтым летам',
        'catalog_all' => 'Усе напрамкі',
        'reviews_title' => 'Што кажуць падарожнікі',
        'faq_title' => 'Частыя пытанні',
        'callback_title' => 'Не знайшлі патрэбны тур?',
        'callback_text' => 'Пакіньце нумар тэлефона, і мы патэлефануем на працягу гадзіны ў працоўны час.',
        'tagline' => 'турыстычнае агенцтва',
    ],

    'restaurant' => [
        'modules' => ['catalog' => 'Меню', 'news' => 'Падзеі', 'stores' => 'Адрасы', 'faq' => 'Пытанні'],
        'price' => ':price',
        'eyebrow' => 'Хатняя кухня',
        'hero_menu' => 'Паглядзець меню',
        'menu_title' => 'Меню',
        'menu_text' => 'Мяняем яго кожны сезон, стравы дня — на дошцы каля ўваходу.',
        'stores_title' => 'Дзе нас знайсці',
        'directions' => 'Як дабрацца',
        'booking_title' => 'Забраніраваць столік',
        'booking_text' => 'Пацвердзім браніраванне тэлефонным званком або паведамленнем.',
        'book' => 'Забраніраваць столік',
    ],

    'clinic' => [
        'modules' => ['catalog' => 'Паслугі', 'news' => 'Навіны', 'stores' => 'Філіялы', 'faq' => 'Пытанні'],
        'price' => 'ад :price',
        'services_title' => 'Паслугі',
        'services_all' => 'Усе паслугі',
        'doctors_all' => 'Усе лекары',
        'news_title' => 'Навіны клінікі',
        'faq_title' => 'Пытанні і адказы',
        'book' => 'Запісацца на прыём',
        'disclaimer' => 'Перад любым лячэннем звярніцеся да спецыяліста.',
    ],

    'shop' => [
        'modules' => ['catalog' => 'Каталог', 'news' => 'Навіны', 'stores' => 'Крамы', 'faq' => 'Дапамога'],
        'price' => ':price',
        'eyebrow' => 'Новая калекцыя',
        'catalog_button' => 'Паглядзець каталог',
        'products_title' => 'Тавары',
        'sections' => 'Раздзелы каталога',
        'all' => 'Усё',
        'brands_title' => 'Брэнды',
        'stores_title' => 'Крамы',
        'tagline' => 'Рэчы для дома',
    ],

    'atrium' => [
        'modules' => ['catalog' => 'Каталог'],
        'price' => ':price',
        'in_stock' => 'В наяўнасці',
        'out_of_stock' => 'Няма ў наяўнасці',
        'hero_title' => 'Увесь сайт — з адной панэлі',
        'hero_text' => 'Тавары, навіны, крамы, пытанні і формы: кожная старонка тут кіруецца ў Atrium, на 15 мовах.',
        'hero_catalog' => 'Паглядзець каталог',
        'hero_stores' => 'Знайсці краму',
        'brands_title' => 'Брэнды ў асартыменце',
        'sections_title' => 'Купляйце па раздзелах',
        'products_title' => 'Новае ў каталогу',
        'products_all' => 'Усе тавары',
        'news_title' => 'Апошнія навіны',
        'news_all' => 'Усе навіны',
        'stores_title' => 'Завітайце ў краму',
        'stores_all' => 'Усе крамы',
        'team_title' => 'Людзі за праектам',
        'team_all' => 'Пазнаёміцца з камандай',
        'reviews_title' => 'Што кажуць пакупнікі',
        'faq_title' => 'Пытанні і адказы',
        'contact_title' => 'Напішыце нам',
        'contact_text' => 'Пакіньце свае даныя, і мы адкажам на працягу аднаго рабочага дня.',
        'count' => 'Усяго: :count',
        'sections' => 'Раздзелы каталога',
        'filters' => 'Фільтры',
        'filter_brand' => 'Брэнд',
        'filter_all' => 'Усе',
        'filter_stock' => 'Толькі ў наяўнасці',
        'sort' => 'Сартаванне',
        'sort_default' => 'Рэкамендаваныя',
        'sort_cheap' => 'Цана: ад меншай да большай',
        'sort_dear' => 'Цана: ад большай да меншай',
        'shown' => 'Паказана :shown з :total',
        'filter_none' => 'Па гэтых фільтрах нічога не знойдзена. Паспрабуйце іншы брэнд або адключыце фільтр наяўнасці.',
        'map_show' => 'Паказаць карту',
        'map_open' => 'Адкрыць у OpenStreetMap',
        'map_title' => 'Карта: :store',
        'tagline' => 'Дэмаверсія панэлі адміністравання',
        'footer_menu' => 'Сайт',
        'contact_footer' => 'Кантакты',
        'footer_note' => 'Дэмасайт, створаны на Atrium.',
        'photo' => 'Фота :number',
        'order' => 'Замовіць тавар',
        'back_to_catalog' => 'Назад у каталог',
        'available_in' => 'Даступна ў',
        'description' => 'Пра тавар',
        'details' => 'Падрабязнасці',
        'related' => 'Добра спалучаецца з',
    ],
];
