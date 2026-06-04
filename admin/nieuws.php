<?php
$adminTitle = 'Nieuws beheren';
require_once __DIR__ . '/includes/admin_header.php';

// Verwijder actie
if (isset($_GET['verwijder']) && is_numeric($_GET['verwijder'])) {
    $stmt = db()->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([(int)$_GET['verwijder']]);
    flash('succes', 'Nieuwsbericht verwijderd.');
    redirect(SITE_URL . '/admin/nieuws.php');
}

$succes = flash('succes');
$items = db()->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="admin-h1">Nieuws</h1>
        <p class="admin-sub">Beheer alle nieuwsberichten op de website.</p>
    </div>
    <a href="<?= SITE_URL ?>/admin/nieuws-form.php" class="btn btn-primary">+ Nieuw bericht</a>
</div>

<?php if ($succes): ?><div class="alert alert-succes"><?= h($succes) ?></div><?php endif; ?>

<div class="admin-tabel-wrap">
    <table class="admin-tabel">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Categorie</th>
                <th>Status</th>
                <th>Gepubliceerd</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($items): foreach ($items as $item): ?>
            <tr>
                <td style="font-weight:600;max-width:300px;"><?= h($item['title']) ?></td>
                <td><span class="badge badge-grijs"><?= h($item['category'] ?? '–') ?></span></td>
                <td>
                    <span class="badge <?= $item['published'] ? 'badge-groen' : 'badge-grijs' ?>">
                        <?= $item['published'] ? 'Gepubliceerd' : 'Concept' ?>
                    </span>
                </td>
                <td style="white-space:nowrap;font-size:.84rem;">
                    <?= $item['published_at'] ? formatDate($item['published_at']) : '–' ?>
                </td>
                <td>
                    <div style="display:flex;gap:.75rem;align-items:center;">
                        <a href="<?= SITE_URL ?>/artikel.php?slug=<?= h($item['slug']) ?>" target="_blank" class="tabel-link" style="color:var(--grijs-50);">Bekijk</a>
                        <a href="<?= SITE_URL ?>/admin/nieuws-form.php?id=<?= $item['id'] ?>" class="tabel-link">Bewerken</a>
                        <a href="<?= SITE_URL ?>/admin/nieuws.php?verwijder=<?= $item['id'] ?>"
                           class="tabel-verwijder js-verwijder"
                           data-bevestig="Bericht '<?= h(addslashes($item['title'])) ?>' definitief verwijderen?">
                           Verwijderen
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="5" class="leeg-cel">Nog geen nieuwsberichten. <a href="<?= SITE_URL ?>/admin/nieuws-form.php">Maak het eerste bericht aan →</a></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
