<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    methodNotAllowed(['POST']);
}

$input = readJsonInput();
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = (string) ($input['password'] ?? '');

if ($email === '' || $password === '') {
    jsonResponse([
        'success' => false,
        'message' => 'Email và mật khẩu là bắt buộc',
    ], 422);
}

try {
    $pdo = getPdoConnection();
    $statement = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if (!$user) {
        jsonResponse([
            'success' => false,
            'message' => 'Email chưa được đăng ký',
        ], 401);
    }

    if (($user['status'] ?? 'active') !== 'active') {
        jsonResponse([
            'success' => false,
            'message' => 'Tài khoản đang không hoạt động',
        ], 403);
    }

    $passwordHash = (string) ($user['password_hash'] ?? '');
    $isValid = hash_equals($passwordHash, $password);

    if (!$isValid) {
        jsonResponse([
            'success' => false,
            'message' => 'Mật khẩu không chính xác',
        ], 401);
    }

    $_SESSION['auth_user'] = [
        'id' => (int) $user['id'],
        'fullName' => (string) $user['full_name'],
        'email' => (string) $user['email'],
        'role' => (string) $user['role'],
    ];

    jsonResponse([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'data' => currentUser(),
    ]);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể đăng nhập',
        'error' => $exception->getMessage(),
    ], 500);
}
