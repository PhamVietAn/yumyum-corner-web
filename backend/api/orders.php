<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

function getOrCreateCartIdForOrder(PDO $pdo, int $userId): int
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

function fetchOrders(PDO $pdo, bool $isAdminScope, string $email): array
{
    if ($isAdminScope) {
        $ordersStmt = $pdo->query('SELECT * FROM orders ORDER BY order_date DESC, id DESC');
    } else {
        $ordersStmt = $pdo->prepare('SELECT * FROM orders WHERE customer_email = :email ORDER BY order_date DESC, id DESC');
        $ordersStmt->execute(['email' => $email]);
    }

    $orders = $ordersStmt->fetchAll();
    if (!$orders) {
        return [];
    }

    $orderIds = array_map(static fn(array $row): int => (int) $row['id'], $orders);
    $placeholder = implode(',', array_fill(0, count($orderIds), '?'));

    $itemSql = 'SELECT oi.order_id, oi.product_id, oi.quantity, oi.unit_price,
                       p.name AS product_name, p.image_url, c.name AS category_name
                FROM order_items oi
                LEFT JOIN products p ON p.id = oi.product_id
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE oi.order_id IN (' . $placeholder . ')
                ORDER BY oi.id ASC';

    $itemStmt = $pdo->prepare($itemSql);
    $itemStmt->execute($orderIds);
    $itemRows = $itemStmt->fetchAll();

    $itemsByOrder = [];
    foreach ($itemRows as $item) {
        $orderId = (int) $item['order_id'];
        if (!isset($itemsByOrder[$orderId])) {
            $itemsByOrder[$orderId] = [];
        }

        $itemsByOrder[$orderId][] = [
            'quantity' => (int) $item['quantity'],
            'qty' => (int) $item['quantity'],
            'product' => [
                'id' => (int) ($item['product_id'] ?? 0),
                'name' => (string) ($item['product_name'] ?? 'Sản phẩm'),
                'image' => (string) ($item['image_url'] ?? 'https://via.placeholder.com/80'),
                'price' => (int) ($item['unit_price'] ?? 0),
                'category' => (string) ($item['category_name'] ?? 'Khác'),
            ],
        ];
    }

    return array_map(
        static function (array $order) use ($itemsByOrder): array {
            $orderId = (int) $order['id'];
            return [
                'id' => (string) $orderId,
                'orderNumber' => (string) ($order['order_number'] ?? ''),
                'customerName' => (string) ($order['customer_name'] ?? ''),
                'customerEmail' => (string) ($order['customer_email'] ?? ''),
                'paymentMethod' => (string) ($order['payment_method'] ?? 'COD'),
                'shippingAddress' => (string) ($order['shipping_address'] ?? ''),
                'status' => normalizeOrderStatus((string) ($order['status'] ?? 'pending')),
                'orderDate' => (string) ($order['order_date'] ?? date('Y-m-d H:i:s')),
                'shippingFee' => (int) ($order['shipping_fee'] ?? 0),
                'subtotal' => (int) ($order['subtotal'] ?? 0),
                'totalAmount' => (int) ($order['total_amount'] ?? 0),
                'items' => $itemsByOrder[$orderId] ?? [],
            ];
        },
        $orders
    );
}

