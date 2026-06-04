<?php
$adminTitle = 'Nieuwsbericht';
require_once __DIR__ . '/includes/admin_header.php';

$id     = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
$isNieuw = $id === null;
$fout   = '';
$succes = '';

$item = [
    'title' => '', 'excerpt' => '', 'content' => '', 'image' => '',
    'category' => '', 'published' => 0, 'published_at' => ''
];

if (!$isNieuw) {
    $stmt = db()->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $gevonden = $stmt->fetch();
    if (!$gevonden) {
        redirect(SITE_URL . '/admin/nieuws.php');
    }
    $item = $gevonden;
    if ($item['published_at']) {
        $item['published_at'] = date('Y-m-d\TH:i', strtotime($item['published_at']));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $excerpt     = trim($_POST['excerpt'] ?? '');
    $content     = trim($_POST['content'] ?? '');
    $image       = trim($_POST['image'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $published   = isset($_POST['published']) ? 1 : 0;
    $publishedAt = $_POST['published_at'] ? date('Y-m-d H:i:s', strtotime($_POST['published_at'])) : date('Y-m-d H:i:s');

    if (strlen($title) < 3)   $fout = 'Vul een titel in (minimaal 3 tekens).';
    if (strlen($excerpt) < 5) $fout = 'Vul een samenvatting in.';
    if (strlen($content) < 5) $fout = 'Vul de inhoud in.';

    if (!$fout) {
        $slugBase = slug($title);
        $slugFinal = $slugBase;

        if ($isNieuw) {
           
            $i = 1;
            while (db()->prepare("SELECT id FROM news WHERE slug = ?")->execute([$slugFinal]) &&
                   db()->query("SELECT id FROM news WHERE slug = '$slugFinal'")->fetchColumn()) {
                $slugFinal = $slugBase . '-' . $i++;
            }
            $stmt = db()->prepare("INSERT INTO news (title, slug, excerpt, content, image, category, published, published_at) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $slugFinal, $excerpt, $content, $image, $category, $published, $publishedAt]);
            flash('succes', 'Nieuwsbericht aangemaakt!');
            redirect(SITE_URL . '/admin/nieuws.php');
        } else {
            $stmt = db()->prepare("UPDATE news SET title=?, excerpt=?, content=?, image=?, category=?, published=?, published_at=? WHERE id=?");
            $stmt->execute([$title, $excerpt, $content, $image, $category, $published, $publishedAt, $id]);
            $succes = 'Wijzigingen opgeslagen!';
            // Herlaad
            $stmt = db()->prepare("SELECT * FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            $item['published_at'] = $item['published_at'] ? date('Y-m-d\TH:i', strtotime($item['published_at'])) : '';
        }
    }

    if ($fout) {
        
        $item = array_merge($item, [
            'title' => $title, 'excerpt' => $excerpt, 'content' => $content,
            'image' => $image, 'category' => $category, 'published' => $published,
            'published_at' => $_POST['published_at'] ?? ''
        ]);
    }
}

$categorieen = ['Cao', 'Dienstverlening', 'Belasting', 'Arbeidsmarkt', 'Nieuws', 'Overig'];
?>

<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.75rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="admin-h1"><?= $isNieuw ? 'Nieuw nieuwsbericht' : 'Bericht bewerken' ?></h1>
        <p class="admin-sub"><?= $isNieuw ? 'Schrijf en publiceer een nieuw artikel.' : 'Wijzig dit nieuwsbericht.' ?></p>
    </div>
    <a href="<?= SITE_URL ?>/admin/nieuws.php" class="btn btn-secondary">← Terug naar overzicht</a>
</div>

<?php if ($fout):   ?><div class="alert alert-fout"><?= h($fout) ?></div><?php endif; ?>
<?php if ($succes): ?><div class="alert alert-succes">✓ <?= h($succes) ?></div><?php endif; ?>

<form method="POST">
    <div style="display:grid;grid-template-columns:1fr 280px;gap:1.5rem;align-items:start;">

      
        <div>
            <div class="admin-kaart">
                <div class="form-group">
                    <label class="form-label" for="title">Titel *</label>
                    <input type="text" id="title" name="title" class="form-input" value="<?= h($item['title']) ?>" placeholder="Artikeltitel" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="excerpt">Samenvatting *</label>
                    <textarea id="excerpt" name="excerpt" class="form-textarea" rows="3"
                        placeholder="Korte samenvatting (max. 500 tekens)"
                        data-max="500" required><?= h($item['excerpt']) ?></textarea>
                    <p class="form-small"><span data-teller="excerpt">0</span> tekens</p>
                </div>
                <div class="form-group">
                    <label class="form-label" for="content">Inhoud * <small style="font-weight:400;color:var(--grijs-50);">(HTML toegestaan)</small></label>
                    <textarea id="content" name="content" class="form-textarea" rows="18"
                        placeholder="Volledige inhoud van het artikel. HTML-tags zoals &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt; zijn toegestaan."
                        required style="font-family:monospace;font-size:.88rem;"><?= h($item['content']) ?></textarea>
                </div>
            </div>
        </div>

       
        <aside>
            <div class="admin-kaart">
                <div class="admin-kaart__titel">Publiceren</div>
                <div class="form-group">
                    <label class="schakelaar">
                        <input type="checkbox" name="published" value="1" <?= $item['published'] ? 'checked' : '' ?>>
                        <span class="schakelaar__slider"></span>
                        <span id="pubLabel"><?= $item['published'] ? 'Gepubliceerd' : 'Concept' ?></span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="form-label" for="published_at">Publicatiedatum</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-input"
                        value="<?= h($item['published_at'] ?? '') ?>">
                </div>
                <?php if ($fout): ?>
                <div class="alert alert-fout" style="margin-bottom:1rem;"><?= h($fout) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary w-100">Opslaan</button>
                <?php if (!$isNieuw): ?>
                <a href="<?= SITE_URL ?>/artikel.php?slug=<?= h($item['slug'] ?? '') ?>" target="_blank"
                   class="btn btn-secondary w-100" style="margin-top:.5rem;">Bekijk artikel →</a>
                <?php endif; ?>
            </div>

            <div class="admin-kaart">
                <div class="admin-kaart__titel">Instellingen</div>
                <div class="form-group">
                    <label class="form-label" for="category">Categorie</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">– Geen categorie –</option>
                        <?php foreach ($categorieen as $cat): ?>
                        <option value="<?= h($cat) ?>" <?= ($item['category'] ?? '') === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="image">Afbeelding URL</label>
                    <input type="url" id="image" name="image" class="form-input"
                        value="<?= h($item['image'] ?? '') ?>"
                        placeholder="https://example.com/afbeelding.jpg">
                    <p class="form-small">Plak een URL naar een afbeelding</p>
                </div>
            </div>
        </aside>
    </div>
</form>

<script>
// Toggle label publicatie
document.querySelector('[name="published"]')?.addEventListener('change', function() {
    document.getElementById('pubLabel').textContent = this.checked ? 'Gepubliceerd' : 'Concept';
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
