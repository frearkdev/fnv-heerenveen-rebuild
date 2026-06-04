<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentPath = $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($metaDesc) ? h($metaDesc) : 'FNV Heerenveen – Uw lokale vakbond voor werkenden, uitkeringsgerechtigden en gepensioneerden in de regio Heerenveen-Opsterland.' ?>">
    <title><?= isset($pageTitle) ? h($pageTitle) . ' – ' : '' ?>FNV Heerenveen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>

<body>

    <!-- Top bar -->
    <div class="topbar">
        <div class="container topbar__inner">
            <span>Lokaal Netwerk FNV Heerenveen-Opsterland</span>
            <div class="topbar__links">
                <a href="https://www.fnv.nl" target="_blank" rel="noopener">fnv.nl ↗</a>
                <span>|</span>
                <a href="<?= SITE_URL ?>/contact.php">Contact</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header" id="header">
        <div class="container header__inner">

            <!-- Logo -->
            <a href="<?= SITE_URL ?>/index.php" class="logo" aria-label="FNV Heerenveen – naar startpagina">
                <img src="<?= SITE_URL ?>/assets/img/lokaal-fnv-logo-fixed.png" alt="Lokaal FNV" class="logo__image" loading="eager" decoding="async">
            </a>

            <!-- Nav desktop -->
            <nav class="nav" aria-label="Hoofdnavigatie">
                <a href="<?= SITE_URL ?>/index.php" class="nav__link <?= $currentPage === 'index' ? 'nav__link--actief' : '' ?>">Home</a>
                <a href="<?= SITE_URL ?>/nieuws.php" class="nav__link <?= in_array($currentPage, ['nieuws', 'artikel']) ? 'nav__link--actief' : '' ?>">Nieuws</a>
                <a href="<?= SITE_URL ?>/agenda.php" class="nav__link <?= $currentPage === 'agenda' ? 'nav__link--actief' : '' ?>">Agenda</a>
                <a href="<?= SITE_URL ?>/pagina.php?slug=over-ons" class="nav__link <?= ($currentPage === 'pagina' && ($_GET['slug'] ?? '') === 'over-ons') ? 'nav__link--actief' : '' ?>">Over ons</a>
                <a href="<?= SITE_URL ?>/pagina.php?slug=diensten" class="nav__link <?= ($currentPage === 'pagina' && ($_GET['slug'] ?? '') === 'diensten') ? 'nav__link--actief' : '' ?>">Diensten</a>
                <a href="<?= SITE_URL ?>/pagina.php?slug=lid-worden" class="nav__link nav__link--lid">Lid worden</a>
            </nav>

            <!-- Zoek + hamburger -->
            <div class="header__acties">
                <button class="zoek-btn" id="zoekToggle" aria-label="Zoeken" title="Zoeken">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <!-- Zoekbalk -->
        <div class="zoekbalk" id="zoekbalk" hidden>
            <div class="container">
                <form action="<?= SITE_URL ?>/nieuws.php" method="GET" class="zoekbalk__form">
                    <input type="search" name="zoek" class="form-input" placeholder="Zoek op de website..." value="<?= h($_GET['zoek'] ?? '') ?>" autofocus>
                    <button type="submit" class="btn btn-primary">Zoeken</button>
                </form>
            </div>
        </div>

        <!-- Mobiel menu -->
        <div class="mobiel-menu" id="mobielMenu">
            <a href="<?= SITE_URL ?>/index.php" class="mobiel-link">Home</a>
            <a href="<?= SITE_URL ?>/nieuws.php" class="mobiel-link">Nieuws</a>
            <a href="<?= SITE_URL ?>/agenda.php" class="mobiel-link">Agenda</a>
            <a href="<?= SITE_URL ?>/pagina.php?slug=over-ons" class="mobiel-link">Over ons</a>
            <a href="<?= SITE_URL ?>/pagina.php?slug=diensten" class="mobiel-link">Diensten</a>
            <a href="<?= SITE_URL ?>/contact.php" class="mobiel-link">Contact</a>
            <a href="<?= SITE_URL ?>/pagina.php?slug=lid-worden" class="btn btn-primary" style="margin:1rem 1.5rem;">Lid worden</a>
        </div>
    </header>

    <main id="main">