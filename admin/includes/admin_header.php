<?php
require_once dirname(__DIR__, 2) . '/includes/config.php';
requireLogin();
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
$adminUser = db()->prepare("SELECT name, email FROM users WHERE id = ?");
$adminUser->execute([$_SESSION['admin_id']]);
$adminUser = $adminUser->fetch();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? h($adminTitle) . ' – ' : '' ?>Beheer – FNV Heerenveen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>

<body>
    <div class="admin-layout">


        <div class="admin-overlay" id="adminOverlay"></div>


        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar__logo">
                <img src="<?= SITE_URL ?>/assets/img/lokaal-fnv-logo-fixed.png" alt="Lokaal FNV" class="admin-sidebar__logo-image" loading="eager" decoding="async">
                <div>
                    <strong>Heerenveen</strong>
                    <small>Beheer</small>
                </div>
            </div>
            <nav class="admin-nav">
                <a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= $adminPage === 'dashboard' ? 'actief' : '' ?>">
                    <span>▪</span> Dashboard
                </a>
                <a href="<?= SITE_URL ?>/admin/nieuws.php" class="<?= in_array($adminPage, ['nieuws', 'nieuws-form']) ? 'actief' : '' ?>">
                    <span>▪</span> Nieuws
                </a>
                <a href="<?= SITE_URL ?>/admin/agenda.php" class="<?= $adminPage === 'agenda' ? 'actief' : '' ?>">
                    <span>▪</span> Agenda
                </a>
                <a href="<?= SITE_URL ?>/admin/paginas.php" class="<?= $adminPage === 'paginas' ? 'actief' : '' ?>">
                    <span>▪</span> Pagina's
                </a>
                <a href="<?= SITE_URL ?>/admin/berichten.php" class="<?= $adminPage === 'berichten' ? 'actief' : '' ?>">
                    <span>▪</span> Contactberichten
                </a>
            </nav>
            <div class="admin-sidebar__footer">
                <a href="<?= SITE_URL ?>/admin/uitloggen.php" class="admin-logout">↩ Uitloggen</a>
                <a href="<?= SITE_URL ?>/index.php" target="_blank" class="admin-site-link">Website bekijken →</a>
            </div>
        </aside>


        <div class="admin-hoofd">
            <header class="admin-topbar">
                <button class="hamburger" id="adminHamburger" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
                <span class="admin-topbar__naam">👤 <?= h($adminUser['name'] ?? '') ?></span>
            </header>
            <div class="admin-inhoud">