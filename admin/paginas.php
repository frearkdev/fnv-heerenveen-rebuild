<?php
$adminTitle = "Pagina's beheren";
require_once __DIR__ . '/includes/admin_header.php';

$succes = flash('succes');
$fout   = '';

// Pagina laden om te bewerken
$gekozen = null;
if (isset($_GET['slug'])) {
    $stmt = db()->prepare("SELECT * FROM pages WHERE slug = ?");
    $stmt->execute([trim($_GET['slug'])]);
    $gekozen = $stmt->fetch();
}

// Opslaan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slug'])) {
    $slug        = trim($_POST['slug']);
    $title       = trim($_POST['title'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $metaDesc    = trim($_POST['meta_description'] ?? '');
    $active      = isset($_POST['active']) ? 1 : 0;

    if (strlen($title) < 2)   $fout = 'Vul een titel in.';
    if (strlen($content) < 5) $fout = 'Vul de inhoud in.';

    if (!$fout) {
        $stmt = db()->prepare("UPDATE pages SET title=?, content=?, meta_description=?, active=? WHERE slug=?");
        $stmt->execute([$title, $content, $metaDesc, $active, $slug]);
        flash('succes', 'Pagina opgeslagen!');
        redirect(SITE_URL . '/admin/paginas.php?slug=' . urlencode($slug));
    } else {
        // Herlaad aangepaste versie
        $gekozen = array_merge($gekozen ?? [], [
            'title' => $title, 'content' => $content,
            'meta_description' => $metaDesc, 'active' => $active, 'slug' => $slug
        ]);
    }
}

$paginas = db()->query("SELECT * FROM pages ORDER BY sort_order ASC")->fetchAll();
?>

<h1 class="admin-h1">Pagina's</h1>
<p class="admin-sub">Bewerk de inhoud van de CMS-pagina's op de website.</p>

<?php if ($succes): ?><div class="alert alert-succes">✓ <?= h($succes) ?></div><?php endif; ?>

<div class="paginas-layout">

    <!-- Lijst met pagina's -->
    <div class="paginas-lijst">
        <?php foreach ($paginas as $p): ?>
        <a href="?slug=<?= urlencode($p['slug']) ?>"
           class="pagina-rij <?= ($gekozen['slug'] ?? '') === $p['slug'] ? 'actief' : '' ?>">
            <div>
                <strong><?= h($p['title']) ?></strong>
                <small>/pagina.php?slug=<?= h($p['slug']) ?></small>
            </div>
            <span class="badge <?= $p['active'] ? 'badge-groen' : 'badge-grijs' ?>">
                <?= $p['active'] ? 'Actief' : 'Verborgen' ?>
            </span>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Editor -->
    <?php if ($gekozen): ?>
    <div class="pagina-editor">
        <h3 style="margin-bottom:1.25rem;"><?= h($gekozen['title']) ?> bewerken</h3>
        <?php if ($fout): ?><div class="alert alert-fout"><?= h($fout) ?></div><?php endif; ?>

        <form method="POST">
            <input type="hidden" name="slug" value="<?= h($gekozen['slug']) ?>">

            <div class="form-group">
                <label class="form-label">Paginatitel</label>
                <input type="text" name="title" class="form-input" value="<?= h($gekozen['title']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Meta-beschrijving (SEO)
                    <small style="font-weight:400;color:var(--grijs-50);">max. 160 tekens</small>
                </label>
                <input type="text" id="meta_description" name="meta_description" class="form-input"
                    value="<?= h($gekozen['meta_description'] ?? '') ?>"
                    maxlength="160" data-max="160">
                <p class="form-small"><span data-teller="meta_description">0</span>/160</p>
            </div>
            <div class="form-group">
                <label class="form-label">Inhoud <small style="font-weight:400;color:var(--grijs-50);">(HTML)</small></label>
                <textarea name="content" class="form-textarea" rows="20"
                    style="font-family:monospace;font-size:.85rem;" required><?= h($gekozen['content']) ?></textarea>
                <p class="form-small">HTML-tags zijn toegestaan: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;a&gt;, &lt;strong&gt;</p>
            </div>
            <div class="form-group">
                <label class="schakelaar">
                    <input type="checkbox" name="active" value="1" <?= $gekozen['active'] ? 'checked' : '' ?>>
                    <span class="schakelaar__slider"></span>
                    <span>Pagina actief (zichtbaar op website)</span>
                </label>
            </div>
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary">Wijzigingen opslaan</button>
                <a href="<?= SITE_URL ?>/pagina.php?slug=<?= h($gekozen['slug']) ?>" target="_blank" class="btn btn-secondary">Bekijk pagina →</a>
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="pagina-editor" style="display:flex;align-items:center;justify-content:center;min-height:300px;color:var(--grijs-50);">
        <div class="text-center">
            <p style="font-size:2rem;margin-bottom:.75rem;">📄</p>
            <p>Kies een pagina aan de linkerkant om te bewerken.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
