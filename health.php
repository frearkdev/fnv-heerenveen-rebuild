<?php
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json');

try {
  db()->query('SELECT 1');
  http_response_code(200);
  echo json_encode([
    'status' => 'ok',
    'app' => SITE_NAME,
    'env' => APP_ENV,
    'timestamp' => date(DATE_ATOM),
  ]);
} catch (Throwable $e) {
  http_response_code(503);
  echo json_encode([
    'status' => 'error',
    'message' => 'Database not reachable',
  ]);
}
