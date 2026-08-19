<?php
require_once dirname(__DIR__) . '/includes/config.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if ($email && $wachtwoord) {
        $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($wachtwoord, $user['password'])) {
            $_SESSION['admin_id']   = $user['id'];
            $_SESSION['admin_naam'] = $user['name'];
            redirect(SITE_URL . '/admin/dashboard.php');
        } else {
            $fout = 'E-mailadres of wachtwoord is onjuist.';
        }
    } else {
        $fout = 'Vul beide velden in.';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen – Beheer – FNV Heerenveen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>

<body>

    <div class="login-pagina">
        <div class="login-kaart">
            <div class="login-logo">
                <img src="<?= SITE_URL ?>/assets/img/lokaal-fnv-logo-fixed.png" alt="Lokaal FNV" class="login-logo-image" loading="eager" decoding="async">
                <div>
                    <strong>Heerenveen</strong>
                    <small>Beheerpaneel</small>
                </div>
            </div>

            <h2>Inloggen</h2>
            <p class="login-sub">Toegang tot het beheerpaneel</p>

            <?php if ($fout): ?>
                <div class="alert alert-fout"><?= h($fout) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label" for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" class="form-input" placeholder="admin@fnvheerenveen.nl" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label class="form-label" for="wachtwoord">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-primary login-knop">Inloggen</button>
            </form>

            <a href="<?= SITE_URL ?>/index.php" class="login-terug">← Terug naar de website</a>

        </div>
    </div>

</body>

</html>