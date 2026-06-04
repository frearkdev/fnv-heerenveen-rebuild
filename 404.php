<?php
require_once __DIR__ . '/includes/config.php';
http_response_code(404);
$pageTitle = 'Pagina niet gevonden';
require_once __DIR__ . '/includes/header.php';
?>

<section class="sectie not-found" style="min-height:60vh;display:flex;align-items:center;">
    <div class="container text-center">
        <div class="not-found__getal">404</div>
        <h1>Pagina niet gevonden</h1>
        <p style="color:var(--grijs-50);margin-bottom:2rem;max-width:420px;margin-left:auto;margin-right:auto;">
            De pagina die u zoekt bestaat niet of is verplaatst.
        </p>
        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">Naar de homepage</a>
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-secondary">Contact opnemen</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
