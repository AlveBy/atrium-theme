<?php

/*
 * This file is part of Atrium (https://github.com/AlveBy/atrium).
 * Copyright (c) 2026 AlveBy. Licensed under the MIT License, see LICENSE.
 */

declare(strict_types=1);

/*
 * The medical centre demo (ClinicDemoSeeder). Names, prices, addresses and
 * phone numbers are made up. Modules that translate get every language;
 * the others take the default content language's text.
 */
$doctor = static fn (array $name, array $role, array $about): array => [
    'name' => $name,
    // The first paragraph is the doctor's speciality, shown on the home page.
    'content' => array_combine(array_keys($role), array_map(static fn (string $role, string $about): string => "<p>{$role}</p><p>{$about}</p>", $role, $about)),
];

return [
    // The site's name, while it still has the application's.
    'site' => ['en' => 'Pulse', 'de' => 'Puls', 'ru' => 'Пульс'],

    'brands' => [
        ['code' => 'point-lab', 'name' => ['en' => 'Point Lab', 'de' => 'Labor Punkt', 'ru' => 'Лаборатория «Точка»']],
        ['code' => 'medtech-supply', 'name' => ['en' => 'MedTech Supply', 'de' => 'MedTech Bedarf', 'ru' => 'МедТехника']],
    ],

    'sections' => [
        'adults' => ['name' => ['en' => 'For adults', 'de' => 'Für Erwachsene', 'ru' => 'Взрослым']],
        'children' => ['name' => ['en' => 'For children', 'de' => 'Für Kinder', 'ru' => 'Детям']],
        'diagnostics' => ['name' => ['en' => 'Diagnostics', 'de' => 'Diagnostik', 'ru' => 'Диагностика']],
    ],

    'products' => [
        'therapy' => [
            'section' => 'adults', 'picture' => 'clinic-therapy.svg', 'price' => ['en' => 45, 'ru' => 1800],
            'name' => ['en' => 'General practice', 'de' => 'Allgemeinmedizin', 'ru' => 'Терапия'],
            'text' => ['en' => 'A first visit and a referral to the right doctor', 'de' => 'Erstuntersuchung und Überweisung zur richtigen Fachärztin', 'ru' => 'Первичный приём и направление к врачу'],
        ],
        'paediatrics' => [
            'section' => 'children', 'picture' => 'clinic-paediatrics.svg', 'price' => ['en' => 48, 'ru' => 1900],
            'name' => ['en' => 'Paediatrics', 'de' => 'Kinderheilkunde', 'ru' => 'Педиатрия'],
            'text' => ['en' => 'Children from birth to 18', 'de' => 'Kinder von der Geburt bis 18', 'ru' => 'Дети от рождения до 18 лет'],
        ],
        'ultrasound' => [
            'section' => 'diagnostics', 'brand' => 'medtech-supply', 'picture' => 'clinic-ultrasound.svg', 'price' => ['en' => 60, 'ru' => 2200],
            'name' => ['en' => 'Ultrasound', 'de' => 'Ultraschall', 'ru' => 'УЗИ'],
            'text' => ['en' => 'Every kind of scan, results right away', 'de' => 'Alle Untersuchungen, Ergebnis sofort', 'ru' => 'Все виды исследований, результат сразу'],
        ],
        'tests' => [
            'section' => 'diagnostics', 'brand' => 'point-lab', 'picture' => 'clinic-tests.svg', 'price' => ['en' => 9, 'ru' => 350],
            'name' => ['en' => 'Lab tests', 'de' => 'Laboruntersuchungen', 'ru' => 'Анализы'],
            'text' => ['en' => 'Samples from 7:30, results by e-mail', 'de' => 'Blutabnahme ab 7:30, Ergebnisse per E-Mail', 'ru' => 'Забор с 7:30, результаты на почту'],
        ],
        'dentistry' => [
            'section' => 'adults', 'picture' => 'clinic-dentistry.svg', 'price' => ['en' => 70, 'ru' => 2500],
            'name' => ['en' => 'Dentistry', 'de' => 'Zahnmedizin', 'ru' => 'Стоматология'],
            'text' => ['en' => 'Treatment, hygiene, implants', 'de' => 'Behandlung, Prophylaxe, Implantate', 'ru' => 'Лечение, гигиена, имплантация'],
        ],
        'cardiology' => [
            'section' => 'adults', 'picture' => 'clinic-cardiology.svg', 'price' => ['en' => 65, 'ru' => 2400],
            'name' => ['en' => 'Cardiology', 'de' => 'Kardiologie', 'ru' => 'Кардиология'],
            'text' => ['en' => 'ECG, 24-hour monitoring, consultation', 'de' => 'EKG, Langzeitmessung, Beratung', 'ru' => 'ЭКГ, суточный мониторинг, консультация'],
        ],
    ],

    'stores' => [
        'school-street' => [
            'name' => ['en' => 'On School Street', 'de' => 'In der Schulstraße', 'ru' => 'На Школьной'],
            'address' => ['en' => '3 School Street', 'de' => 'Schulstraße 3', 'ru' => 'ул. Школьная, 3'],
            'phone' => ['en' => '+44 20 0000 0000', 'de' => '+49 30 0000 0000', 'ru' => '8 800 000-00-00'],
            'hours' => ['en' => 'Daily 8:00–21:00', 'de' => 'Täglich 8–21 Uhr', 'ru' => 'Ежедневно 8:00–21:00'],
            'text' => ['en' => 'Parking in the yard, a lift to every floor.', 'de' => 'Parkplätze im Hof, Aufzug zu allen Etagen.', 'ru' => 'Парковка во дворе, лифт на все этажи.'],
        ],
    ],

    'news' => [
        [5, 'clinic-news.svg', [
            'en' => ['A room for 24-hour blood pressure monitoring has opened', 'blood-pressure-monitoring', 'Wear the monitor for a day and see the cardiologist the day after.', '<p>The monitor is fitted in ten minutes. Book with the form on the home page or by phone.</p>'],
            'de' => ['Neuer Raum für die 24-Stunden-Blutdruckmessung', 'blutdruck-langzeitmessung', 'Einen Tag das Gerät tragen, am nächsten Tag zur Kardiologin.', '<p>Das Gerät ist in zehn Minuten angelegt. Termine über das Formular auf der Startseite oder telefonisch.</p>'],
            'ru' => ['Открыли кабинет суточного мониторинга давления', 'sutochnyj-monitoring-davleniya', 'Сутки носите прибор, на следующий день — к кардиологу.', '<p>Прибор устанавливают за десять минут. Записаться можно через форму на главной или по телефону.</p>'],
        ]],
        [14, 'clinic-news.svg', [
            'en' => ['Flu vaccine has arrived', 'flu-vaccine', 'Vaccinations for adults and children without an appointment, 9:00–19:00.', '<p>Bring your vaccination record if you have one. A doctor sees you before the shot.</p>'],
            'de' => ['Grippeimpfstoff ist da', 'grippeimpfstoff', 'Impfungen für Erwachsene und Kinder ohne Termin, 9–19 Uhr.', '<p>Bringen Sie, falls vorhanden, Ihren Impfpass mit. Vor der Impfung spricht eine Ärztin mit Ihnen.</p>'],
            'ru' => ['Прививки от гриппа: вакцина поступила', 'privivki-ot-grippa', 'Прививаем взрослых и детей без записи, 9:00–19:00.', '<p>Возьмите прививочный сертификат, если он есть. Перед прививкой вас осмотрит врач.</p>'],
        ]],
        [20, null, [
            'en' => ['Opening hours over the holidays', 'holiday-hours', 'We are open every day; the lab closes at 14:00.', '<p>Doctors see patients as usual. Lab samples are taken until 12:00.</p>'],
            'de' => ['Öffnungszeiten an den Feiertagen', 'feiertage', 'Wir haben täglich geöffnet, das Labor schließt um 14 Uhr.', '<p>Die Sprechstunden laufen wie gewohnt. Blutabnahme bis 12 Uhr.</p>'],
            'ru' => ['Новый график работы в праздничные дни', 'grafik-v-prazdniki', 'Работаем каждый день, лаборатория — до 14:00.', '<p>Врачи принимают как обычно. Забор анализов — до 12:00.</p>'],
        ]],
    ],

    'reviews' => [
        ['product' => 'paediatrics', 'rating' => 5, 'author' => ['en' => 'Irina', 'de' => 'Irina', 'ru' => 'Ирина'], 'text' => ['en' => 'The paediatrician found an approach to our shy son in five minutes.', 'de' => 'Die Kinderärztin hat in fünf Minuten einen Draht zu unserem schüchternen Sohn gefunden.', 'ru' => 'Педиатр за пять минут нашла подход к нашему стеснительному сыну.']],
        ['product' => 'ultrasound', 'rating' => 5, 'author' => ['en' => 'Sergey', 'de' => 'Sergej', 'ru' => 'Сергей'], 'text' => ['en' => 'No queue, the scan took fifteen minutes and the doctor explained every line.', 'de' => 'Keine Wartezeit, der Ultraschall dauerte eine Viertelstunde, und der Arzt erklärte jede Zeile.', 'ru' => 'Без очереди, УЗИ заняло пятнадцать минут, врач объяснил каждую строчку.']],
    ],

    'faq' => [
        [[
            'en' => ['Appointments', 'appointments'],
            'de' => ['Termine', 'termine'],
            'ru' => ['Запись на приём', 'zapis'],
        ], [
            ['en' => ['Do I need a referral?', 'No, you can book a paid appointment yourself.'], 'de' => ['Brauche ich eine Überweisung?', 'Nein, einen Termin als Selbstzahler buchen Sie direkt.'], 'ru' => ['Нужно ли направление?', 'Нет, на платный приём можно записаться самому.']],
            ['en' => ['Do you accept health insurance?', 'Yes, we work with most insurers; check yours with reception.'], 'de' => ['Rechnen Sie mit Versicherungen ab?', 'Ja, mit den meisten; fragen Sie am Empfang nach Ihrer.'], 'ru' => ['Принимаете по ДМС?', 'Да, работаем с большинством страховых компаний — уточните свою у администратора.']],
            ['en' => ['How do I get my test results?', 'By e-mail and in your account, usually the next day.'], 'de' => ['Wie bekomme ich meine Laborergebnisse?', 'Per E-Mail und in Ihrem Konto, meist am nächsten Tag.'], 'ru' => ['Как получить результаты анализов?', 'Пришлём на почту и в личный кабинет, обычно на следующий день.']],
        ]],
    ],

    'forms' => [
        'appointment' => [
            'name' => ['en' => 'Appointment', 'de' => 'Terminbuchung', 'ru' => 'Запись на приём'],
            'button' => ['en' => 'Book', 'de' => 'Termin buchen', 'ru' => 'Записаться'],
            'success' => ['en' => 'Thank you! Reception will call you to confirm the time.', 'de' => 'Danke! Der Empfang ruft Sie an, um die Uhrzeit zu bestätigen.', 'ru' => 'Спасибо! Администратор перезвонит, чтобы подтвердить время.'],
            'fields' => [
                ['name' => 'service', 'type' => 'select', 'required' => true, 'label' => ['en' => 'Doctor or service', 'de' => 'Ärztin, Arzt oder Leistung', 'ru' => 'Врач или услуга'], 'options' => ['en' => "General practitioner\nPaediatrician\nCardiologist\nUltrasound\nLab tests", 'de' => "Allgemeinmedizin\nKinderheilkunde\nKardiologie\nUltraschall\nLabor", 'ru' => "Терапевт\nПедиатр\nКардиолог\nУЗИ\nАнализы"]],
                ['name' => 'phone', 'type' => 'tel', 'required' => true, 'label' => ['en' => 'Phone', 'de' => 'Telefon', 'ru' => 'Телефон']],
            ],
        ],
    ],

    'pages' => [
        'home' => [
            'name' => ['en' => 'A medical centre close to home', 'de' => 'Ein medizinisches Zentrum in Ihrer Nähe', 'ru' => 'Медицинский центр рядом с домом'],
            'content' => ['en' => '<p>Adults and children, tests and ultrasound in one place. No queues, by appointment.</p>', 'de' => '<p>Erwachsene und Kinder, Labor und Ultraschall an einem Ort. Ohne Wartezeit, mit Termin.</p>', 'ru' => '<p>Взрослые и дети, анализы и УЗИ в одном месте. Приём без очередей, по записи.</p>'],
        ],
        'doctors' => [
            'name' => ['en' => 'Our doctors', 'de' => 'Unser Team', 'ru' => 'Наши врачи'],
            'content' => ['en' => '<p>Doctors who see adults and children every day of the week.</p>', 'de' => '<p>Ärztinnen und Ärzte für Erwachsene und Kinder, an jedem Tag der Woche.</p>', 'ru' => '<p>Врачи, которые принимают взрослых и детей каждый день недели.</p>'],
            'children' => [
                'elena-sokolova' => $doctor(
                    ['en' => 'Elena Sokolova', 'de' => 'Elena Sokolova', 'ru' => 'Елена Соколова'],
                    ['en' => 'General practitioner · 18 years of practice', 'de' => 'Allgemeinmedizinerin · 18 Jahre Erfahrung', 'ru' => 'Терапевт · Стаж 18 лет'],
                    ['en' => 'Sees adults with colds, chronic conditions and everything in between.', 'de' => 'Behandelt Erwachsene bei Erkältungen, chronischen Erkrankungen und allem dazwischen.', 'ru' => 'Принимает взрослых с простудой, хроническими болезнями и всем, что между.'],
                ),
                'artyom-kim' => $doctor(
                    ['en' => 'Artyom Kim', 'de' => 'Artjom Kim', 'ru' => 'Артём Ким'],
                    ['en' => 'Cardiologist · 12 years of practice', 'de' => 'Kardiologe · 12 Jahre Erfahrung', 'ru' => 'Кардиолог · Стаж 12 лет'],
                    ['en' => 'ECG, 24-hour monitoring and advice after a heart operation.', 'de' => 'EKG, Langzeitmessung und Beratung nach Herzoperationen.', 'ru' => 'ЭКГ, суточный мониторинг и наблюдение после операций на сердце.'],
                ),
                'maria-volkova' => $doctor(
                    ['en' => 'Maria Volkova', 'de' => 'Maria Volkova', 'ru' => 'Мария Волкова'],
                    ['en' => 'Paediatrician · 15 years of practice', 'de' => 'Kinderärztin · 15 Jahre Erfahrung', 'ru' => 'Педиатр · Стаж 15 лет'],
                    ['en' => 'Check-ups, vaccinations and sick visits from birth to 18.', 'de' => 'Vorsorge, Impfungen und Krankheitsbesuche von der Geburt bis 18.', 'ru' => 'Осмотры, прививки и приёмы при болезни — от рождения до 18 лет.'],
                ),
            ],
        ],
    ],
];
