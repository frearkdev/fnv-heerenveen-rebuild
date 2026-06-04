<?php

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function detectSiteUrl(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    return $scheme . '://' . $host;
}

define('APP_ENV', env('APP_ENV', 'production'));
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'fnv_heerenveen'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));

define('SITE_NAME', env('SITE_NAME', 'FNV Heerenveen'));
define('SITE_URL', rtrim(env('SITE_URL', detectSiteUrl()), '/'));
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'info@fnvheerenveen.nl'));

if (APP_ENV !== 'production') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
}

// Database verbinding
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            die('<style>:root{--color-primary:#0897DD;--color-primary-dark:#067BB5;--color-surface:#FFFFFF;--color-primary-soft:#EAF5FB;}</style>
            <div style="font-family:sans-serif;padding:2rem;background:var(--color-primary-soft);border:2px solid var(--color-primary);border-radius:8px;margin:2rem;">
                <h2 style="color:var(--color-primary-dark);"> Database verbindingsfout</h2>
                <p>Kan geen verbinding maken met de database. Controleer of:</p>
                <ol>
                    <li>MySQL draait (lokaal of in Docker)</li>
                    <li>MySQL/MariaDB draait</li>
                    <li>De database <strong>fnv_heerenveen</strong> bestaat (importeer database.sql)</li>
                    <li>De omgevingsvariabelen (DB_HOST, DB_NAME, DB_USER, DB_PASS) kloppen</li>
                </ol>
                <details><summary>Technische fout</summary><pre>' . htmlspecialchars($e->getMessage()) . '</pre></details>
            </div>');
        }
    }
    return $pdo;
}

// Sessie starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functies
function isLoggedIn(): bool
{
    return isset($_SESSION['admin_id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit;
    }
}

function h(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function slug(string $str): string
{
    $str = strtolower($str);
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}

function formatDate(string $date): string
{
    $maanden = [
        '',
        'januari',
        'februari',
        'maart',
        'april',
        'mei',
        'juni',
        'juli',
        'augustus',
        'september',
        'oktober',
        'november',
        'december'
    ];
    $ts = strtotime($date);
    return date('j', $ts) . ' ' . $maanden[(int)date('n', $ts)] . ' ' . date('Y', $ts);
}

function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function flash(string $key, string $msg = ''): string
{
    if ($msg) {
        $_SESSION['flash'][$key] = $msg;
        return '';
    }
    $val = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $val;
}
