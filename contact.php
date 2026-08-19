<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Contact';
$succes = $fout = '';
$form = ['naam' => '', 'email' => '', 'telefoon' => '', 'onderwerp' => '', 'bericht' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    foreach ($form as $key => $_) {
        $form[$key] = trim($_POST[$key] ?? '');
    }

    // Validatie
    $fouten = [];
    if (strlen($form['naam']) < 2)         $fouten[] = 'Vul uw naam in.';
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) $fouten[] = 'Vul een geldig e-mailadres in.';
    if (strlen($form['onderwerp']) < 3)    $fouten[] = 'Vul een onderwerp in.';
    if (strlen($form['bericht']) < 10)     $fouten[] = 'Vul een bericht in (minimaal 10 tekens).';

    if (!$fouten) {
        // Opslaan in database
        $stmt = db()->prepare("INSERT INTO contact_messages (naam, email, telefoon, onderwerp, bericht) VALUES (?,?,?,?,?)");
        $stmt->execute([$form['naam'], $form['email'], $form['telefoon'], $form['onderwerp'], $form['bericht']]);

        $aan      = ADMIN_EMAIL;
        $onderwerp = 'Contactformulier: ' . $form['onderwerp'];
        $tekst    = "Van: {$form['naam']} ({$form['email']})\nTelefoon: " . ($form['telefoon'] ?: 'niet opgegeven') . "\n\nBericht:\n{$form['bericht']}";
        $headers  = "From: noreply@fnvheerenveen.nl\r\nReply-To: {$form['email']}\r\nContent-Type: text/plain; charset=utf-8";
        mail($aan, $onderwerp, $tekst, $headers);

        $succes = 'Uw bericht is ontvangen! We nemen zo spoedig mogelijk contact met u op.';
        $form   = ['naam' => '', 'email' => '', 'telefoon' => '', 'onderwerp' => '', 'bericht' => ''];
    } else {
        $fout = implode(' ', $fouten);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="paginakop">
    <div class="container">
        <p class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> › Contact</p>
        <h1>Contact</h1>
        <p>Neem contact op met FNV Heerenveen. We helpen u graag verder.</p>
    </div>
</div>

<section class="sectie">
    <div class="container contact-layout">

        <!-- Formulier -->
        <div>
            <div class="contact-kaart">
                <h2>Stuur een bericht</h2>
                <p>Vul het formulier in en we nemen zo spoedig mogelijk contact met u op.</p>

                <?php if ($succes): ?><div class="alert alert-succes">✓ <?= h($succes) ?></div><?php endif; ?>
                <?php if ($fout):   ?><div class="alert alert-fout">✗ <?= h($fout) ?></div><?php endif; ?>

                <?php if (!$succes): ?>
                <form method="POST" id="contactForm">
                    <div class="form-group">
                        <label class="form-label" for="naam">Naam *</label>
                        <input type="text" id="naam" name="naam" class="form-input" value="<?= h($form['naam']) ?>" placeholder="Uw volledige naam" required>
                    </div>
                    <div class="grid-2col">
                        <div class="form-group">
                            <label class="form-label" for="email">E-mailadres *</label>
                            <input type="email" id="email" name="email" class="form-input" value="<?= h($form['email']) ?>" placeholder="uw@email.nl" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="telefoon">Telefoonnummer</label>
                            <input type="tel" id="telefoon" name="telefoon" class="form-input" value="<?= h($form['telefoon']) ?>" placeholder="06 – 00 000 000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="onderwerp">Onderwerp *</label>
                        <select id="onderwerp" name="onderwerp" class="form-select" required>
                            <option value="">– Kies een onderwerp –</option>
                            <?php
                            $opties = ['Arbeidsrechtelijk advies', 'Belastingaangifte hulp', 'Spreekuur aanvragen', 'Lidmaatschap informatie', 'WW / uitkering', 'Pensioen', 'Overig'];
                            foreach ($opties as $opt): ?>
                            <option value="<?= h($opt) ?>" <?= $form['onderwerp'] === $opt ? 'selected' : '' ?>><?= h($opt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="bericht">Bericht *</label>
                        <textarea id="bericht" name="bericht" class="form-textarea" rows="6" placeholder="Beschrijf uw vraag of situatie..." required data-max="2000"><?= h($form['bericht']) ?></textarea>
                        <p class="form-small"><span data-teller="bericht">0</span>/2000 tekens</p>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Bericht versturen</button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contactinfo -->
        <aside>
            <div class="info-blok">
                <h4>Contactgegevens</h4>
                <ul class="info-lijst">
                    <li>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <div><strong>E-mail</strong><a href="mailto:info@fnvheerenveen.nl">info@fnvheerenveen.nl</a></div>
                    </li>
                    <li>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <div><strong>Telefoon</strong><a href="tel:0513000000">0513 – 000 000</a></div>
                    </li>
                    <li>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div><strong>Kantooruren</strong><span>Ma–Vr 09:00 – 17:00</span></div>
                    </li>
                    <li>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <div><strong>Locatie</strong><span>Heerenveen, Friesland</span></div>
                    </li>
                </ul>
            </div>

            <div class="info-blok">
                <h4>Spreekuur</h4>
                <p style="font-size:.88rem;color:var(--grijs-50);margin-bottom:1rem;">Elke maand gratis spreekuur op meerdere locaties. Geen afspraak nodig!</p>
                <a href="<?= SITE_URL ?>/agenda.php" class="btn btn-secondary w-100">Bekijk agenda</a>
            </div>

            <div class="info-blok info-blok--rood">
                <h4>Spoed?</h4>
                <p style="font-size:.88rem;">Bij dringende arbeidsrechtelijke kwesties of aankomend ontslag kunt u direct bellen.</p>
                <a href="tel:0513000000" class="btn btn-white w-100" style="margin-top:.9rem;">Bel ons nu</a>
            </div>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
