<?php
$adminTitle = 'Agenda beheren';
require_once __DIR__ . '/includes/admin_header.php';

$fout   = '';
$succes = flash('succes');

// Verwijderen
if (isset($_GET['verwijder']) && is_numeric($_GET['verwijder'])) {
    db()->prepare("DELETE FROM agenda WHERE id = ?")->execute([(int)$_GET['verwijder']]);
    flash('succes', 'Agenda-item verwijderd.');
    redirect(SITE_URL . '/admin/agenda.php');
}

// Bewerken
$bewerkItem = null;
if (isset($_GET['bewerken']) && is_numeric($_GET['bewerken'])) {
    $stmt = db()->prepare("SELECT * FROM agenda WHERE id = ?");
    $stmt->execute([(int)$_GET['bewerken']]);
    $bewerkItem = $stmt->fetch();
}

// Opslaan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bewerkId    = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;
    $titel       = trim($_POST['titel'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $datum       = $_POST['datum'] ?? '';
    $tijdstip    = trim($_POST['tijdstip'] ?? '');
    $locatie     = trim($_POST['locatie'] ?? '');
    $type        = trim($_POST['type'] ?? '');
    $aanmelding  = trim($_POST['aanmelding_url'] ?? '');

    if (strlen($titel) < 2) {
        $fout = 'Vul een titel in.';
    } elseif (!$datum) {
        $fout = 'Vul een datum in.';
    } else {
        if ($bewerkId) {
            $stmt = db()->prepare("UPDATE agenda SET titel=?,beschrijving=?,datum=?,tijdstip=?,locatie=?,type=?,aanmelding_url=? WHERE id=?");
            $stmt->execute([$titel, $beschrijving, $datum, $tijdstip, $locatie, $type, $aanmelding, $bewerkId]);
            flash('succes', 'Agenda-item bijgewerkt.');
        } else {
            $stmt = db()->prepare("INSERT INTO agenda (titel,beschrijving,datum,tijdstip,locatie,type,aanmelding_url) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$titel, $beschrijving, $datum, $tijdstip, $locatie, $type, $aanmelding]);
            flash('succes', 'Agenda-item toegevoegd.');
        }
        redirect(SITE_URL . '/admin/agenda.php');
    }
}

$items = db()->query("SELECT * FROM agenda ORDER BY datum ASC")->fetchAll();
$typen = ['Spreekuur', 'Vergadering', 'Informatie', 'Actie', 'Overig'];
$maandAfk = ['','jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec'];
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="admin-h1">Agenda</h1>
        <p class="admin-sub">Beheer aankomende evenementen en activiteiten.</p>
    </div>
    <a href="?nieuw=1" class="btn btn-primary">+ Nieuw evenement</a>
</div>

<?php if ($succes): ?><div class="alert alert-succes">✓ <?= h($succes) ?></div><?php endif; ?>


<?php if (isset($_GET['nieuw']) || $bewerkItem || $fout): ?>
<div class="admin-kaart" style="border-top:3px solid var(--rood);margin-bottom:1.75rem;">
    <div class="admin-kaart__titel"><?= $bewerkItem ? 'Evenement bewerken' : 'Nieuw evenement' ?></div>
    <?php if ($fout): ?><div class="alert alert-fout"><?= h($fout) ?></div><?php endif; ?>
    <form method="POST">
        <?php if ($bewerkItem): ?><input type="hidden" name="id" value="<?= $bewerkItem['id'] ?>"><?php endif; ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group">
                <label class="form-label">Titel *</label>
                <input type="text" name="titel" class="form-input" value="<?= h($bewerkItem['titel'] ?? $_POST['titel'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">– Kies type –</option>
                    <?php foreach ($typen as $t): ?>
                    <option value="<?= h($t) ?>" <?= ($bewerkItem['type'] ?? $_POST['type'] ?? '') === $t ? 'selected' : '' ?>><?= h($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Datum *</label>
                <input type="date" name="datum" class="form-input" value="<?= h($bewerkItem['datum'] ?? $_POST['datum'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tijdstip</label>
                <input type="text" name="tijdstip" class="form-input" placeholder="10:00 – 12:00" value="<?= h($bewerkItem['tijdstip'] ?? $_POST['tijdstip'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Locatie</label>
                <input type="text" name="locatie" class="form-input" placeholder="Naam locatie, Heerenveen" value="<?= h($bewerkItem['locatie'] ?? $_POST['locatie'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Aanmelding URL</label>
                <input type="url" name="aanmelding_url" class="form-input" placeholder="https://..." value="<?= h($bewerkItem['aanmelding_url'] ?? $_POST['aanmelding_url'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Beschrijving</label>
            <textarea name="beschrijving" class="form-textarea" rows="3"><?= h($bewerkItem['beschrijving'] ?? $_POST['beschrijving'] ?? '') ?></textarea>
        </div>
        <div style="display:flex;gap:.75rem;">
            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="<?= SITE_URL ?>/admin/agenda.php" class="btn btn-secondary">Annuleren</a>
        </div>
    </form>
</div>
<?php endif; ?>


<div class="admin-tabel-wrap">
    <table class="admin-tabel">
        <thead>
            <tr><th>Datum</th><th>Titel</th><th>Type</th><th>Locatie</th><th>Acties</th></tr>
        </thead>
        <tbody>
            <?php if ($items): foreach ($items as $item):
                $verleden = strtotime($item['datum']) < strtotime('today');
            ?>
            <tr style="<?= $verleden ? 'opacity:.55;' : '' ?>">
                <td style="white-space:nowrap;font-weight:600;">
                    <?= formatDate($item['datum']) ?>
                    <?php if ($verleden): ?><br><small style="color:var(--grijs-50);">verleden</small><?php endif; ?>
                </td>
                <td><?= h($item['titel']) ?></td>
                <td><span class="badge badge-rood"><?= h($item['type'] ?? '–') ?></span></td>
                <td style="font-size:.84rem;"><?= h($item['locatie'] ?? '–') ?></td>
                <td>
                    <div style="display:flex;gap:.75rem;">
                        <a href="?bewerken=<?= $item['id'] ?>" class="tabel-link">Bewerken</a>
                        <a href="?verwijder=<?= $item['id'] ?>" class="tabel-verwijder js-verwijder"
                           data-bevestig="'<?= h(addslashes($item['titel'])) ?>' verwijderen?">Verwijderen</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="5" class="leeg-cel">Geen agenda-items. <a href="?nieuw=1">Voeg het eerste evenement toe →</a></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
