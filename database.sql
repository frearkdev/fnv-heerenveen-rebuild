-- ============================================
-- FNV Heerenveen Website – Database Setup
-- Importeer dit bestand in phpMyAdmin
-- ============================================

CREATE DATABASE IF NOT EXISTS fnv_heerenveen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fnv_heerenveen;

-- Gebruikers (admin panel)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','editor') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Nieuwsberichten
CREATE TABLE IF NOT EXISTS news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT NOT NULL,
    content LONGTEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    published TINYINT(1) DEFAULT 0,
    published_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Agenda
CREATE TABLE IF NOT EXISTS agenda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titel VARCHAR(255) NOT NULL,
    beschrijving TEXT DEFAULT NULL,
    datum DATE NOT NULL,
    tijdstip VARCHAR(30) DEFAULT NULL,
    locatie VARCHAR(255) DEFAULT NULL,
    type VARCHAR(100) DEFAULT NULL,
    aanmelding_url VARCHAR(500) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pagina's (CMS)
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    meta_description VARCHAR(160) DEFAULT NULL,
    active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contactberichten
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telefoon VARCHAR(30) DEFAULT NULL,
    onderwerp VARCHAR(200) NOT NULL,
    bericht TEXT NOT NULL,
    gelezen TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Voorbeelddata
-- ============================================

