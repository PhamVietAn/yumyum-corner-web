<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getPdoConnection();
    $pdo->query('SELECT 1');

    echo json_encode([
        'success' => true,
        'message' => 'Database connection is OK',
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error' => $exception->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
