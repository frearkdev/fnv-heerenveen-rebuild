<?php
$adminTitle = 'Contactberichten';
require_once __DIR__ . '/includes/admin_header.php';

// Verwijderen
if (isset($_GET['verwijder']) && is_numeric($_GET['verwijder'])) {
    db()->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([(int)$_GET['verwijder']]);
    flash('succes', 'Bericht verwijderd.');
    redirect(SITE_URL . '/admin/berichten.php');
}

$succes = flash('succes');

$gekozen = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $stmt = db()->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $gekozen = $stmt->fetch();
    if ($gekozen && !$gekozen['gelezen']) {
        db()->prepare("UPDATE contact_messages SET gelezen = 1 WHERE id = ?")->execute([$gekozen['id']]);
        $gekozen['gelezen'] = 1;
    }
}

$berichten = db()->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
$ongelezen = db()->query("SELECT COUNT(*) FROM contact_messages WHERE gelezen = 0")->fetchColumn();
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="admin-h1">Contactberichten</h1>
        <p class="admin-sub">
            <?= $ongelezen ?> ongelezen bericht<?= $ongelezen !== 1 ? 'en' : '' ?> –
            <?= count($berichten) ?> totaal
        </p>
    </div>
</div>

<?php if ($succes): ?><div class="alert alert-succes">✓ <?= h($succes) ?></div><?php endif; ?>

<?php if ($gekozen): ?>
    <!-- Bericht detail -->
    <div class="admin-kaart" style="border-top:3px solid var(--rood);margin-bottom:1.75rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;flex-wrap:wrap;gap:.75rem;">
            <div>
                <h3 style="margin-bottom:.25rem;"><?= h($gekozen['onderwerp']) ?></h3>
                <p style="color:var(--grijs-50);font-size:.84rem;">
                    Ontvangen op <?= formatDate($gekozen['created_at']) ?>
                </p>
            </div>
            <a href="<?= SITE_URL ?>/admin/berichten.php" class="btn btn-secondary">← Terug</a>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.25rem;">
            <div>
                <p class="form-label">Van</p>
                <p><strong><?= h($gekozen['naam']) ?></strong></p>
            </div>
            <div>
                <p class="form-label">E-mailadres</p>
                <p><a href="mailto:<?= h($gekozen['email']) ?>"><?= h($gekozen['email']) ?></a></p>
            </div>
            <?php if ($gekozen['telefoon']): ?>
                <div>
                    <p class="form-label">Telefoon</p>
                    <p><a href="tel:<?= h($gekozen['telefoon']) ?>"><?= h($gekozen['telefoon']) ?></a></p>
                </div>
            <?php endif; ?>
        </div>
        <div style="background:var(--grijs-95);border-radius:var(--radius);padding:1.25rem;margin-bottom:1.25rem;">
            <p class="form-label" style="margin-bottom:.5rem;">Bericht</p>
            <p style="white-space:pre-wrap;line-height:1.7;"><?= h($gekozen['bericht']) ?></p>
        </div>
        <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
            <a href="mailto:<?= h($gekozen['email']) ?>?subject=Re: <?= urlencode($gekozen['onderwerp']) ?>" class="btn btn-primary">
                 Beantwoorden
            </a>
            <a href="?verwijder=<?= $gekozen['id'] ?>" class="btn btn-secondary js-verwijder"
                data-bevestig="Dit bericht definitief verwijderen?">
                 Verwijderen
            </a>
        </div>
    </div>
<?php endif; ?>


<div class="admin-tabel-wrap">
    <table class="admin-tabel">
        <thead>
            <tr>
                <th>Status</th>
                <th>Naam</th>
                <th>Onderwerp</th>
                <th>E-mail</th>
                <th>Datum</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($berichten): foreach ($berichten as $msg): ?>
                    <tr style="<?= !$msg['gelezen'] ? 'background:var(--color-warning-soft);font-weight:500;' : '' ?>">
                        <td>
                            <span class="badge <?= $msg['gelezen'] ? 'badge-grijs' : 'badge-rood' ?>">
                                <?= $msg['gelezen'] ? 'Gelezen' : ' Nieuw' ?>
                            </span>
                        </td>
                        <td><?= h($msg['naam']) ?></td>
                        <td style="max-width:200px;"><?= h($msg['onderwerp']) ?></td>
                        <td style="font-size:.82rem;"><a href="mailto:<?= h($msg['email']) ?>"><?= h($msg['email']) ?></a></td>
                        <td style="white-space:nowrap;font-size:.82rem;"><?= formatDate($msg['created_at']) ?></td>
                        <td>
                            <div style="display:flex;gap:.75rem;">
                                <a href="?id=<?= $msg['id'] ?>" class="tabel-link">Bekijken</a>
                                <a href="?verwijder=<?= $msg['id'] ?>" class="tabel-verwijder js-verwijder"
                                    data-bevestig="Dit bericht verwijderen?">Verwijderen</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="6" class="leeg-cel">Nog geen contactberichten ontvangen.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>