-- Admin gebruiker (wachtwoord: Admin@FNV2024!)
INSERT INTO users (name, email, password, role) VALUES
('FNV Heerenveen Admin', 'admin@fnvheerenveen.nl', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Nieuws
INSERT INTO news (title, slug, excerpt, content, category, published, published_at) VALUES
('FNV Heerenveen houdt spreekuur op 3 locaties', 'fnv-heerenveen-spreekuur-3-locaties',
 'Onze vrijwilligers zijn nu op drie locaties in de regio bereikbaar voor gratis advies over werk en inkomen.',
 '<p>FNV Heerenveen is blij te kunnen melden dat we ons spreekuur hebben uitgebreid naar drie locaties in de regio Heerenveen-Opsterland.</p><p>Elke maand kunt u terecht bij onze vrijwilligers voor persoonlijk advies over arbeidsrechtelijke kwesties, uitkeringen en belastingaangifte. Het spreekuur is kosteloos voor leden.</p><p><strong>Locaties:</strong></p><ul><li>Heerenveen (bibliotheek)</li><li>Joure (gemeentehuis)</li><li>Drachten (FNV-kantoor)</li></ul><p>Maak een afspraak via het contactformulier of bel ons tijdens kantooruren.</p>',
 'Dienstverlening', 1, NOW()),
('Nieuwe cao Metaal & Techniek: 5% loonsverhoging', 'nieuwe-cao-metaal-techniek',
 'Na lange onderhandelingen is er een nieuw cao-akkoord gesloten met een loonsverhoging van 5%.',
 '<p>Na maanden van intensieve onderhandelingen heeft FNV een nieuw cao-akkoord gesloten voor werknemers in de Metaal & Techniek sector.</p><p>De belangrijkste punten:</p><ul><li>Loonsverhoging van 5% per 1 januari 2025</li><li>Extra vakantiedag voor medewerkers met meer dan 10 jaar dienst</li><li>Verbeterde regeling voor thuiswerken</li><li>Hogere onkostenvergoeding</li></ul>',
 'Cao', 1, NOW()),
('Belastingaangifte hulp: meld u aan voor 2025', 'belastingaangifte-hulp-2025',
 'FNV-vrijwilligers helpen leden gratis met hun belastingaangifte. Aanmelden is nu mogelijk.',
 '<p>Elk jaar helpen de vrijwilligers van FNV Heerenveen leden met hun belastingaangifte. Dit jaar starten we in februari.</p><p>Het belastingserviceprogramma is voor FNV-leden met een relatief eenvoudige belastingsituatie. Aanmelden kan via het contactformulier. Vermeld "Belastingaangifte 2025" in het onderwerp.</p>',
 'Belasting', 1, NOW());

-- Agenda
INSERT INTO agenda (titel, beschrijving, datum, tijdstip, locatie, type) VALUES
('FNV Spreekuur Heerenveen', 'Maandelijks gratis spreekuur voor leden. Kom langs voor advies over werk, inkomen of uitkering.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), '10:00 - 12:00', 'Bibliotheek Heerenveen, Zeppelinstraat 70', 'Spreekuur'),
('Ledenvergadering FNV Heerenveen', 'Jaarlijkse ledenvergadering. Agenda: jaarverslag, beleidsplannen 2025, verkiezing bestuur.', DATE_ADD(CURDATE(), INTERVAL 21 DAY), '19:30', 'Zalencentrum De Greiden, Heerenveen', 'Vergadering'),
('Informatiebijeenkomst Werkloosheid & WW', 'Informatiesessie over uw rechten bij werkloosheid en WW-aanvraag.', DATE_ADD(CURDATE(), INTERVAL 35 DAY), '14:00 - 16:00', 'Online via Teams', 'Informatie');

-- Pagina's
INSERT INTO pages (title, slug, content, meta_description, active, sort_order) VALUES
('Over Ons', 'over-ons', '<h2>Over FNV Heerenveen</h2><p>FNV Heerenveen is de lokale afdeling van de grootste vakbond van Nederland. We ondersteunen werkenden, uitkeringsgerechtigden en gepensioneerden in de regio Heerenveen-Opsterland.</p><p>Onze vrijwilligers staan voor u klaar met advies over werk, inkomen en sociale zekerheid. Wij zijn er voor iedereen die werkt of heeft gewerkt in onze regio.</p><h3>Wat doen wij?</h3><ul><li>Advies bij problemen op het werk</li><li>Hulp bij ontslagprocedures</li><li>Begeleiding bij WW- en bijstandsaanvragen</li><li>Belastingaangifte (voor leden)</li><li>CAO-onderhandelingen</li></ul>', 'Meer over FNV Heerenveen – uw lokale vakbond voor werkenden in de regio Heerenveen.', 1, 1),
('Diensten', 'diensten', '<h2>Onze Diensten</h2><p>Als lid van FNV profiteert u van een breed pakket aan diensten en ondersteuning.</p><h3>Juridische hulp</h3><p>Onze juristen helpen u bij arbeidsrechtelijke kwesties, ontslag, loongeschillen en meer.</p><h3>Belastingaangifte</h3><p>Elk jaar helpen onze vrijwilligers leden met hun belastingaangifte.</p><h3>Cao-informatie</h3><p>We houden u op de hoogte van de laatste cao-ontwikkelingen in uw sector.</p><h3>Werkloosheid en uitkering</h3><p>Begeleiding bij WW-aanvragen, bezwaar en herbeoordeling van uitkeringen.</p>', 'Bekijk alle diensten van FNV Heerenveen – juridische hulp, belastingaangifte en meer.', 1, 2),
('Lid Worden', 'lid-worden', '<h2>Word lid van FNV</h2><p>Als lid van FNV sta je sterker op het werk. Samen maken we het verschil voor betere arbeidsomstandigheden, eerlijker loon en meer zekerheid.</p><h3>Waarom lid worden?</h3><ul><li>Persoonlijk advies bij arbeidsconflicten</li><li>Juridische bijstand</li><li>Korting op verzekeringen</li><li>Invloed op cao-onderhandelingen</li><li>Solidariteit met collega\'s</li></ul><p>Lid worden kan via <a href="https://www.fnv.nl/lid-worden" target="_blank">fnv.nl/lid-worden</a> of neem contact met ons op.</p>', 'Word lid van FNV Heerenveen en profiteer van alle voordelen van de grootste vakbond.', 1, 3);
