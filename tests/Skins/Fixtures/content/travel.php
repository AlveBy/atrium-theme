<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

/*
 * The travel agency demo (TravelDemoSeeder). Names, prices, addresses and
 * phone numbers are made up. Modules that translate get every language;
 * the others take the default content language's text.
 */
return [
    // The site's name, while it still has the application's.
    'site' => ['en' => 'Horizon', 'de' => 'Horizont', 'ru' => 'Горизонт'],

    'brands' => [
        ['code' => 'north-wind', 'name' => ['en' => 'North Wind Air', 'de' => 'Nordwind Air', 'ru' => 'Северный ветер']],
        ['code' => 'mountain-trail', 'name' => ['en' => 'Mountain Trail Guides', 'de' => 'Bergpfad-Guides', 'ru' => 'Горная тропа']],
        ['code' => 'quiet-harbour', 'name' => ['en' => 'Quiet Harbour Hotels', 'de' => 'Stiller Hafen Hotels', 'ru' => 'Тихая гавань']],
    ],

    'sections' => [
        'nature' => ['name' => ['en' => 'Nature', 'de' => 'Natur', 'ru' => 'Природа'], 'picture' => 'travel-coast.svg'],
        'cities' => ['name' => ['en' => 'Cities and culture', 'de' => 'Städte und Kultur', 'ru' => 'Города и культура'], 'picture' => 'travel-georgia.svg'],
    ],

    'products' => [
        'karelia' => [
            'section' => 'nature', 'brand' => 'quiet-harbour', 'picture' => 'travel-karelia.svg', 'price' => ['en' => 590, 'ru' => 38000],
            'name' => ['en' => 'Karelia', 'de' => 'Karelien', 'ru' => 'Карелия'],
            'text' => ['en' => '7 days · lakes and skerries', 'de' => '7 Tage · Seen und Schären', 'ru' => '7 дней · озёра и шхеры'],
            'detail' => ['en' => '<p>A week among lakes and pine forests: a boat trip through the skerries, a night in a wooden guesthouse and a sauna by the water.</p>', 'de' => '<p>Eine Woche zwischen Seen und Kiefernwäldern: Bootsfahrt durch die Schären, eine Nacht im Holzgasthaus und Sauna am Wasser.</p>', 'ru' => '<p>Неделя среди озёр и сосновых лесов: прогулка на лодке по шхерам, ночь в деревянном гостевом доме и баня у воды.</p>'],
        ],
        'altai' => [
            'section' => 'nature', 'brand' => 'mountain-trail', 'picture' => 'travel-altai.svg', 'price' => ['en' => 790, 'ru' => 54000], 'label' => 'hit',
            'name' => ['en' => 'Altai', 'de' => 'Altai', 'ru' => 'Алтай'],
            'text' => ['en' => '10 days · mountains and passes', 'de' => '10 Tage · Berge und Pässe', 'ru' => '10 дней · горы и перевалы'],
            'detail' => ['en' => '<p>Mountain roads, turquoise rivers and nights under the stars. Easy walks every day, no climbing gear needed.</p>', 'de' => '<p>Bergstraßen, türkisfarbene Flüsse und Nächte unter Sternen. Jeden Tag leichte Wanderungen, keine Kletterausrüstung nötig.</p>', 'ru' => '<p>Горные дороги, бирюзовые реки и ночи под звёздами. Каждый день лёгкие прогулки, снаряжение не нужно.</p>'],
        ],
        'georgia' => [
            'section' => 'cities', 'brand' => 'north-wind', 'picture' => 'travel-georgia.svg', 'price' => ['en' => 890, 'ru' => 61000], 'label' => 'new',
            'name' => ['en' => 'Georgia', 'de' => 'Georgien', 'ru' => 'Грузия'],
            'text' => ['en' => '8 days · wine and old towns', 'de' => '8 Tage · Wein und Altstädte', 'ru' => '8 дней · вино и старые города'],
            'detail' => ['en' => '<p>Old towns, a family winery and a cooking class. Flights are included.</p>', 'de' => '<p>Altstädte, ein Familienweingut und ein Kochkurs. Flüge inklusive.</p>', 'ru' => '<p>Старые города, семейная винодельня и кулинарный мастер-класс. Перелёт включён.</p>'],
        ],
        'baikal' => [
            'section' => 'nature', 'picture' => 'travel-baikal.svg', 'price' => ['en' => 690, 'ru' => 47000],
            'name' => ['en' => 'Baikal', 'de' => 'Baikal', 'ru' => 'Байкал'],
            'text' => ['en' => '6 days · ice and islands', 'de' => '6 Tage · Eis und Inseln', 'ru' => '6 дней · лёд и острова'],
            'detail' => ['en' => '<p>Clear ice you can see through, an island village and a hovercraft ride.</p>', 'de' => '<p>Klares Eis, durch das man hindurchsieht, ein Inseldorf und eine Fahrt mit dem Luftkissenboot.</p>', 'ru' => '<p>Прозрачный лёд, островная деревня и поездка на судне на воздушной подушке.</p>'],
        ],
        'kamchatka' => [
            'section' => 'nature', 'brand' => 'north-wind', 'picture' => 'travel-kamchatka.svg', 'price' => ['en' => 1690, 'ru' => 120000],
            'name' => ['en' => 'Kamchatka', 'de' => 'Kamtschatka', 'ru' => 'Камчатка'],
            'text' => ['en' => '12 days · volcanoes and the ocean', 'de' => '12 Tage · Vulkane und Ozean', 'ru' => '12 дней · вулканы и океан'],
        ],
        'armenia' => [
            'section' => 'cities', 'picture' => 'travel-armenia.svg', 'price' => ['en' => 740, 'ru' => 52000], 'label' => 'sale',
            'name' => ['en' => 'Armenia', 'de' => 'Armenien', 'ru' => 'Армения'],
            'text' => ['en' => '7 days · monasteries and mountain lakes', 'de' => '7 Tage · Klöster und Bergseen', 'ru' => '7 дней · монастыри и горные озёра'],
        ],
    ],

    'stores' => [
        'head-office' => [
            'name' => ['en' => 'Head office', 'de' => 'Hauptbüro', 'ru' => 'Главный офис'],
            'address' => ['en' => '5 Sea Street', 'de' => 'Meerstraße 5', 'ru' => 'ул. Морская, 5'],
            'phone' => ['en' => '+44 20 0000 0000', 'de' => '+49 30 0000 0000', 'ru' => '8 800 000-00-00'],
            'hours' => ['en' => 'Mon–Fri 10:00–19:00, Sat 11:00–16:00', 'de' => 'Mo–Fr 10–19 Uhr, Sa 11–16 Uhr', 'ru' => 'Пн–Пт 10:00–19:00, сб 11:00–16:00'],
            'text' => ['en' => 'Come in for a coffee and a map: we plan trips at a big table.', 'de' => 'Kommen Sie auf einen Kaffee vorbei: Wir planen Reisen an einem großen Tisch.', 'ru' => 'Заходите на кофе: маршруты собираем за большим столом с картой.'],
        ],
    ],

    'news' => [
        [3, 'travel-kamchatka.svg', [
            'en' => ['Kamchatka for next summer is on sale', 'kamchatka-next-summer', 'Groups of eight, three dates in July and August.', '<p>Volcanoes, hot springs and a boat trip on the ocean. We keep the groups small, so places go fast.</p>'],
            'de' => ['Kamtschatka für den nächsten Sommer buchbar', 'kamtschatka-naechster-sommer', 'Gruppen zu acht, drei Termine im Juli und August.', '<p>Vulkane, heiße Quellen und eine Bootsfahrt auf dem Ozean. Die Gruppen sind klein, die Plätze schnell weg.</p>'],
            'ru' => ['Открыли продажи туров на Камчатку на следующее лето', 'kamchatka-sleduyushchee-leto', 'Группы по восемь человек, три даты в июле и августе.', '<p>Вулканы, горячие источники и выход в океан. Группы маленькие, места разбирают быстро.</p>'],
        ]],
        [12, 'travel-armenia.svg', [
            'en' => ['A new route through Armenia', 'new-route-armenia', 'Seven days from Yerevan to the mountain lakes.', '<p>Monasteries on the cliffs, a night by Lake Sevan and a dinner with a local family.</p>'],
            'de' => ['Neue Route durch Armenien', 'neue-route-armenien', 'Sieben Tage von Jerewan zu den Bergseen.', '<p>Klöster auf den Felsen, eine Nacht am Sewansee und ein Abendessen bei einer Familie.</p>'],
            'ru' => ['Новый маршрут по Армении', 'novyj-marshrut-armeniya', 'Семь дней от Еревана до горных озёр.', '<p>Монастыри на скалах, ночь у Севана и ужин в армянской семье.</p>'],
        ]],
    ],

    'reviews' => [
        ['product' => 'georgia', 'rating' => 5, 'author' => ['en' => 'Olga', 'de' => 'Olga', 'ru' => 'Ольга'], 'text' => ['en' => 'Our manager answered even at night when the flight was delayed. We felt calm the whole trip.', 'de' => 'Unsere Betreuerin hat sogar nachts geantwortet, als der Flug verspätet war. Die ganze Reise über waren wir entspannt.', 'ru' => 'Менеджер отвечал даже ночью, когда задержали рейс. Спокойно было всю поездку.']],
        ['product' => 'altai', 'rating' => 5, 'author' => ['en' => 'Dmitry', 'de' => 'Dmitri', 'ru' => 'Дмитрий'], 'text' => ['en' => 'We went with the children. The route was made for us, not a single long drive.', 'de' => 'Wir waren mit den Kindern unterwegs. Die Route war auf uns zugeschnitten, keine einzige lange Fahrt.', 'ru' => 'Брали тур с детьми. Маршрут подстроили под нас, ни одного длинного переезда.']],
        ['product' => 'karelia', 'rating' => 4, 'author' => ['en' => 'Anna', 'de' => 'Anna', 'ru' => 'Анна'], 'text' => ['en' => 'Every detail was thought through: they met us at the station and the guide knew every lake. I want to go back.', 'de' => 'Alles war bis ins Detail durchdacht: Abholung am Bahnhof, und der Guide kannte jeden See. Ich will wieder hin.', 'ru' => 'Всё продумано до мелочей: встретили на вокзале, гид знал каждое озеро. Хочется вернуться.']],
    ],

    'faq' => [
        [[
            'en' => ['Booking a tour', 'booking'],
            'de' => ['Buchung', 'buchung'],
            'ru' => ['Бронирование', 'bronirovanie'],
        ], [
            ['en' => ['Can I pay in instalments?', 'Yes: 30% up front, the rest two weeks before the trip.'], 'de' => ['Kann ich in Raten zahlen?', 'Ja: 30 % Anzahlung, der Rest zwei Wochen vor der Reise.'], 'ru' => ['Можно ли оплатить частями?', 'Да: предоплата 30 %, остальное за две недели до поездки.']],
            ['en' => ['What if the trip falls through?', 'We refund everything the suppliers refund us and help you move the dates.'], 'de' => ['Was, wenn die Reise ausfällt?', 'Wir erstatten alles, was uns die Anbieter erstatten, und helfen beim Verschieben.'], 'ru' => ['Что, если поездка сорвётся?', 'Вернём деньги за всё, что поставщики возвращают нам, и поможем перенести даты.']],
            ['en' => ['Do I need a visa?', 'We will tell you for every country and put the documents together if needed.'], 'de' => ['Brauche ich ein Visum?', 'Wir sagen es Ihnen für jedes Land und stellen bei Bedarf die Unterlagen zusammen.'], 'ru' => ['Нужна ли виза?', 'Подскажем по каждой стране и при необходимости соберём документы.']],
            ['en' => ['Are there tours for one person?', 'Yes: a group or a private trip, without a single supplement where possible.'], 'de' => ['Gibt es Reisen für eine Person?', 'Ja: in einer Gruppe oder privat, wo möglich ohne Einzelzimmerzuschlag.'], 'ru' => ['Есть туры для одного человека?', 'Да, подберём группу или индивидуальную поездку без доплаты за одноместность там, где это возможно.']],
        ]],
    ],

    'forms' => [
        'tour-request' => [
            'name' => ['en' => 'Tour request', 'de' => 'Reiseanfrage', 'ru' => 'Подбор тура'],
            'button' => ['en' => 'Find me a tour', 'de' => 'Reise finden', 'ru' => 'Подобрать тур'],
            'success' => ['en' => 'Thank you! We will call you back with ideas within a working day.', 'de' => 'Danke! Wir melden uns innerhalb eines Werktags mit Vorschlägen.', 'ru' => 'Спасибо! Перезвоним с вариантами в течение рабочего дня.'],
            'fields' => [
                ['name' => 'where', 'type' => 'text', 'label' => ['en' => 'Where to', 'de' => 'Wohin', 'ru' => 'Куда']],
                ['name' => 'when', 'type' => 'text', 'label' => ['en' => 'When', 'de' => 'Wann', 'ru' => 'Когда']],
                ['name' => 'travellers', 'type' => 'select', 'label' => ['en' => 'How many of you', 'de' => 'Wie viele Personen', 'ru' => 'Сколько вас'], 'options' => ['en' => "1\n2\n3\n4\n5 or more", 'de' => "1\n2\n3\n4\n5 oder mehr", 'ru' => "1\n2\n3\n4\n5 и больше"]],
                ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => ['en' => 'Phone', 'de' => 'Telefon', 'ru' => 'Телефон']],
            ],
        ],
        'callback' => [
            'name' => ['en' => 'Call back', 'de' => 'Rückruf', 'ru' => 'Обратный звонок'],
            'button' => ['en' => 'Call me back', 'de' => 'Rufen Sie mich an', 'ru' => 'Перезвоните мне'],
            'success' => ['en' => 'Thank you! We will call you within an hour during office hours.', 'de' => 'Danke! Wir rufen zu Geschäftszeiten innerhalb einer Stunde an.', 'ru' => 'Спасибо! Перезвоним в течение часа в рабочее время.'],
            'fields' => [
                ['name' => 'name', 'type' => 'text', 'label' => ['en' => 'Name', 'de' => 'Name', 'ru' => 'Имя']],
                ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => ['en' => 'Phone', 'de' => 'Telefon', 'ru' => 'Телефон']],
            ],
        ],
    ],

    'pages' => [
        'home' => [
            'name' => ['en' => 'Trips put together around you', 'de' => 'Reisen, die wir um Sie herum planen', 'ru' => 'Путешествия, которые собирают под вас'],
            'content' => ['en' => '<p>Tell us where you feel drawn to and we will find the route, the stay and the guides. Ready-made tours are here too.</p>', 'de' => '<p>Erzählen Sie uns, wohin es Sie zieht – wir finden Route, Unterkunft und Guides. Fertige Reisen gibt es auch.</p>', 'ru' => '<p>Расскажите, куда тянет, — подберём маршрут, жильё и гидов. Готовые туры тоже есть.</p>'],
        ],
        'why-us' => [
            'name' => ['en' => 'Why travel with us', 'de' => 'Warum mit uns reisen', 'ru' => 'Почему с нами'],
            'content' => ['en' => '<p>Three things we promise every traveller.</p>', 'de' => '<p>Drei Dinge, die wir allen Reisenden versprechen.</p>', 'ru' => '<p>Три вещи, которые мы обещаем каждому путешественнику.</p>'],
            'children' => [
                'your-route' => [
                    'name' => ['en' => 'A route made for you', 'de' => 'Eine Route für Sie', 'ru' => 'Маршрут под вас'],
                    'content' => ['en' => '<p>We ask how you like to travel and put the trip together: flights, stays, guides.</p>', 'de' => '<p>Wir fragen, wie Sie gern reisen, und stellen die Reise zusammen: Flüge, Unterkünfte, Guides.</p>', 'ru' => '<p>Спросим, как вы любите отдыхать, и соберём поездку: перелёты, жильё, гиды.</p>'],
                ],
                'in-touch' => [
                    'name' => ['en' => 'In touch on the road', 'de' => 'Unterwegs erreichbar', 'ru' => 'На связи в дороге'],
                    'content' => ['en' => '<p>One manager from the request to your return, answering in a messenger.</p>', 'de' => '<p>Eine Ansprechperson von der Anfrage bis zur Rückkehr, erreichbar per Messenger.</p>', 'ru' => '<p>Один менеджер от заявки до возвращения, отвечает в мессенджере.</p>'],
                ],
                'fair-price' => [
                    'name' => ['en' => 'A fair price', 'de' => 'Ein ehrlicher Preis', 'ru' => 'Честная цена'],
                    'content' => ['en' => '<p>The price covers everything in the programme. Extras are named before you pay.</p>', 'de' => '<p>Der Preis umfasst alles im Programm. Zusatzkosten nennen wir vor der Zahlung.</p>', 'ru' => '<p>В стоимости всё, что в программе. Доплаты называем до оплаты.</p>'],
                ],
            ],
        ],
    ],
];
