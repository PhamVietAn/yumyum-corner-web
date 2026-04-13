<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

function profileStats(PDO $pdo, string $email): array
{
    $statement = $pdo->prepare('SELECT COUNT(*) AS total_orders, COALESCE(SUM(total_amount), 0) AS total_spent FROM orders WHERE customer_email = :email');
    $statement->execute(['email' => $email]);
    $row = $statement->fetch() ?: ['total_orders' => 0, 'total_spent' => 0];

    return [
        'totalOrders' => (int) ($row['total_orders'] ?? 0),
        'totalSpent' => (int) ($row['total_spent'] ?? 0),
    ];
}

try {
    $pdo = getPdoConnection();
    $authUser = requireAuth();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $statement = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $authUser['id']]);
        $user = $statement->fetch();

        if (!$user) {
            jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản',
            ], 404);
        }

        $mapped = mapUserRow($user);
        $stats = profileStats($pdo, (string) $user['email']);

        jsonResponse([
            'success' => true,
            'data' => array_merge($mapped, $stats),
        ]);
    }

    if ($method === 'PUT') {
        $input = readJsonInput();

        $fullName = trim((string) ($input['fullName'] ?? ''));
        $email = strtolower(trim((string) ($input['email'] ?? '')));
        $phone = trim((string) ($input['phone'] ?? ''));
        $addressDetail = trim((string) ($input['addressDetail'] ?? ($input['address']['detail'] ?? '')));
        $addressDistrict = trim((string) ($input['addressDistrict'] ?? ($input['address']['district'] ?? '')));
        $addressCity = trim((string) ($input['addressCity'] ?? ($input['address']['city'] ?? '')));

        if ($fullName === '' || $email === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Họ tên và email là bắt buộc',
            ], 422);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse([
                'success' => false,
                'message' => 'Email không hợp lệ',
            ], 422);
        }

        $existing = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
        $existing->execute([
            'email' => $email,
            'id' => $authUser['id'],
        ]);
        if ($existing->fetch()) {
            jsonResponse([
                'success' => false,
                'message' => 'Email đã tồn tại',
            ], 409);
        }

        $oldUser = $pdo->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
        $oldUser->execute(['id' => $authUser['id']]);
        $old = $oldUser->fetch();
        $oldEmail = strtolower((string) ($old['email'] ?? ''));

        $update = $pdo->prepare(
            'UPDATE users
             SET full_name = :full_name,
                 email = :email,
                 phone = :phone,
                 address_detail = :address_detail,
                 address_district = :address_district,
                 address_city = :address_city
             WHERE id = :id'
        );

        $update->execute([
            'id' => $authUser['id'],
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'address_detail' => $addressDetail,
            'address_district' => $addressDistrict,
            'address_city' => $addressCity,
        ]);

        if ($oldEmail !== '' && $oldEmail !== $email) {
            $syncOrders = $pdo->prepare('UPDATE orders SET customer_email = :new_email, customer_name = :new_name WHERE customer_email = :old_email');
            $syncOrders->execute([
                'new_email' => $email,
                'new_name' => $fullName,
                'old_email' => $oldEmail,
            ]);
        } else {
            $syncOrders = $pdo->prepare('UPDATE orders SET customer_name = :new_name WHERE customer_email = :email');
            $syncOrders->execute([
                'new_name' => $fullName,
                'email' => $email,
            ]);
        }

        $_SESSION['auth_user'] = [
            'id' => $authUser['id'],
            'fullName' => $fullName,
            'email' => $email,
            'role' => $authUser['role'],
        ];

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật hồ sơ thành công',
            'data' => currentUser(),
        ]);
    }

    if ($method === 'PATCH') {
        $input = readJsonInput();
        $currentPassword = (string) ($input['currentPassword'] ?? '');
        $newPassword = (string) ($input['newPassword'] ?? '');

        if ($currentPassword === '' || $newPassword === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Vui lòng nhập đủ mật khẩu hiện tại và mật khẩu mới',
            ], 422);
        }

        if (strlen($newPassword) < 8) {
            jsonResponse([
                'success' => false,
                'message' => 'Mật khẩu mới tối thiểu 8 ký tự',
            ], 422);
        }

        $statement = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $authUser['id']]);
        $user = $statement->fetch();

        if (!$user) {
            jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản',
            ], 404);
        }

        $hash = (string) ($user['password_hash'] ?? '');
        $valid = hash_equals($hash, $currentPassword);
        if (!$valid) {
            jsonResponse([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác',
            ], 422);
        }

        $update = $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $update->execute([
            'id' => $authUser['id'],
            'password_hash' => $newPassword,
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }

    if ($method === 'DELETE') {
        $delete = $pdo->prepare('DELETE FROM users WHERE id = :id');
        $delete->execute(['id' => $authUser['id']]);

        $_SESSION = [];
        session_destroy();

        jsonResponse([
            'success' => true,
            'message' => 'Đã xóa tài khoản',
        ]);
    }

    methodNotAllowed(['GET', 'PUT', 'PATCH', 'DELETE']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý hồ sơ người dùng',
        'error' => $exception->getMessage(),
    ], 500);
}
