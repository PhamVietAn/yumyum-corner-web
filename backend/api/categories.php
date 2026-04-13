<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

try {
    $pdo = getPdoConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $query = $pdo->query(
            'SELECT c.id, c.name, c.image_url, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             GROUP BY c.id, c.name, c.image_url
             ORDER BY c.id ASC'
        );

        $data = array_map(
            static fn(array $row): array => [
                'id' => (int) $row['id'],
                'name' => (string) $row['name'],
                'image' => (string) $row['image_url'],
                'productCount' => (int) $row['product_count'],
            ],
            $query->fetchAll()
        );

        jsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    if ($method === 'POST') {
        requireAdmin();
        $input = readJsonInput();
        $name = trim((string) ($input['name'] ?? ''));
        $image = trim((string) ($input['image'] ?? ''));

        if ($name === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Tên danh mục là bắt buộc',
            ], 422);
        }

        $exists = $pdo->prepare('SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1');
        $exists->execute(['name' => $name]);
        if ($exists->fetch()) {
            jsonResponse([
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại',
            ], 409);
        }

        $insert = $pdo->prepare('INSERT INTO categories (name, image_url) VALUES (:name, :image_url)');
        $insert->execute([
            'name' => $name,
            'image_url' => $image !== '' ? $image : '/src/assets/home/snacks_4689118.png',
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Thêm danh mục thành công',
            'data' => ['id' => (int) $pdo->lastInsertId()],
        ], 201);
    }

    if ($method === 'PUT') {
        requireAdmin();
        $input = readJsonInput();
        $id = (int) ($input['id'] ?? 0);
        $name = trim((string) ($input['name'] ?? ''));
        $image = trim((string) ($input['image'] ?? ''));

        if ($id <= 0 || $name === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Id và tên danh mục là bắt buộc',
            ], 422);
        }

        $exists = $pdo->prepare('SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) AND id <> :id LIMIT 1');
        $exists->execute(['name' => $name, 'id' => $id]);
        if ($exists->fetch()) {
            jsonResponse([
                'success' => false,
                'message' => 'Tên danh mục đã tồn tại',
            ], 409);
        }

        $update = $pdo->prepare('UPDATE categories SET name = :name, image_url = :image_url WHERE id = :id');
        $update->execute([
            'id' => $id,
            'name' => $name,
            'image_url' => $image !== '' ? $image : '/src/assets/home/snacks_4689118.png',
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công',
        ]);
    }

    if ($method === 'DELETE') {
        requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu id danh mục',
            ], 422);
        }

        $countStatement = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $countStatement->execute(['id' => $id]);
        $productCount = (int) $countStatement->fetchColumn();

        if ($productCount > 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Danh mục đang có sản phẩm, không thể xóa',
            ], 409);
        }

        $delete = $pdo->prepare('DELETE FROM categories WHERE id = :id');
        $delete->execute(['id' => $id]);

        jsonResponse([
            'success' => true,
            'message' => 'Xóa danh mục thành công',
        ]);
    }

    methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý yêu cầu danh mục',
        'error' => $exception->getMessage(),
    ], 500);
}
