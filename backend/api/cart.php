<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

function getOrCreateCartId(PDO $pdo, int $userId): int
{
    $select = $pdo->prepare('SELECT id FROM carts WHERE user_id = :user_id LIMIT 1');
    $select->execute(['user_id' => $userId]);
    $row = $select->fetch();
    if ($row) {
        return (int) $row['id'];
    }

    $insert = $pdo->prepare('INSERT INTO carts (user_id) VALUES (:user_id)');
    $insert->execute(['user_id' => $userId]);

    return (int) $pdo->lastInsertId();
}

function fetchCartItems(PDO $pdo, int $userId): array
{
    $sql = 'SELECT ci.id AS item_id, ci.product_id, ci.quantity,
                   p.name, p.price, p.image_url, p.stock,
                   c.name AS category_name
            FROM carts cart
            JOIN cart_items ci ON ci.cart_id = cart.id
            JOIN products p ON p.id = ci.product_id
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE cart.user_id = :user_id
            ORDER BY ci.id DESC';

    $statement = $pdo->prepare($sql);
    $statement->execute(['user_id' => $userId]);

    return array_map(
        static fn(array $row): array => [
            'itemId' => (int) $row['item_id'],
            'id' => (int) $row['product_id'],
            'productId' => (int) $row['product_id'],
            'name' => (string) $row['name'],
            'price' => (int) $row['price'],
            'img' => (string) $row['image_url'],
            'category' => (string) ($row['category_name'] ?? ''),
            'quantity' => (int) $row['quantity'],
            'qty' => (int) $row['quantity'],
            'stock' => (int) $row['stock'],
        ],
        $statement->fetchAll()
    );
}

try {
    $pdo = getPdoConnection();
    $authUser = requireAuth();
    $userId = (int) $authUser['id'];
    $cartId = getOrCreateCartId($pdo, $userId);
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        jsonResponse([
            'success' => true,
            'data' => fetchCartItems($pdo, $userId),
        ]);
    }

    if ($method === 'POST') {
        $input = readJsonInput();
        $productId = (int) ($input['productId'] ?? 0);
        $quantity = parsePositiveInt($input['quantity'] ?? 1, 1);

        if ($productId <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu productId',
            ], 422);
        }

        $productStatement = $pdo->prepare('SELECT id, stock FROM products WHERE id = :id LIMIT 1');
        $productStatement->execute(['id' => $productId]);
        $product = $productStatement->fetch();

        if (!$product) {
            jsonResponse([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        }

        $currentStock = (int) $product['stock'];

        $existing = $pdo->prepare('SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id LIMIT 1');
        $existing->execute([
            'cart_id' => $cartId,
            'product_id' => $productId,
        ]);
        $item = $existing->fetch();

        if ($item) {
            $nextQty = (int) $item['quantity'] + $quantity;
            if ($nextQty > $currentStock) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho',
                ], 422);
            }

            $update = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
            $update->execute([
                'id' => (int) $item['id'],
                'quantity' => $nextQty,
            ]);
        } else {
            if ($quantity > $currentStock) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho',
                ], 422);
            }

            $insert = $pdo->prepare('INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)');
            $insert->execute([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }

        jsonResponse([
            'success' => true,
            'message' => 'Đã cập nhật giỏ hàng',
            'data' => fetchCartItems($pdo, $userId),
        ]);
    }

    if ($method === 'PATCH') {
        $input = readJsonInput();
        $itemId = (int) ($input['itemId'] ?? 0);
        $productId = (int) ($input['productId'] ?? 0);
        $quantity = (int) ($input['quantity'] ?? 1);

        if ($itemId <= 0 && $productId <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu itemId hoặc productId',
            ], 422);
        }

        $whereSql = $itemId > 0 ? 'ci.id = :item_id' : 'ci.product_id = :product_id';
        $params = ['cart_id' => $cartId];
        if ($itemId > 0) {
            $params['item_id'] = $itemId;
        } else {
            $params['product_id'] = $productId;
        }

        $statement = $pdo->prepare(
            'SELECT ci.id, ci.product_id, p.stock
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = :cart_id AND ' . $whereSql . '
             LIMIT 1'
        );
        $statement->execute($params);
        $target = $statement->fetch();

        if (!$target) {
            jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm trong giỏ',
            ], 404);
        }

        if ($quantity <= 0) {
            $delete = $pdo->prepare('DELETE FROM cart_items WHERE id = :id');
            $delete->execute(['id' => (int) $target['id']]);
        } else {
            if ($quantity > (int) $target['stock']) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho',
                ], 422);
            }

            $update = $pdo->prepare('UPDATE cart_items SET quantity = :quantity WHERE id = :id');
            $update->execute([
                'id' => (int) $target['id'],
                'quantity' => $quantity,
            ]);
        }

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật giỏ hàng thành công',
            'data' => fetchCartItems($pdo, $userId),
        ]);
    }

    if ($method === 'DELETE') {
        $itemId = isset($_GET['itemId']) ? (int) $_GET['itemId'] : 0;
        $productId = isset($_GET['productId']) ? (int) $_GET['productId'] : 0;
        $clear = isset($_GET['clear']) && $_GET['clear'] === '1';

        if ($clear) {
            $deleteAll = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id');
            $deleteAll->execute(['cart_id' => $cartId]);
        } elseif ($itemId > 0) {
            $delete = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id AND id = :id');
            $delete->execute(['cart_id' => $cartId, 'id' => $itemId]);
        } elseif ($productId > 0) {
            $delete = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id');
            $delete->execute(['cart_id' => $cartId, 'product_id' => $productId]);
        } else {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu tham số xóa',
            ], 422);
        }

        jsonResponse([
            'success' => true,
            'message' => 'Xóa sản phẩm khỏi giỏ thành công',
            'data' => fetchCartItems($pdo, $userId),
        ]);
    }

    methodNotAllowed(['GET', 'POST', 'PATCH', 'DELETE']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý giỏ hàng',
        'error' => $exception->getMessage(),
    ], 500);
}
