<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { header('Location: ' . SITE_URL . '/index.php'); exit; }

$stmt = db()->prepare("SELECT * FROM pages WHERE slug = ? AND active = 1 LIMIT 1");
$stmt->execute([$slug]);
$pagina = $stmt->fetch();

if (!$pagina) {
    $pageTitle = 'Niet gevonden';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="sectie"><div class="container not-found text-center"><div class="not-found__getal">404</div><h1>Pagina niet gevonden</h1><p>Deze pagina bestaat niet of is verwijderd.</p><a href="' . SITE_URL . '/index.php" class="btn btn-primary mt-2">Naar de homepage</a></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $pagina['title'];
$metaDesc  = $pagina['meta_description'] ?? '';
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="paginakop">
    <div class="container">
        <p class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> › <?= h($pagina['title']) ?></p>
        <h1><?= h($pagina['title']) ?></h1>
        <?php if ($metaDesc): ?><p><?= h($metaDesc) ?></p><?php endif; ?>
    </div>
</div>

<section class="sectie">
    <div class="container">
        <div style="max-width:800px;" class="pagina-inhoud">
            <?= $pagina['content'] ?>
        </div>
    </div>
</section>

<style>
.pagina-inhoud h2{color:var(--grijs-10);margin:2rem 0 .75rem}
.pagina-inhoud h3{color:var(--rood);margin:1.5rem 0 .5rem}
.pagina-inhoud ul{margin-left:1.5rem;margin-bottom:1rem}
.pagina-inhoud li{margin-bottom:.35rem}
.pagina-inhoud a{color:var(--rood);font-weight:500}
.pagina-inhoud p{line-height:1.75}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
