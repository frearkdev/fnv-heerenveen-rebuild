<?php
$adminTitle = 'Dashboard';
require_once __DIR__ . '/includes/admin_header.php';


$stats = [
    'nieuws'   => db()->query("SELECT COUNT(*) FROM news")->fetchColumn(),
    'agenda'   => db()->query("SELECT COUNT(*) FROM agenda WHERE datum >= CURDATE()")->fetchColumn(),
    'paginas'  => db()->query("SELECT COUNT(*) FROM pages")->fetchColumn(),
    'berichten' => db()->query("SELECT COUNT(*) FROM contact_messages WHERE gelezen = 0")->fetchColumn(),
];


$recentNieuws = db()->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5")->fetchAll();


$recentContact = db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<h1 class="admin-h1">Dashboard</h1>
<p class="admin-sub">Welkom terug, <?= h($_SESSION['admin_naam'] ?? 'beheerder') ?>. Hier een overzicht van de website.</p>


<div class="dash-stats">
    <div class="stat-kaart">
        <div class="stat-icoon stat-icoon--rood"></div>
        <div><strong><?= $stats['nieuws'] ?></strong><span>Nieuwsberichten</span></div>
    </div>
    <div class="stat-kaart">
        <div class="stat-icoon stat-icoon--blauw"></div>
        <div><strong><?= $stats['agenda'] ?></strong><span>Aankomende events</span></div>
    </div>
    <div class="stat-kaart">
        <div class="stat-icoon stat-icoon--groen"></div>
        <div><strong><?= $stats['paginas'] ?></strong><span>Pagina's</span></div>
    </div>
    <div class="stat-kaart">
        <div class="stat-icoon stat-icoon--geel"></div>
        <div><strong><?= $stats['berichten'] ?></strong><span>Ongelezen berichten</span></div>
    </div>
</div>


<h2 class="admin-h1" style="font-size:1.15rem;margin-bottom:1rem;">Snelle acties</h2>
<div class="dash-acties">
    <a href="<?= SITE_URL ?>/admin/nieuws-form.php" class="actie-kaart">
        <span>✏️</span>
        <strong>Nieuw artikel</strong>
        <span>Schrijf en publiceer nieuws</span>
    </a>
    <a href="<?= SITE_URL ?>/admin/agenda.php" class="actie-kaart">
        <span>📅</span>
        <strong>Agenda toevoegen</strong>
        <span>Plan een evenement</span>
    </a>
    <a href="<?= SITE_URL ?>/admin/paginas.php" class="actie-kaart">
        <span>📝</span>
        <strong>Pagina bewerken</strong>
        <span>Wijzig website-inhoud</span>
    </a>
    <a href="<?= SITE_URL ?>/admin/berichten.php" class="actie-kaart">
        <span>💬</span>
        <strong>Contactberichten</strong>
        <span>Bekijk inkomende vragen</span>
    </a>
</div>


<h2 class="admin-h1" style="font-size:1.15rem;margin-bottom:1rem;">Recente nieuwsberichten</h2>
<div class="admin-tabel-wrap">
    <table class="admin-tabel">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Categorie</th>
                <th>Status</th>
                <th>Datum</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recentNieuws): foreach ($recentNieuws as $item): ?>
                    <tr>
                        <td style="font-weight:600;max-width:280px;"><?= h($item['title']) ?></td>
                        <td><span class="badge badge-grijs"><?= h($item['category'] ?? '–') ?></span></td>
                        <td><span class="badge <?= $item['published'] ? 'badge-groen' : 'badge-grijs' ?>"><?= $item['published'] ? 'Gepubliceerd' : 'Concept' ?></span></td>
                        <td style="white-space:nowrap;"><?= $item['published_at'] ? formatDate($item['published_at']) : '–' ?></td>
                        <td style="display:flex;gap:.75rem;">
                            <a href="<?= SITE_URL ?>/admin/nieuws-form.php?id=<?= $item['id'] ?>" class="tabel-link">Bewerken</a>
                            <a href="<?= SITE_URL ?>/admin/nieuws-delete.php?id=<?= $item['id'] ?>" class="tabel-verwijder js-verwijder" data-bevestig="Bericht '<?= h(addslashes($item['title'])) ?>' verwijderen?">Verwijderen</a>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="5" class="leeg-cel">Nog geen nieuwsberichten.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<h2 class="admin-h1" style="font-size:1.15rem;margin:2rem 0 1rem;">Recente contactberichten</h2>
<div class="admin-tabel-wrap">
    <table class="admin-tabel">
        <thead>
            <tr>
                <th>Naam</th>
                <th>Onderwerp</th>
                <th>Datum</th>
                <th>Status</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($recentContact): foreach ($recentContact as $msg): ?>
                    <tr style="<?= !$msg['gelezen'] ? 'background:var(--color-warning-soft);' : '' ?>">
                        <td style="font-weight:600;"><?= h($msg['naam']) ?></td>
                        <td><?= h($msg['onderwerp']) ?></td>
                        <td style="white-space:nowrap;font-size:.82rem;"><?= formatDate($msg['created_at']) ?></td>
                        <td><span class="badge <?= $msg['gelezen'] ? 'badge-grijs' : 'badge-rood' ?>"><?= $msg['gelezen'] ? 'Gelezen' : 'Nieuw' ?></span></td>
                        <td><a href="<?= SITE_URL ?>/admin/berichten.php?id=<?= $msg['id'] ?>" class="tabel-link">Bekijken</a></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="5" class="leeg-cel">Geen berichten ontvangen.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>