try {
    $pdo = getPdoConnection();
    $authUser = requireAuth();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $scope = strtolower((string) ($_GET['scope'] ?? 'mine'));
        $isAdminScope = $scope === 'all';

        if ($isAdminScope && ($authUser['role'] ?? 'user') !== 'admin') {
            jsonResponse([
                'success' => false,
                'message' => 'Bạn không có quyền xem toàn bộ đơn hàng',
            ], 403);
        }

        $data = fetchOrders($pdo, $isAdminScope, strtolower((string) $authUser['email']));

        jsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    if ($method === 'POST') {
        $input = readJsonInput();

        $shipping = is_array($input['shipping'] ?? null) ? $input['shipping'] : [];
        $fullName = trim((string) ($shipping['fullName'] ?? $authUser['fullName']));
        $phone = trim((string) ($shipping['phone'] ?? ''));
        $email = strtolower(trim((string) ($shipping['email'] ?? $authUser['email'])));
        $address = trim((string) ($shipping['address'] ?? ''));
        $district = trim((string) ($shipping['district'] ?? ''));
        $city = trim((string) ($shipping['city'] ?? ''));
        $paymentMethod = trim((string) ($input['paymentMethod'] ?? 'COD'));
        $shippingFee = max(0, (int) ($input['shippingFee'] ?? 25000));

        if ($fullName === '' || $email === '' || $address === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Thông tin giao hàng chưa đầy đủ',
            ], 422);
        }

        $cartId = getOrCreateCartIdForOrder($pdo, (int) $authUser['id']);
        $cartItemsStmt = $pdo->prepare(
            'SELECT ci.product_id, ci.quantity, p.name, p.price, p.stock, p.image_url, c.name AS category_name
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE ci.cart_id = :cart_id'
        );
        $cartItemsStmt->execute(['cart_id' => $cartId]);
        $cartItems = $cartItemsStmt->fetchAll();

        if (!$cartItems) {
            jsonResponse([
                'success' => false,
                'message' => 'Giỏ hàng đang trống',
            ], 422);
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $qty = (int) $item['quantity'];
            $stock = (int) $item['stock'];
            if ($qty > $stock) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Sản phẩm "' . $item['name'] . '" không đủ tồn kho',
                ], 422);
            }
            $subtotal += ((int) $item['price']) * $qty;
        }

        $shippingAddress = implode(', ', array_filter([$address, $district, $city]));
        $totalAmount = $subtotal + $shippingFee;

        $pdo->beginTransaction();

        $tempOrderNumber = 'TMP-' . bin2hex(random_bytes(6));
        $insertOrder = $pdo->prepare(
            'INSERT INTO orders
             (order_number, customer_name, customer_email, payment_method, shipping_address, status, order_date, shipping_fee, subtotal, total_amount)
             VALUES
             (:order_number, :customer_name, :customer_email, :payment_method, :shipping_address, :status, NOW(), :shipping_fee, :subtotal, :total_amount)'
        );
        $insertOrder->execute([
            'order_number' => $tempOrderNumber,
            'customer_name' => $fullName,
            'customer_email' => $email,
            'payment_method' => $paymentMethod,
            'shipping_address' => $shippingAddress,
            'status' => 'pending',
            'shipping_fee' => $shippingFee,
            'subtotal' => $subtotal,
            'total_amount' => $totalAmount,
        ]);

        $orderId = (int) $pdo->lastInsertId();
        $orderNumber = sprintf('ORD-%s-%03d', date('Y'), $orderId);

        $updateOrderNumber = $pdo->prepare('UPDATE orders SET order_number = :order_number WHERE id = :id');
        $updateOrderNumber->execute([
            'id' => $orderId,
            'order_number' => $orderNumber,
        ]);

        $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (:order_id, :product_id, :quantity, :unit_price)');
        $updateStock = $pdo->prepare('UPDATE products SET stock = GREATEST(stock - :quantity, 0) WHERE id = :product_id');

        foreach ($cartItems as $item) {
            $insertItem->execute([
                'order_id' => $orderId,
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => (int) $item['price'],
            ]);

            $updateStock->execute([
                'product_id' => (int) $item['product_id'],
                'quantity' => (int) $item['quantity'],
            ]);
        }

        $updateUser = $pdo->prepare(
            'UPDATE users
             SET full_name = :full_name,
                 phone = :phone,
                 address_detail = :address_detail,
                 address_district = :address_district,
                 address_city = :address_city
             WHERE id = :id'
        );
        $updateUser->execute([
            'id' => (int) $authUser['id'],
            'full_name' => $fullName,
            'phone' => $phone,
            'address_detail' => $address,
            'address_district' => $district,
            'address_city' => $city,
        ]);

        $clearCart = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = :cart_id');
        $clearCart->execute(['cart_id' => $cartId]);

        $pdo->commit();

        $orderData = fetchOrders($pdo, false, $email);
        $created = array_values(array_filter($orderData, static fn(array $o): bool => (string) $o['id'] === (string) $orderId));

        jsonResponse([
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'data' => $created[0] ?? null,
        ], 201);
    }

    if ($method === 'PATCH') {
        $input = readJsonInput();
        $orderId = (int) ($input['orderId'] ?? $input['id'] ?? 0);
        $status = normalizeOrderStatus((string) ($input['status'] ?? 'pending'));

        if ($orderId <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu orderId',
            ], 422);
        }

        $find = $pdo->prepare('SELECT id, customer_email, status FROM orders WHERE id = :id LIMIT 1');
        $find->execute(['id' => $orderId]);
        $order = $find->fetch();

        if (!$order) {
            jsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng',
            ], 404);
        }

        $isAdmin = ($authUser['role'] ?? 'user') === 'admin';
        $isOwner = strtolower((string) $order['customer_email']) === strtolower((string) $authUser['email']);

        if (!$isAdmin && !$isOwner) {
            jsonResponse([
                'success' => false,
                'message' => 'Bạn không có quyền thao tác đơn hàng này',
            ], 403);
        }

        if (!$isAdmin && $status !== 'cancelled') {
            jsonResponse([
                'success' => false,
                'message' => 'Bạn chỉ có thể hủy đơn hàng',
            ], 403);
        }

        $currentStatus = normalizeOrderStatus((string) $order['status']);
        if (!$isAdmin && $currentStatus !== 'pending') {
            jsonResponse([
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn đang chờ xử lý',
            ], 422);
        }

        $update = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $update->execute([
            'id' => $orderId,
            'status' => $status,
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công',
        ]);
    }

    methodNotAllowed(['GET', 'POST', 'PATCH']);
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý đơn hàng',
        'error' => $exception->getMessage(),
    ], 500);
}
