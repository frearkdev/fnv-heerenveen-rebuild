<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Vakbond voor werkenden in Heerenveen';

// Haal recente nieuws op
$stmt = db()->query("SELECT * FROM news WHERE published = 1 ORDER BY published_at DESC LIMIT 3");
$nieuws = $stmt->fetchAll();

// Haal aankomende agenda op
$stmt = db()->query("SELECT * FROM agenda WHERE datum >= CURDATE() ORDER BY datum ASC LIMIT 3");
$agenda = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="container hero__inner">
        <div>
            <span class="hero__badge">Lokaal Netwerk Heerenveen-Opsterland</span>
            <h1>Samen staan we <span class="accent">sterk</span></h1>
            <p class="hero__lead">FNV Heerenveen ondersteunt werkenden, uitkeringsgerechtigden en gepensioneerden in onze regio met persoonlijk advies over werk, inkomen en sociale zekerheid.</p>
            <div class="hero__knoppen">
                <a href="<?= SITE_URL ?>/pagina.php?slug=lid-worden" class="btn btn-primary">Lid worden</a>
                <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary">Gratis spreekuur</a>
            </div>
            <div class="hero__stats">
                <div class="hero__stat">
                    <strong class="js-teller" data-doel="1200000">1.200.000</strong>
                    <span>FNV-leden</span>
                </div>
                <div class="hero__stat">
                    <strong>100+</strong>
                    <span>Jaar ervaring</span>
                </div>
                <div class="hero__stat">
                    <strong>Gratis</strong>
                    <span>Spreekuur</span>
                </div>
            </div>
        </div>
        <div class="hero__visual" aria-hidden="true">
            <div class="hero__blob"></div>
            <div class="hero__card">
                <strong>Maandelijks spreekuur</strong>
                <span>Gratis advies voor leden in de regio</span>
            </div>
        </div>
    </div>
</section>

<!-- DIENSTEN -->
<section class="sectie">
    <div class="container">
        <div class="sectie-titel">
            <h2>Hoe kunnen wij u helpen?</h2>
            <p>FNV Heerenveen biedt een breed scala aan gratis diensten voor leden.</p>
        </div>
        <div class="diensten-grid">
            <?php
            $diensten = [
                ['⚖️', 'Juridische hulp', 'Onze juristen helpen u bij arbeidsrechtelijke geschillen, ontslag en loonconflicten.'],
                ['🧾', 'Belastingaangifte', 'Elk jaar helpen onze vrijwilligers leden gratis met hun belastingaangifte.'],
                ['🤝', 'Spreekuur', 'Maandelijks gratis spreekuur op meerdere locaties in de regio Heerenveen.'],
                ['📋', 'Cao-informatie', 'Wij houden u op de hoogte van cao-onderhandelingen en uw rechten als werknemer.'],
                ['💶', 'WW & uitkering', 'Begeleiding bij WW-aanvraag, bezwaar en begeleiding naar werk.'],
                ['❤️', 'Pensioen', 'Advies over pensioenopbouw en uw rechten als gepensioneerde.'],
            ];
            foreach ($diensten as $d): ?>
                <div class="dienst">
                    <div class="dienst__icoon"><?= $d[0] ?></div>
                    <h3><?= h($d[1]) ?></h3>
                    <p><?= h($d[2]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- NIEUWS -->
<section class="sectie sectie-grijs">
    <div class="container">
        <div class="sectie-titel">
            <h2>Laatste nieuws</h2>
            <p>Blijf op de hoogte van het laatste vakbondsnieuws van FNV Heerenveen.</p>
        </div>
        <?php if ($nieuws): ?>
            <div class="nieuws-grid">
                <?php foreach ($nieuws as $item): ?>
                    <a href="<?= SITE_URL ?>/artikel.php?slug=<?= h($item['slug']) ?>" class="nieuws-kaart" data-cat="<?= h($item['category'] ?? '') ?>">
                        <div class="nieuws-kaart__img" <?= $item['image'] ? 'style="background-image:url(' . h($item['image']) . ')"' : '' ?>>
                            <?php if ($item['category']): ?>
                                <span class="badge badge-rood"><?= h($item['category']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="nieuws-kaart__body">
                            <p class="nieuws-kaart__datum"><?= $item['published_at'] ? formatDate($item['published_at']) : '' ?></p>
                            <h3 class="nieuws-kaart__titel"><?= h($item['title']) ?></h3>
                            <p class="nieuws-kaart__excerpt"><?= h($item['excerpt']) ?></p>
                            <span class="nieuws-kaart__lees">Lees meer →</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center" style="color:var(--grijs-50);">Nog geen nieuwsberichten.</p>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="<?= SITE_URL ?>/nieuws.php" class="btn btn-secondary">Alle nieuwsberichten</a>
        </div>
    </div>
</section>

<!-- AGENDA -->
<section class="sectie">
    <div class="container">
        <div class="sectie-titel">
            <h2>Agenda</h2>
            <p>Aankomende activiteiten en bijeenkomsten van FNV Heerenveen.</p>
        </div>
        <?php if ($agenda): ?>
            <div class="agenda-lijst" style="margin:0 auto;">
                <?php foreach ($agenda as $item):
                    $dag   = date('j', strtotime($item['datum']));
                    $maand = strtoupper(substr(['', 'jan', 'feb', 'mrt', 'apr', 'mei', 'jun', 'jul', 'aug', 'sep', 'okt', 'nov', 'dec'][(int)date('n', strtotime($item['datum']))], 0, 3));
                ?>
                    <div class="agenda-item">
                        <div class="agenda-item__datum">
                            <span class="agenda-item__dag"><?= $dag ?></span>
                            <span class="agenda-item__maand"><?= $maand ?></span>
                        </div>
                        <div class="agenda-item__body">
                            <div class="agenda-item__meta">
                                <?php if ($item['type']): ?><span class="badge badge-rood"><?= h($item['type']) ?></span><?php endif; ?>
                                <?php if ($item['tijdstip']): ?><span class="agenda-item__tijd">🕐 <?= h($item['tijdstip']) ?></span><?php endif; ?>
                            </div>
                            <h3><?= h($item['titel']) ?></h3>
                            <?php if ($item['locatie']): ?><p class="agenda-item__locatie">📍 <?= h($item['locatie']) ?></p><?php endif; ?>
                            <?php if ($item['beschrijving']): ?><p class="agenda-item__beschrijving"><?= h($item['beschrijving']) ?></p><?php endif; ?>
                            <?php if ($item['aanmelding_url']): ?>
                                <a href="<?= h($item['aanmelding_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:.5rem;font-size:.82rem;padding:.4rem .9rem;">Aanmelden</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center" style="color:var(--grijs-50);">Geen aankomende evenementen.</p>
        <?php endif; ?>
        <div class="text-center mt-3">
            <a href="<?= SITE_URL ?>/agenda.php" class="btn btn-secondary">Volledige agenda</a>
        </div>
    </div>
</section>

<!-- CTA LID WORDEN -->
<section class="cta">
    <div class="container cta__inner">
        <div>
            <h2>Word vandaag nog lid</h2>
            <p>Als FNV-lid sta je sterker op het werk. Persoonlijk advies, juridische hulp en solidariteit met collega's.</p>
        </div>
        <div class="cta__knoppen">
            <a href="https://www.fnv.nl/lid-worden" target="_blank" rel="noopener" class="btn btn-white">Lid worden bij FNV</a>
            <a href="<?= SITE_URL ?>/contact.php" class="btn" style="border:2px solid var(--color-surface);color:var(--color-surface);background:transparent;">Meer informatie</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>