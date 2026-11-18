<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

/*
 * The restaurant demo (RestaurantDemoSeeder). Names, prices, addresses and
 * phone numbers are made up. Modules that translate get every language;
 * the others take the default content language's text.
 */
$dish = static fn (string $section, array $name, array $text, int $euro, int $rouble, ?string $label = null): array => [
    'section' => $section, 'picture' => "restaurant-{$section}.svg", 'name' => $name, 'text' => $text,
    'price' => ['en' => $euro, 'ru' => $rouble], 'label' => $label,
];

return [
    // The site's name, while it still has the application's.
    'site' => ['en' => 'Caraway', 'de' => 'Kümmel', 'ru' => 'Тмин'],

    'brands' => [
        ['code' => 'riverside-farm', 'name' => ['en' => 'Riverside Farm', 'de' => 'Hof am Fluss', 'ru' => 'Ферма «Заречье»']],
        ['code' => 'golden-ear-mill', 'name' => ['en' => 'Golden Ear Mill', 'de' => 'Mühle Goldähre', 'ru' => 'Мельница «Колос»']],
    ],

    'sections' => [
        'breakfast' => ['name' => ['en' => 'Breakfast', 'de' => 'Frühstück', 'ru' => 'Завтраки'], 'picture' => 'restaurant-breakfast.svg'],
        'mains' => ['name' => ['en' => 'Main menu', 'de' => 'Hauptkarte', 'ru' => 'Основное меню'], 'picture' => 'restaurant-mains.svg'],
        'desserts' => ['name' => ['en' => 'Desserts', 'de' => 'Desserts', 'ru' => 'Десерты'], 'picture' => 'restaurant-desserts.svg'],
        'drinks' => ['name' => ['en' => 'Drinks', 'de' => 'Getränke', 'ru' => 'Напитки'], 'picture' => 'restaurant-drinks.svg'],
    ],

    'products' => [
        'syrniki' => $dish('breakfast', ['en' => 'Cottage cheese pancakes with berries', 'de' => 'Quarkpfannkuchen mit Beeren', 'ru' => 'Сырники со сметаной и ягодами'], ['en' => 'Farm cottage cheese, honey', 'de' => 'Hofquark, Honig', 'ru' => 'Творог фермерский, мёд'], 9, 420, 'hit'),
        'mushroom-omelette' => $dish('breakfast', ['en' => 'Omelette with forest mushrooms', 'de' => 'Omelett mit Waldpilzen', 'ru' => 'Омлет с лесными грибами'], ['en' => 'Spring onion, sour cream', 'de' => 'Frühlingszwiebel, Schmand', 'ru' => 'Зелёный лук, сметана'], 8, 380),
        'oat-porridge' => $dish('breakfast', ['en' => 'Oat porridge with berries', 'de' => 'Haferbrei mit Beeren', 'ru' => 'Овсяная каша с ягодами'], ['en' => 'Honey, walnuts', 'de' => 'Honig, Walnüsse', 'ru' => 'Мёд, грецкий орех'], 6, 290),
        'beef-tartare' => $dish('mains', ['en' => 'Beef tartare', 'de' => 'Rindertatar', 'ru' => 'Тартар из говядины'], ['en' => 'Egg yolk, capers, rye bread', 'de' => 'Eigelb, Kapern, Roggenbrot', 'ru' => 'Желток, каперсы, бородинский хлеб'], 14, 690),
        'duck-confit' => $dish('mains', ['en' => 'Duck leg confit', 'de' => 'Confierte Entenkeule', 'ru' => 'Утиная ножка конфи'], ['en' => 'Baked pear, cherry sauce', 'de' => 'Gebackene Birne, Kirschsauce', 'ru' => 'Печёная груша, соус из вишни'], 21, 980),
        'porcini-risotto' => $dish('mains', ['en' => 'Porcini risotto', 'de' => 'Steinpilzrisotto', 'ru' => 'Ризотто с белыми грибами'], ['en' => 'Parmesan, thyme', 'de' => 'Parmesan, Thymian', 'ru' => 'Пармезан, тимьян'], 16, 760),
        'pike-cakes' => $dish('mains', ['en' => 'Pike fish cakes', 'de' => 'Hechtfrikadellen', 'ru' => 'Щучьи котлеты'], ['en' => 'Celeriac purée, dill oil', 'de' => 'Selleriepüree, Dillöl', 'ru' => 'Пюре из сельдерея, укропное масло'], 15, 720),
        'honey-cake' => $dish('desserts', ['en' => 'Honey cake', 'de' => 'Honigtorte', 'ru' => 'Медовик'], ['en' => 'Sour cream, salted caramel', 'de' => 'Schmandcreme, Salzkaramell', 'ru' => 'Сметанный крем, солёная карамель'], 7, 380, 'hit'),
        'lingonberry-pie' => $dish('desserts', ['en' => 'Lingonberry pie', 'de' => 'Preiselbeerkuchen', 'ru' => 'Пирог с брусникой'], ['en' => 'Shortcrust, vanilla sauce', 'de' => 'Mürbeteig, Vanillesauce', 'ru' => 'Песочное тесто, ванильный соус'], 7, 360, 'new'),
        'sea-buckthorn-lemonade' => $dish('drinks', ['en' => 'Sea buckthorn lemonade', 'de' => 'Sanddornlimonade', 'ru' => 'Лимонад с облепихой'], ['en' => 'Sea buckthorn, mint', 'de' => 'Sanddorn, Minze', 'ru' => 'Облепиха, мята'], 5, 290),
        'herbal-tea' => $dish('drinks', ['en' => 'Fireweed tea with thyme', 'de' => 'Weidenröschentee mit Thymian', 'ru' => 'Иван-чай с чабрецом'], ['en' => 'A pot for two', 'de' => 'Eine Kanne für zwei', 'ru' => 'Чайник на двоих'], 4, 250),
    ],

    'stores' => [
        'garden-street' => [
            'name' => ['en' => 'On Garden Street', 'de' => 'In der Gartenstraße', 'ru' => 'На Садовой'],
            'address' => ['en' => '12 Garden Street', 'de' => 'Gartenstraße 12', 'ru' => 'ул. Садовая, 12'],
            'phone' => ['en' => '+44 20 0000 0001', 'de' => '+49 30 0000 0001', 'ru' => '8 800 000-00-01'],
            'hours' => ['en' => 'Daily 9:00–23:00', 'de' => 'Täglich 9–23 Uhr', 'ru' => 'Каждый день 9:00–23:00'],
        ],
        'by-the-river' => [
            'name' => ['en' => 'By the river', 'de' => 'Am Fluss', 'ru' => 'У реки'],
            'address' => ['en' => '4 Embankment, first floor', 'de' => 'Uferpromenade 4, 1. Stock', 'ru' => 'Набережная, 4, второй этаж'],
            'phone' => ['en' => '+44 20 0000 0002', 'de' => '+49 30 0000 0002', 'ru' => '8 800 000-00-02'],
            'hours' => ['en' => 'Daily 12:00–24:00', 'de' => 'Täglich 12–24 Uhr', 'ru' => 'Каждый день 12:00–00:00'],
        ],
    ],

    'news' => [
        [4, 'restaurant-mains.svg', [
            'en' => ['The new season’s menu', 'new-season-menu', 'Porcini risotto, duck with baked pear and a lingonberry pie.', '<p>Every season we rewrite the menu around what the farms nearby bring us. Dishes of the day are on the board by the door.</p>'],
            'de' => ['Die neue Saisonkarte', 'neue-saisonkarte', 'Steinpilzrisotto, Ente mit gebackener Birne und Preiselbeerkuchen.', '<p>Jede Saison schreiben wir die Karte neu – nach dem, was die Höfe in der Nähe liefern. Tagesgerichte stehen auf der Tafel am Eingang.</p>'],
            'ru' => ['Новое сезонное меню', 'novoe-sezonnoe-menyu', 'Ризотто с белыми грибами, утка с печёной грушей и пирог с брусникой.', '<p>Каждый сезон переписываем меню под то, что привозят соседние фермы. Блюда дня — на доске у входа.</p>'],
        ]],
        [11, 'restaurant-desserts.svg', [
            'en' => ['Dinner with the farmers', 'dinner-with-the-farmers', 'On the last Friday of the month the farmers who supply us cook with our chef.', '<p>Five courses, one long table. Book a seat by phone or with the form on the home page.</p>'],
            'de' => ['Abendessen mit den Bauern', 'abendessen-mit-den-bauern', 'Am letzten Freitag im Monat kochen unsere Lieferhöfe mit dem Küchenchef.', '<p>Fünf Gänge, eine lange Tafel. Plätze per Telefon oder über das Formular auf der Startseite.</p>'],
            'ru' => ['Ужин с фермерами', 'uzhin-s-fermerami', 'В последнюю пятницу месяца фермеры, которые нас снабжают, готовят вместе с шефом.', '<p>Пять подач, один длинный стол. Места бронируйте по телефону или через форму на главной.</p>'],
        ]],
    ],

    'reviews' => [
        ['product' => 'honey-cake', 'rating' => 5, 'author' => ['en' => 'Marina', 'de' => 'Marina', 'ru' => 'Марина'], 'text' => ['en' => 'Honey cake like my grandmother’s, only better. The whole family has come here on Sundays for two years.', 'de' => 'Honigtorte wie bei Oma, nur besser. Seit zwei Jahren kommen wir sonntags mit der ganzen Familie.', 'ru' => 'Медовик как у бабушки, только лучше. Ходим всей семьёй по воскресеньям уже второй год.']],
        ['product' => 'beef-tartare', 'rating' => 5, 'author' => ['en' => 'Pavel', 'de' => 'Pavel', 'ru' => 'Павел'], 'text' => ['en' => 'The tartare is the best I have had in town, and the staff remembered our table.', 'de' => 'Das beste Tatar der Stadt, und das Personal hat sich unseren Tisch gemerkt.', 'ru' => 'Лучший тартар в городе, а официанты запомнили наш столик.']],
        ['product' => 'syrniki', 'rating' => 4, 'author' => ['en' => 'Kate', 'de' => 'Katja', 'ru' => 'Катя'], 'text' => ['en' => 'Lovely breakfasts. On Saturday morning book ahead, it fills up.', 'de' => 'Wunderbares Frühstück. Samstags vorher reservieren, es wird voll.', 'ru' => 'Чудесные завтраки. В субботу утром лучше бронировать — бывает полно.']],
    ],

    'faq' => [
        [[
            'en' => ['Visiting us', 'visiting'],
            'de' => ['Ihr Besuch', 'besuch'],
            'ru' => ['Посещение', 'poseshchenie'],
        ], [
            ['en' => ['Can we come with children?', 'Of course: there are high chairs, a children’s menu and crayons.'], 'de' => ['Können wir mit Kindern kommen?', 'Natürlich: Es gibt Kinderstühle, eine Kinderkarte und Buntstifte.'], 'ru' => ['Можно ли с детьми?', 'Конечно: есть детские стулья, детское меню и карандаши.']],
            ['en' => ['Do you have vegetarian dishes?', 'Yes, they are marked on the menu, and the chef will adapt most dishes.'], 'de' => ['Gibt es vegetarische Gerichte?', 'Ja, sie sind auf der Karte markiert, und der Küchenchef passt die meisten Gerichte an.'], 'ru' => ['Есть вегетарианские блюда?', 'Да, они отмечены в меню, а большинство блюд шеф приготовит без мяса.']],
            ['en' => ['Do you host banquets?', 'Up to 40 guests by the river; we will put the menu together with you.'], 'de' => ['Richten Sie Feiern aus?', 'Bis zu 40 Gäste am Fluss; das Menü stellen wir mit Ihnen zusammen.'], 'ru' => ['Проводите банкеты?', 'До 40 гостей в зале у реки, меню составим вместе с вами.']],
        ]],
    ],

    'forms' => [
        'table-booking' => [
            'name' => ['en' => 'Table booking', 'de' => 'Tischreservierung', 'ru' => 'Бронь стола'],
            'button' => ['en' => 'Book', 'de' => 'Reservieren', 'ru' => 'Забронировать'],
            'success' => ['en' => 'Thank you! We will confirm by phone or message.', 'de' => 'Danke! Wir bestätigen per Anruf oder Nachricht.', 'ru' => 'Спасибо! Подтвердим бронь звонком или сообщением.'],
            'fields' => [
                ['name' => 'date', 'type' => 'date', 'required' => true, 'label' => ['en' => 'Date', 'de' => 'Datum', 'ru' => 'Дата']],
                ['name' => 'time', 'type' => 'select', 'required' => true, 'label' => ['en' => 'Time', 'de' => 'Uhrzeit', 'ru' => 'Время'], 'options' => "12:00\n13:00\n14:00\n15:00\n16:00\n17:00\n18:00\n19:00\n19:30\n20:00\n21:00\n22:00"],
                ['name' => 'guests', 'type' => 'number', 'required' => true, 'label' => ['en' => 'Guests', 'de' => 'Gäste', 'ru' => 'Гостей']],
                ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => ['en' => 'Phone', 'de' => 'Telefon', 'ru' => 'Телефон']],
            ],
        ],
    ],

    'pages' => [
        'home' => [
            'name' => ['en' => 'Home cooking you will want to repeat', 'de' => 'Küche zum Wiederkommen', 'ru' => 'Кухня, которую хочется повторить'],
            'content' => ['en' => '<p>We cook with what grows nearby: farm cottage cheese, river fish, mushrooms and berries in season.</p>', 'de' => '<p>Wir kochen mit dem, was in der Nähe wächst: Hofquark, Flussfisch, Pilze und Beeren der Saison.</p>', 'ru' => '<p>Готовим из того, что растёт рядом: фермерский творог, речная рыба, грибы и ягоды по сезону.</p>'],
        ],
        'delivery' => [
            'name' => ['en' => 'Order delivery', 'de' => 'Lieferung bestellen', 'ru' => 'Заказать доставку'],
            'content' => ['en' => '<p>We deliver the whole menu except the tartare, daily from 12:00 to 22:00. Call either restaurant to order.</p>', 'de' => '<p>Wir liefern die ganze Karte außer dem Tatar, täglich von 12 bis 22 Uhr. Bestellen Sie telefonisch in einem der beiden Restaurants.</p>', 'ru' => '<p>Привезём всё меню, кроме тартара, каждый день с 12:00 до 22:00. Заказ — по телефону любого из ресторанов.</p>'],
        ],
    ],
];
