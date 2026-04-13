<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

function mapProductRow(array $row): array
{
    $stock = (int) ($row['stock'] ?? 0);

    return [
        'id' => (int) $row['id'],
        'categoryId' => (int) ($row['category_id'] ?? 0),
        'category' => (string) ($row['category_name'] ?? ''),
        'name' => (string) ($row['name'] ?? ''),
        'price' => (int) ($row['price'] ?? 0),
        'originalPrice' => isset($row['original_price']) ? (int) $row['original_price'] : null,
        'image' => (string) ($row['image_url'] ?? ''),
        'description' => (string) ($row['description'] ?? ''),
        'rating' => (float) ($row['rating'] ?? 5),
        'reviewCount' => (int) ($row['review_count'] ?? 0),
        'stock' => $stock,
        'inStock' => $stock > 0,
    ];
}

function findCategoryId(PDO $pdo, mixed $categoryIdInput, string $categoryNameInput): int
{
    $categoryId = (int) $categoryIdInput;
    if ($categoryId > 0) {
        $exists = $pdo->prepare('SELECT id FROM categories WHERE id = :id LIMIT 1');
        $exists->execute(['id' => $categoryId]);
        $row = $exists->fetch();
        if ($row) {
            return (int) $row['id'];
        }
    }

    $categoryName = trim($categoryNameInput);
    if ($categoryName === '') {
        return 1;
    }

    $select = $pdo->prepare('SELECT id FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1');
    $select->execute(['name' => $categoryName]);
    $found = $select->fetch();
    if ($found) {
        return (int) $found['id'];
    }

    $insert = $pdo->prepare('INSERT INTO categories (name, image_url) VALUES (:name, :image_url)');
    $insert->execute([
        'name' => $categoryName,
        'image_url' => '/src/assets/home/snacks_4689118.png',
    ]);

    return (int) $pdo->lastInsertId();
}

try {
    $pdo = getPdoConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $sql = 'SELECT p.id, p.category_id, c.name AS category_name, p.name, p.price, p.original_price,
                       p.image_url, p.description, p.rating, p.review_count, p.stock
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id';

        if ($id > 0) {
            $statement = $pdo->prepare($sql . ' WHERE p.id = :id LIMIT 1');
            $statement->execute(['id' => $id]);
            $product = $statement->fetch();

            if (!$product) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Không tìm thấy sản phẩm',
                ], 404);
            }

            jsonResponse([
                'success' => true,
                'data' => mapProductRow($product),
            ]);
        }

        $statement = $pdo->query($sql . ' ORDER BY p.id DESC');
        $products = array_map('mapProductRow', $statement->fetchAll());

        jsonResponse([
            'success' => true,
            'data' => $products,
        ]);
    }

    if ($method === 'POST') {
        requireAdmin();
        $input = readJsonInput();

        $name = trim((string) ($input['name'] ?? ''));
        $price = (int) ($input['price'] ?? 0);

        if ($name === '' || $price <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Tên sản phẩm và giá là bắt buộc',
            ], 422);
        }

        $categoryId = findCategoryId($pdo, $input['categoryId'] ?? null, (string) ($input['category'] ?? ''));
        $image = trim((string) ($input['image'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $originalPrice = isset($input['originalPrice']) ? (int) $input['originalPrice'] : null;
        $rating = (float) ($input['rating'] ?? 5);
        $reviewCount = (int) ($input['reviewCount'] ?? 0);
        $inStock = filter_var($input['inStock'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $stock = isset($input['stock']) ? (int) $input['stock'] : (($inStock === false) ? 0 : 100);

        $insert = $pdo->prepare(
            'INSERT INTO products
             (category_id, name, price, original_price, image_url, description, rating, review_count, stock)
             VALUES
             (:category_id, :name, :price, :original_price, :image_url, :description, :rating, :review_count, :stock)'
        );

        $insert->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'price' => $price,
            'original_price' => $originalPrice,
            'image_url' => $image !== '' ? $image : 'https://via.placeholder.com/120',
            'description' => $description,
            'rating' => max(1, min(5, $rating)),
            'review_count' => max(0, $reviewCount),
            'stock' => max(0, $stock),
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Tạo sản phẩm thành công',
            'data' => [
                'id' => (int) $pdo->lastInsertId(),
            ],
        ], 201);
    }

    if ($method === 'PUT') {
        requireAdmin();
        $input = readJsonInput();
        $id = (int) ($input['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu id sản phẩm',
            ], 422);
        }

        $name = trim((string) ($input['name'] ?? ''));
        $price = (int) ($input['price'] ?? 0);

        if ($name === '' || $price <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Tên sản phẩm và giá là bắt buộc',
            ], 422);
        }

        $categoryId = findCategoryId($pdo, $input['categoryId'] ?? null, (string) ($input['category'] ?? ''));
        $image = trim((string) ($input['image'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $originalPrice = isset($input['originalPrice']) ? (int) $input['originalPrice'] : null;
        $rating = (float) ($input['rating'] ?? 5);
        $reviewCount = (int) ($input['reviewCount'] ?? 0);
        $inStock = filter_var($input['inStock'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        $stock = isset($input['stock']) ? (int) $input['stock'] : (($inStock === false) ? 0 : 100);

        $update = $pdo->prepare(
            'UPDATE products
             SET category_id = :category_id,
                 name = :name,
                 price = :price,
                 original_price = :original_price,
                 image_url = :image_url,
                 description = :description,
                 rating = :rating,
                 review_count = :review_count,
                 stock = :stock
             WHERE id = :id'
        );

        $update->execute([
            'id' => $id,
            'category_id' => $categoryId,
            'name' => $name,
            'price' => $price,
            'original_price' => $originalPrice,
            'image_url' => $image !== '' ? $image : 'https://via.placeholder.com/120',
            'description' => $description,
            'rating' => max(1, min(5, $rating)),
            'review_count' => max(0, $reviewCount),
            'stock' => max(0, $stock),
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công',
        ]);
    }

    if ($method === 'DELETE') {
        requireAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu id sản phẩm',
            ], 422);
        }

        $delete = $pdo->prepare('DELETE FROM products WHERE id = :id');
        $delete->execute(['id' => $id]);

        jsonResponse([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công',
        ]);
    }

    methodNotAllowed(['GET', 'POST', 'PUT', 'DELETE']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý yêu cầu sản phẩm',
        'error' => $exception->getMessage(),
    ], 500);
}
