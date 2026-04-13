<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

try {
    $pdo = getPdoConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        requireAdmin();

        $sql = 'SELECT u.id, u.full_name, u.email, u.phone, u.address_detail, u.address_district, u.address_city,
                       u.status, u.created_at,
                       COUNT(o.id) AS total_orders,
                       COALESCE(SUM(o.total_amount), 0) AS total_spent
                FROM users u
                LEFT JOIN orders o ON o.customer_email = u.email
                WHERE u.role = :role
                GROUP BY u.id, u.full_name, u.email, u.phone, u.address_detail, u.address_district, u.address_city, u.status, u.created_at
                ORDER BY u.id DESC';

        $statement = $pdo->prepare($sql);
        $statement->execute(['role' => 'user']);

        $data = array_map(
            static function (array $row): array {
                $address = implode(', ', array_filter([
                    trim((string) ($row['address_detail'] ?? '')),
                    trim((string) ($row['address_district'] ?? '')),
                    trim((string) ($row['address_city'] ?? '')),
                ]));

                return [
                    'id' => (string) $row['id'],
                    'name' => (string) $row['full_name'],
                    'email' => (string) $row['email'],
                    'phone' => (string) ($row['phone'] ?? ''),
                    'address' => $address,
                    'totalOrders' => (int) $row['total_orders'],
                    'totalSpent' => (int) $row['total_spent'],
                    'joinDate' => substr((string) $row['created_at'], 0, 10),
                    'status' => (string) ($row['status'] ?? 'active'),
                ];
            },
            $statement->fetchAll()
        );

        jsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    if ($method === 'PATCH') {
        requireAdmin();

        $input = readJsonInput();
        $id = (int) ($input['id'] ?? 0);
        $status = strtolower(trim((string) ($input['status'] ?? '')));

        if ($id <= 0 || !in_array($status, ['active', 'inactive'], true)) {
            jsonResponse([
                'success' => false,
                'message' => 'Id và trạng thái hợp lệ là bắt buộc',
            ], 422);
        }

        $update = $pdo->prepare('UPDATE users SET status = :status WHERE id = :id AND role = :role');
        $update->execute([
            'id' => $id,
            'status' => $status,
            'role' => 'user',
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật trạng thái khách hàng thành công',
        ]);
    }

    methodNotAllowed(['GET', 'PATCH']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý yêu cầu khách hàng',
        'error' => $exception->getMessage(),
    ], 500);
}
