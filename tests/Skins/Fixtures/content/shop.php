<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

/*
 * The home goods shop demo (ShopDemoSeeder). Names, prices, addresses and
 * phone numbers are made up. Modules that translate get every language;
 * the others take the default content language's text.
 */
return [
    // The site's name, while it still has the application's.
    'site' => ['en' => 'Shelf', 'de' => 'Regal', 'ru' => 'Полка'],

    'brands' => [
        ['code' => 'clay-and-co', 'name' => ['en' => 'Clay & Co', 'de' => 'Ton & Co', 'ru' => 'Глина и Ко']],
        ['code' => 'glow', 'name' => ['en' => 'GLOW', 'de' => 'LICHT', 'ru' => 'СВЕТ']],
        ['code' => 'workshop-12', 'name' => ['en' => 'Workshop 12', 'de' => 'Werkstatt 12', 'ru' => 'Мастерская 12']],
        ['code' => 'north', 'name' => ['en' => 'North', 'de' => 'Nord', 'ru' => 'Север']],
        ['code' => 'linen-and-home', 'name' => ['en' => 'LINEN&HOME', 'de' => 'LEINEN&HAUS', 'ru' => 'ЛЁН&ДОМ']],
    ],

    'sections' => [
        'tableware' => ['name' => ['en' => 'Tableware', 'de' => 'Geschirr', 'ru' => 'Посуда'], 'picture' => 'shop-bowl.svg'],
        'lighting' => ['name' => ['en' => 'Lighting', 'de' => 'Licht', 'ru' => 'Свет'], 'picture' => 'shop-lamp.svg'],
        'furniture' => ['name' => ['en' => 'Furniture', 'de' => 'Möbel', 'ru' => 'Мебель'], 'picture' => 'shop-chair.svg'],
        'textiles' => ['name' => ['en' => 'Textiles', 'de' => 'Textilien', 'ru' => 'Текстиль'], 'picture' => 'shop-throw.svg'],
        'plants' => ['name' => ['en' => 'Plants', 'de' => 'Pflanzen', 'ru' => 'Растения'], 'picture' => 'shop-plant.svg'],
        'decor' => ['name' => ['en' => 'Decor', 'de' => 'Deko', 'ru' => 'Декор'], 'picture' => 'shop-clock.svg'],
    ],

    'products' => [
        'drop-vase' => [
            'section' => 'decor', 'brand' => 'clay-and-co', 'picture' => 'shop-vase.svg', 'price' => ['en' => 39, 'ru' => 2490], 'label' => 'new',
            'name' => ['en' => 'Drop vase', 'de' => 'Vase „Tropfen“', 'ru' => 'Ваза «Капля»'],
            'text' => ['en' => 'Hand-thrown stoneware, 24 cm.', 'de' => 'Handgedrehtes Steinzeug, 24 cm.', 'ru' => 'Керамика ручной работы, 24 см.'],
        ],
        'desk-lamp' => [
            'section' => 'lighting', 'brand' => 'glow', 'picture' => 'shop-lamp.svg', 'price' => ['en' => 89, 'ru' => 5900],
            'name' => ['en' => 'Desk lamp', 'de' => 'Schreibtischlampe', 'ru' => 'Лампа настольная'],
            'text' => ['en' => 'Warm light, a head that turns all the way.', 'de' => 'Warmes Licht, der Kopf dreht sich ganz herum.', 'ru' => 'Тёплый свет, плафон поворачивается во все стороны.'],
        ],
        'mug' => [
            'section' => 'tableware', 'brand' => 'clay-and-co', 'picture' => 'shop-mug.svg', 'price' => ['en' => 14, 'ru' => 890],
            'name' => ['en' => 'Mug, 350 ml', 'de' => 'Becher, 350 ml', 'ru' => 'Кружка 350 мл'],
            'text' => ['en' => 'Glazed inside, raw clay outside.', 'de' => 'Innen glasiert, außen naturbelassen.', 'ru' => 'Глазурь внутри, необработанная глина снаружи.'],
        ],
        'oak-chair' => [
            'section' => 'furniture', 'brand' => 'workshop-12', 'picture' => 'shop-chair.svg', 'price' => ['en' => 189, 'ru' => 12400], 'label' => 'sale',
            'name' => ['en' => 'Oak chair', 'de' => 'Stuhl „Eiche“', 'ru' => 'Стул «Дуб»'],
            'text' => ['en' => 'Solid oak, oiled by hand.', 'de' => 'Massive Eiche, von Hand geölt.', 'ru' => 'Массив дуба, масло ручного нанесения.'],
        ],
        'terracotta-pot' => [
            'section' => 'plants', 'brand' => 'clay-and-co', 'picture' => 'shop-plant.svg', 'price' => ['en' => 25, 'ru' => 1650],
            'name' => ['en' => 'Terracotta pot', 'de' => 'Terrakottatopf', 'ru' => 'Кашпо терракотовое'],
            'text' => ['en' => 'With a saucer, for plants up to 40 cm.', 'de' => 'Mit Untersetzer, für Pflanzen bis 40 cm.', 'ru' => 'С поддоном, для растений до 40 см.'],
        ],
        'wool-throw' => [
            'section' => 'textiles', 'brand' => 'north', 'picture' => 'shop-throw.svg', 'price' => ['en' => 95, 'ru' => 6300],
            'name' => ['en' => 'Wool throw', 'de' => 'Wolldecke', 'ru' => 'Плед шерстяной'],
            'text' => ['en' => '130 × 180 cm, washable at 30 °C.', 'de' => '130 × 180 cm, waschbar bei 30 °C.', 'ru' => '130 × 180 см, стирка при 30 °C.'],
        ],
        'salad-bowl' => [
            'section' => 'tableware', 'brand' => 'linen-and-home', 'picture' => 'shop-bowl.svg', 'price' => ['en' => 29, 'ru' => 1990],
            'name' => ['en' => 'Salad bowl', 'de' => 'Salatschüssel', 'ru' => 'Миска для салата'],
            'text' => ['en' => 'Ash wood, 28 cm across.', 'de' => 'Eschenholz, 28 cm Durchmesser.', 'ru' => 'Ясень, диаметр 28 см.'],
        ],
        'wall-clock' => [
            'section' => 'decor', 'brand' => 'workshop-12', 'picture' => 'shop-clock.svg', 'price' => ['en' => 59, 'ru' => 3800], 'label' => 'hit',
            'name' => ['en' => 'Wall clock', 'de' => 'Wanduhr', 'ru' => 'Часы настенные'],
            'text' => ['en' => 'A silent movement, 30 cm.', 'de' => 'Lautloses Uhrwerk, 30 cm.', 'ru' => 'Бесшумный механизм, 30 см.'],
        ],
        'wall-shelf' => [
            'section' => 'furniture', 'brand' => 'workshop-12', 'picture' => 'shop-shelf.svg', 'price' => ['en' => 115, 'ru' => 7400],
            'name' => ['en' => 'Wall shelf', 'de' => 'Wandregal', 'ru' => 'Полка навесная'],
            'text' => ['en' => 'Oak, 80 cm, holds 15 kg.', 'de' => 'Eiche, 80 cm, trägt 15 kg.', 'ru' => 'Дуб, 80 см, выдерживает 15 кг.'],
        ],
    ],

    'stores' => [
        'forest-street' => [
            'name' => ['en' => 'On Forest Street', 'de' => 'In der Waldstraße', 'ru' => 'На Лесной'],
            'address' => ['en' => '7 Forest Street', 'de' => 'Waldstraße 7', 'ru' => 'ул. Лесная, 7'],
            'phone' => ['en' => '+44 20 0000 0000', 'de' => '+49 30 0000 0000', 'ru' => '8 800 000-00-00'],
            'hours' => ['en' => '10:00–21:00, seven days a week', 'de' => '10–21 Uhr, ohne Ruhetag', 'ru' => '10:00–21:00 без выходных'],
        ],
        'city-centre' => [
            'name' => ['en' => 'In the centre', 'de' => 'In der Innenstadt', 'ru' => 'В центре'],
            'address' => ['en' => '30 Main Avenue, ground floor', 'de' => 'Hauptallee 30, Erdgeschoss', 'ru' => 'пр. Главный, 30, этаж 1'],
            'phone' => ['en' => '+44 20 0000 0001', 'de' => '+49 30 0000 0001', 'ru' => '8 800 000-00-01'],
            'hours' => ['en' => '11:00–22:00, seven days a week', 'de' => '11–22 Uhr, ohne Ruhetag', 'ru' => '11:00–22:00 без выходных'],
        ],
    ],

    'news' => [
        [1, null, [
            'en' => ['Next-day delivery in town · Pick-up from two stores', 'next-day-delivery', 'Order by 20:00 and we bring it tomorrow.', '<p>Delivery in town is free from €50. Pick-up is free from either store, usually in two hours.</p>'],
            'de' => ['Lieferung in der Stadt am nächsten Tag · Abholung in zwei Geschäften', 'lieferung-naechster-tag', 'Bis 20 Uhr bestellt, morgen geliefert.', '<p>Ab 50 € liefern wir in der Stadt kostenlos. Abholung in beiden Geschäften ist kostenlos, meist nach zwei Stunden.</p>'],
            'ru' => ['Доставка по городу на следующий день · Самовывоз из двух магазинов', 'dostavka-na-sleduyushchij-den', 'Закажите до 20:00 — привезём завтра.', '<p>Доставка по городу бесплатна от 3 000 ₽. Самовывоз из любого магазина бесплатный, обычно через два часа.</p>'],
        ]],
        [9, 'shop-vase.svg', [
            'en' => ['The new collection is in the stores', 'new-collection', 'Stoneware from Clay & Co and throws from North.', '<p>Come and touch everything: every piece is on display in both stores.</p>'],
            'de' => ['Die neue Kollektion ist da', 'neue-kollektion', 'Steinzeug von Ton & Co und Decken von Nord.', '<p>Kommen Sie vorbei und fassen Sie alles an: Jedes Stück steht in beiden Geschäften aus.</p>'],
            'ru' => ['Новая коллекция уже в магазинах', 'novaya-kollekciya', 'Керамика «Глины и Ко» и пледы «Севера».', '<p>Приходите потрогать: каждая вещь выставлена в обоих магазинах.</p>'],
        ]],
    ],

    'reviews' => [
        ['product' => 'wool-throw', 'rating' => 5, 'author' => ['en' => 'Vera', 'de' => 'Vera', 'ru' => 'Вера'], 'text' => ['en' => 'Soft, does not prickle, and it survived the washing machine.', 'de' => 'Weich, kratzt nicht und hat die Waschmaschine überstanden.', 'ru' => 'Мягкий, не колется и пережил стиральную машину.']],
        ['product' => 'drop-vase', 'rating' => 5, 'author' => ['en' => 'Alex', 'de' => 'Alex', 'ru' => 'Алексей'], 'text' => ['en' => 'Even nicer than in the photo, and it came packed like a treasure.', 'de' => 'Noch schöner als auf dem Foto und verpackt wie ein Schatz.', 'ru' => 'Вживую ещё красивее, а упаковали как сокровище.']],
        ['product' => 'oak-chair', 'rating' => 4, 'author' => ['en' => 'Nina', 'de' => 'Nina', 'ru' => 'Нина'], 'text' => ['en' => 'A solid chair. Delivery took a day longer than promised.', 'de' => 'Ein solider Stuhl. Die Lieferung dauerte einen Tag länger als versprochen.', 'ru' => 'Крепкий стул. Доставка заняла на день больше обещанного.']],
    ],

    'faq' => [
        [[
            'en' => ['For buyers', 'buyers'],
            'de' => ['Für Kunden', 'kunden'],
            'ru' => ['Покупателям', 'pokupatelyam'],
        ], [
            ['en' => ['How fast do you deliver?', 'The next day in town; up to five days elsewhere.'], 'de' => ['Wie schnell liefern Sie?', 'In der Stadt am nächsten Tag, sonst bis zu fünf Tage.'], 'ru' => ['Как быстро доставите?', 'По городу — на следующий день, в другие города — до пяти дней.']],
            ['en' => ['Can I return something?', 'Within 30 days, unused and in its packaging.'], 'de' => ['Kann ich etwas zurückgeben?', 'Innerhalb von 30 Tagen, unbenutzt und in der Verpackung.'], 'ru' => ['Можно вернуть товар?', 'В течение 30 дней, без следов использования и в упаковке.']],
            ['en' => ['Do you wrap gifts?', 'Yes, for free: say so in the order comment.'], 'de' => ['Packen Sie Geschenke ein?', 'Ja, kostenlos: Schreiben Sie es in den Kommentar zur Bestellung.'], 'ru' => ['Упакуете в подарок?', 'Да, бесплатно — напишите об этом в комментарии к заказу.']],
        ]],
    ],

    'forms' => [
        'question' => [
            'name' => ['en' => 'Ask a question', 'de' => 'Frage stellen', 'ru' => 'Задать вопрос'],
            'button' => ['en' => 'Send', 'de' => 'Senden', 'ru' => 'Отправить'],
            'success' => ['en' => 'Thank you! We will answer by e-mail within a day.', 'de' => 'Danke! Wir antworten innerhalb eines Tages per E-Mail.', 'ru' => 'Спасибо! Ответим на почту в течение дня.'],
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => ['en' => 'Name', 'de' => 'Name', 'ru' => 'Имя']],
                ['name' => 'email', 'type' => 'email', 'required' => true, 'label' => ['en' => 'E-mail', 'de' => 'E-Mail', 'ru' => 'Почта']],
                ['name' => 'message', 'type' => 'textarea', 'required' => true, 'label' => ['en' => 'Question', 'de' => 'Frage', 'ru' => 'Вопрос']],
            ],
        ],
    ],

    'pages' => [
        'home' => [
            'name' => ['en' => 'Things for the home that last for years', 'de' => 'Dinge für Zuhause, die Jahre halten', 'ru' => 'Вещи для дома, которые служат годами'],
            'content' => ['en' => '<p>Tableware, lighting and furniture from small workshops. We try every piece at home first.</p>', 'de' => '<p>Geschirr, Licht und Möbel aus kleinen Werkstätten. Jedes Stück testen wir zuerst bei uns zu Hause.</p>', 'ru' => '<p>Посуда, свет и мебель от небольших мастерских. Каждую вещь мы сначала проверяем у себя дома.</p>'],
        ],
        'delivery' => [
            'name' => ['en' => 'Delivery and payment', 'de' => 'Lieferung und Zahlung', 'ru' => 'Доставка и оплата'],
            'content' => ['en' => '<p>Next-day delivery in town, free pick-up from both stores. Pay by card online or on delivery.</p>', 'de' => '<p>Lieferung in der Stadt am nächsten Tag, kostenlose Abholung in beiden Geschäften. Zahlung per Karte online oder bei Lieferung.</p>', 'ru' => '<p>Доставка по городу на следующий день, бесплатный самовывоз из обоих магазинов. Оплата картой на сайте или при получении.</p>'],
        ],
    ],
];
