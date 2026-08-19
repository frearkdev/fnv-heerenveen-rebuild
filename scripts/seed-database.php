<?php
/**
 * Database Seeding Script
 * Populates database with test data for development/testing
 * Usage: php scripts/seed-database.php
 */

require_once __DIR__ . '/../includes/config.php';

echo "🌱 FNV Heerenveen – Database Seeding Script\n";
echo str_repeat("=", 50) . "\n\n";

if (APP_ENV === 'production') {
    echo "❌ Cannot seed database in production environment!\n";
    exit(1);
}

try {
    $pdo = db();

    // Clear existing data (optional - uncomment if needed)
    // echo "Clearing existing data...\n";
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    // $pdo->exec("TRUNCATE TABLE news");
    // $pdo->exec("TRUNCATE TABLE agenda");
    // $pdo->exec("TRUNCATE TABLE contact_messages");
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    echo "📰 Seeding news articles...\n";
    $newsData = [
        [
            'title' => 'FNV betaalde verlof voor alle werknemers',
            'excerpt' => 'FNV strijdt voor betaald verlof voor alle werknemers, niet alleen voor mensen met vaste contracten.',
            'content' => '<p>FNV Heerenveen zet zich in voor betaald verlof voor iedereen. Dit is belangrijk voor werknemers met flexibele contracten.</p><p>Wij vinden dat verlof recht is en niet privilige. Meer informatie volgt binnenkort.</p>',
            'category' => 'Cao'
        ],
        [
            'title' => 'Nieuwe trainingen en cursussen beschikbaar',
            'excerpt' => 'FNV biedt gratis trainingen aan voor vaardigheidsontwikkeling van leden.',
            'content' => '<p>Dit najaar organiseren we gratis trainingen voor:</p><ul><li>Onderhandelaarsvaardigheden</li><li>Arbeidsrecht basics</li><li>Digitale veiligheid</li></ul><p>Meld je aan via het contactformulier!</p>',
            'category' => 'Dienstverlening'
        ],
        [
            'title' => 'Minimumloon stijgt met ingang 2025',
            'excerpt' => 'Goed nieuws: het wettelijk minimumloon gaat omhoog. FNV zet door voor nog betere arbeidsvoorwaarden.',
            'content' => '<p>Het minimumloon stijgt per 1 januari 2025. Dit is een kleine stap, maar wel in de goede richting.</p><p>FNV blijft ijveren voor eerlijk loon voor iedereen die werkt.</p>',
            'category' => 'Arbeidsmarkt'
        ],
        [
            'title' => 'Wat te doen bij problemen op het werk?',
            'excerpt' => 'Praktische gids voor werknemers die moeilijkheden ervaren op het werk.',
            'content' => '<p>Werkproblemen? Volg deze stappen:</p><ol><li>Documenteer alles (mails, getuigen, data)</li><li>Neem contact op met een vertrouwenspersoon</li><li>Bel het spreekuur van FNV Heerenveen</li><li>We helpen je met je rechten</li></ol>',
            'category' => 'Nieuws'
        ],
        [
            'title' => 'Pensioenrecht voor deeltijders',
            'excerpt' => 'Deeltijdwerkers hebben ook recht op pensioen. Dit is wat je moet weten.',
            'content' => '<p>Ook deeltijdwerkers bouwen pensioen op. Als werkgever verplicht is een pensioenbijdrage betalen.</p><p>Heb je vragen over je pensioenrechten? Neem contact op met ons spreekuur!</p>',
            'category' => 'Pensioen'
        ]
    ];

    $newsStmt = $pdo->prepare("INSERT INTO news (title, slug, excerpt, content, category, published, published_at)
                               VALUES (?, ?, ?, ?, ?, 1, NOW())");

    foreach ($newsData as $item) {
        $slug = slug($item['title']);
        $newsStmt->execute([$item['title'], $slug, $item['excerpt'], $item['content'], $item['category']]);
    }
    echo "✓ Added " . count($newsData) . " news articles\n\n";

    echo "📅 Seeding agenda items...\n";
    $agendaData = [
        [
            'titel' => 'Spreekuur Heerenveen',
            'beschrijving' => 'Gratis spreekuur voor alle FNV-leden. Kom langs met je vragen over werk en inkomen.',
            'datum' => date('Y-m-d', strtotime('+7 days')),
            'tijdstip' => '10:00 - 12:00',
            'locatie' => 'Bibliotheek Heerenveen, Zeppelinstraat 70',
            'type' => 'Spreekuur'
        ],
        [
            'titel' => 'Spreekuur Joure',
            'beschrijving' => 'Gratis spreekuur in Joure. Advies over werk, inkomsten en sociale zekerheid.',
            'datum' => date('Y-m-d', strtotime('+10 days')),
            'tijdstip' => '14:00 - 16:00',
            'locatie' => 'Gemeentehuis Joure',
            'type' => 'Spreekuur'
        ],
        [
            'titel' => 'Ledenvergadering FNV Heerenveen',
            'beschrijving' => 'Jaarlijkse ledenvergadering. Bespreking jaarverslag, beleidsplannen en verkiezing bestuur.',
            'datum' => date('Y-m-d', strtotime('+21 days')),
            'tijdstip' => '19:30',
            'locatie' => 'Zalencentrum De Greiden, Heerenveen',
            'type' => 'Vergadering'
        ],
        [
            'titel' => 'Informatie: Werkloosheid en WW',
            'beschrijving' => 'Online sessie over uw rechten bij werkloosheid, WW-aanvraag en overige uitkeringen.',
            'datum' => date('Y-m-d', strtotime('+28 days')),
            'tijdstip' => '14:00 - 16:00',
            'locatie' => 'Online via Teams',
            'type' => 'Informatie'
        ],
        [
            'titel' => 'Training: Onderhandelaarsvaardigheden',
            'beschrijving' => 'Leer onderhandelingstechnieken en zet je rechten als werknemer krachtig in.',
            'datum' => date('Y-m-d', strtotime('+35 days')),
            'tijdstip' => '19:00 - 21:30',
            'locatie' => 'FNV kantoor Heerenveen',
            'type' => 'Training'
        ],
        [
            'titel' => 'Spreekuur Drachten',
            'beschrijving' => 'Maandelijks spreekuur op kantoor Drachten. Persoonlijk advies voor leden.',
            'datum' => date('Y-m-d', strtotime('+42 days')),
            'tijdstip' => '10:00 - 12:00',
            'locatie' => 'FNV kantoor Drachten',
            'type' => 'Spreekuur'
        ]
    ];

    $agendaStmt = $pdo->prepare("INSERT INTO agenda (titel, beschrijving, datum, tijdstip, locatie, type)
                                 VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($agendaData as $item) {
        $agendaStmt->execute([$item['titel'], $item['beschrijving'], $item['datum'],
                              $item['tijdstip'], $item['locatie'], $item['type']]);
    }
    echo "✓ Added " . count($agendaData) . " agenda items\n\n";

    echo "💬 Seeding contact messages...\n";
    $contactData = [
        [
            'naam' => 'Jan de Boer',
            'email' => 'jan@example.nl',
            'telefoon' => '06 12345678',
            'onderwerp' => 'Arbeidsrechtelijk advies',
            'bericht' => 'Goedemorgen, ik heb een vraag over mijn arbeidsovereenkomst. Kan ik binnenkort langs komen voor advies?',
        ],
        [
            'naam' => 'Maria van Dijk',
            'email' => 'maria@example.nl',
            'telefoon' => '06 87654321',
            'onderwerp' => 'Belastingaangifte hulp',
            'bericht' => 'Ik ben FNV-lid en wil graag hulp bij mijn belastingaangifte. Wanneer kan dit?',
        ]
    ];

    $contactStmt = $pdo->prepare("INSERT INTO contact_messages (naam, email, telefoon, onderwerp, bericht)
                                  VALUES (?, ?, ?, ?, ?)");

    foreach ($contactData as $msg) {
        $contactStmt->execute([$msg['naam'], $msg['email'], $msg['telefoon'], $msg['onderwerp'], $msg['bericht']]);
    }
    echo "✓ Added " . count($contactData) . " contact messages\n\n";

    echo "\n✨ Database seeding completed successfully!\n";
    echo "Login with: admin@fnvheerenveen.nl / Admin@FNV2024! (change after first login)\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

function slug(string $str): string {
    $str = strtolower($str);
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}
