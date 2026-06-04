<?php
require_once __DIR__ . '/includes/config.php';

$pageTitle = 'Nieuws';
$zoek      = trim($_GET['zoek'] ?? '');
$categorie = $_GET['cat'] ?? 'alles';
$pagina    = max(1, (int)($_GET['p'] ?? 1));
$perPagina = 9;
$offset    = ($pagina - 1) * $perPagina;

// Query bouwen
$where  = ['published = 1'];
$params = [];
if ($zoek) {
    $where[]  = '(title LIKE ? OR excerpt LIKE ?)';
    $params[] = "%$zoek%";
    $params[] = "%$zoek%";
}
if ($categorie && $categorie !== 'alles') {
    $where[]  = 'category = ?';
    $params[] = $categorie;
}

$whereStr = implode(' AND ', $where);

// Totaal
$stmt  = db()->prepare("SELECT COUNT(*) FROM news WHERE $whereStr");
$stmt->execute($params);
$totaal = (int)$stmt->fetchColumn();
$aantalPaginas = (int)ceil($totaal / $perPagina);

// Berichten
$stmt = db()->prepare("SELECT * FROM news WHERE $whereStr ORDER BY published_at DESC LIMIT $perPagina OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

// Categorieën
$cats = db()->query("SELECT DISTINCT category FROM news WHERE published = 1 AND category IS NOT NULL ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/includes/header.php';
?>

<div class="paginakop">
    <div class="container">
        <p class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> › Nieuws</p>
        <h1>Nieuws</h1>
        <p>Laatste berichten van FNV Heerenveen en landelijk vakbondsnieuws.</p>
    </div>
</div>

<section class="sectie">
    <div class="container">

        <!-- Filter balk -->
        <div class="filter-balk">
            <form method="GET" style="display:flex;gap:.5rem;">
                <input type="search" name="zoek" class="form-input" placeholder="Zoek in nieuws..." value="<?= h($zoek) ?>" style="max-width:360px;">
                <?php if ($categorie !== 'alles'): ?><input type="hidden" name="cat" value="<?= h($categorie) ?>"><?php endif; ?>
                <button type="submit" class="btn btn-primary">Zoeken</button>
                <?php if ($zoek): ?><a href="<?= SITE_URL ?>/nieuws.php" class="btn btn-secondary">Wissen</a><?php endif; ?>
            </form>
            <div class="filter-balk__cats">
                <a href="<?= SITE_URL ?>/nieuws.php<?= $zoek ? '?zoek='.urlencode($zoek) : '' ?>" class="filter-cat <?= $categorie === 'alles' ? 'actief' : '' ?>">Alles</a>
                <?php foreach ($cats as $cat): ?>
                <a href="<?= SITE_URL ?>/nieuws.php?cat=<?= urlencode($cat) ?><?= $zoek ? '&zoek='.urlencode($zoek) : '' ?>" class="filter-cat <?= $categorie === $cat ? 'actief' : '' ?>"><?= h($cat) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($zoek): ?>
        <p class="mb-2" style="color:var(--grijs-50);">Zoekresultaten voor <strong>"<?= h($zoek) ?>"</strong> – <?= $totaal ?> resultaat<?= $totaal !== 1 ? 'en' : '' ?></p>
        <?php endif; ?>

        <?php if ($items): ?>
        <div class="nieuws-grid">
            <?php foreach ($items as $item): ?>
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
        <div class="text-center" style="padding:4rem 0;color:var(--grijs-50);">
            <p style="font-size:2rem;margin-bottom:1rem;">📰</p>
            <h3>Geen berichten gevonden</h3>
            <p>Probeer een andere zoekterm of categorie.</p>
            <a href="<?= SITE_URL ?>/nieuws.php" class="btn btn-primary mt-2">Alle berichten</a>
        </div>
        <?php endif; ?>

        <!-- Paginering -->
        <?php if ($aantalPaginas > 1): ?>
        <div class="paginering">
            <?php if ($pagina > 1): ?>
            <a href="?p=<?= $pagina-1 ?><?= $zoek ? '&zoek='.urlencode($zoek) : '' ?><?= $categorie !== 'alles' ? '&cat='.urlencode($categorie) : '' ?>" class="paginering__knop">‹</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $aantalPaginas; $i++): ?>
            <a href="?p=<?= $i ?><?= $zoek ? '&zoek='.urlencode($zoek) : '' ?><?= $categorie !== 'alles' ? '&cat='.urlencode($categorie) : '' ?>" class="paginering__knop <?= $i === $pagina ? 'actief' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($pagina < $aantalPaginas): ?>
            <a href="?p=<?= $pagina+1 ?><?= $zoek ? '&zoek='.urlencode($zoek) : '' ?><?= $categorie !== 'alles' ? '&cat='.urlencode($categorie) : '' ?>" class="paginering__knop">›</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
