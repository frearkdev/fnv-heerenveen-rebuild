<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Agenda';

$stmt = db()->query("SELECT * FROM agenda WHERE datum >= CURDATE() ORDER BY datum ASC");
$items = $stmt->fetchAll();

$maanden = ['','januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december'];
$maandAfk = ['','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'];

require_once __DIR__ . '/includes/header.php';
?>

<div class="paginakop">
    <div class="container">
        <p class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> › Agenda</p>
        <h1>Agenda</h1>
        <p>Aankomende activiteiten en bijeenkomsten van FNV Heerenveen.</p>
    </div>
</div>

<section class="sectie">
    <div class="container">
        <?php if ($items): ?>
        <div class="agenda-lijst" style="max-width:800px;margin:0 auto;">
            <?php foreach ($items as $item):
                $ts    = strtotime($item['datum']);
                $dag   = date('j', $ts);
                $maand = strtoupper($maandAfk[(int)date('n', $ts)]);
            ?>
            <div class="agenda-item">
                <div class="agenda-item__datum">
                    <span class="agenda-item__dag"><?= $dag ?></span>
                    <span class="agenda-item__maand"><?= $maand ?></span>
                </div>
                <div class="agenda-item__body">
                    <div class="agenda-item__meta">
                        <?php if ($item['type']): ?><span class="badge badge-rood"><?= h($item['type']) ?></span><?php endif; ?>
                        <?php if ($item['tijdstip']): ?><span class="agenda-item__tijd"><?= h($item['tijdstip']) ?></span><?php endif; ?>
                    </div>
                    <h3><?= h($item['titel']) ?></h3>
                    <?php if ($item['locatie']): ?><p class="agenda-item__locatie"> <?= h($item['locatie']) ?></p><?php endif; ?>
                    <?php if ($item['beschrijving']): ?><p class="agenda-item__beschrijving"><?= h($item['beschrijving']) ?></p><?php endif; ?>
                    <?php if ($item['aanmelding_url']): ?>
                    <a href="<?= h($item['aanmelding_url']) ?>" target="_blank" rel="noopener" class="btn btn-primary" style="margin-top:.6rem;font-size:.82rem;padding:.4rem .9rem;">Aanmelden →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center" style="padding:4rem 0;">
            <p style="font-size:2.5rem;margin-bottom:1rem;"></p>
            <h3>Geen aankomende evenementen</h3>
            <p style="color:var(--grijs-50);margin-bottom:1.5rem;">Er zijn momenteel geen activiteiten gepland. Kom binnenkort terug!</p>
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary">Contact opnemen</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
