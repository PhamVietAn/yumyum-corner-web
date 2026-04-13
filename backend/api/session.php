<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    methodNotAllowed(['GET']);
}

$user = currentUser();

jsonResponse([
    'success' => true,
    'isLoggedIn' => $user !== null,
    'data' => $user,
]);
