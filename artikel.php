<?php
require_once __DIR__ . '/includes/config.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: ' . SITE_URL . '/nieuws.php');
    exit;
}

$stmt = db()->prepare("SELECT * FROM news WHERE slug = ? AND published = 1 LIMIT 1");
$stmt->execute([$slug]);
$artikel = $stmt->fetch();

if (!$artikel) {
    $pageTitle = 'Niet gevonden';
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="sectie"><div class="container not-found text-center"><div class="not-found__getal">404</div><h1>Artikel niet gevonden</h1><p>Dit artikel bestaat niet of is verwijderd.</p><a href="' . SITE_URL . '/nieuws.php" class="btn btn-primary mt-2">Terug naar nieuws</a></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $artikel['title'];
$metaDesc  = $artikel['excerpt'];

$stmt = db()->prepare("SELECT * FROM news WHERE published = 1 AND id != ? ORDER BY published_at DESC LIMIT 3");
$stmt->execute([$artikel['id']]);
$gerelateerd = $stmt->fetchAll();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="paginakop">
    <div class="container">
        <p class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> › <a href="<?= SITE_URL ?>/nieuws.php">Nieuws</a> › <?= h($artikel['title']) ?></p>
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.75rem;flex-wrap:wrap;">
            <?php if ($artikel['category']): ?><span class="badge" style="background:var(--color-primary-light);color:var(--color-text);"><?= h($artikel['category']) ?></span><?php endif; ?>
            <span style="opacity:.8;font-size:.85rem;"><?= $artikel['published_at'] ? formatDate($artikel['published_at']) : '' ?></span>
        </div>
        <h1><?= h($artikel['title']) ?></h1>
    </div>
</div>

<section class="sectie">
    <div class="container artikel-layout">
        <article>
            <p class="artikel-lead"><?= h($artikel['excerpt']) ?></p>
            <?php if ($artikel['image']): ?>
                <img src="<?= h($artikel['image']) ?>" alt="<?= h($artikel['title']) ?>" class="artikel-img">
            <?php endif; ?>
            <div class="artikel-inhoud">
                <?= $artikel['content'] ?>
            </div>
            <div style="margin-top:2.5rem;padding-top:1.5rem;border-top:2px solid var(--border);">
                <a href="<?= SITE_URL ?>/nieuws.php" class="btn btn-secondary">← Terug naar nieuws</a>
            </div>
        </article>

        <aside class="artikel-zijbalk">
            <div class="info-blok">
                <h4>Vragen over dit artikel?</h4>
                <p>Neem contact op met ons spreekuur. We helpen u graag verder.</p>
                <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary w-100">Contact opnemen</a>
            </div>
            <div class="info-blok">
                <h4>Word lid van FNV</h4>
                <p>Als lid sta je sterker op het werk. Sluit u aan bij de grootste vakbond.</p>
                <a href="https://www.fnv.nl/lid-worden" target="_blank" rel="noopener" class="btn btn-secondary w-100">Lid worden</a>
            </div>
            <?php if ($gerelateerd): ?>
                <div class="info-blok">
                    <h4>Meer nieuws</h4>
                    <?php foreach ($gerelateerd as $g): ?>
                        <div style="padding:.6rem 0;border-bottom:1px solid var(--border);">
                            <a href="<?= SITE_URL ?>/artikel.php?slug=<?= h($g['slug']) ?>" style="color:var(--grijs-10);font-weight:600;font-size:.85rem;"><?= h($g['title']) ?></a>
                            <p style="color:var(--grijs-50);font-size:.77rem;margin:.2rem 0 0;"><?= $g['published_at'] ? formatDate($g['published_at']) : '' ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>