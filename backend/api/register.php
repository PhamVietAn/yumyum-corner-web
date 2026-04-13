<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    methodNotAllowed(['POST']);
}

$input = readJsonInput();
$fullName = trim((string) ($input['fullName'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));
$password = (string) ($input['password'] ?? '');
$phone = trim((string) ($input['phone'] ?? ''));

if ($fullName === '' || $email === '' || $password === '') {
    jsonResponse([
        'success' => false,
        'message' => 'Họ tên, email, mật khẩu là bắt buộc',
    ], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse([
        'success' => false,
        'message' => 'Email không hợp lệ',
    ], 422);
}

if (strlen($password) < 8) {
    jsonResponse([
        'success' => false,
        'message' => 'Mật khẩu tối thiểu 8 ký tự',
    ], 422);
}

try {
    $pdo = getPdoConnection();

    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $check->execute(['email' => $email]);
    if ($check->fetch()) {
        jsonResponse([
            'success' => false,
            'message' => 'Email đã tồn tại',
        ], 409);
    }

    $insert = $pdo->prepare(
        'INSERT INTO users (full_name, email, phone, password_hash, role, status)
         VALUES (:full_name, :email, :phone, :password_hash, :role, :status)'
    );

    $insert->execute([
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'password_hash' => $password,
        'role' => 'user',
        'status' => 'active',
    ]);

    jsonResponse([
        'success' => true,
        'message' => 'Đăng ký thành công',
    ], 201);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể đăng ký tài khoản',
        'error' => $exception->getMessage(),
    ], 500);
}
