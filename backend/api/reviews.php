<?php

declare(strict_types=1);

require_once __DIR__ . '/_common.php';

try {
    $pdo = getPdoConnection();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $productId = isset($_GET['productId']) ? (int) $_GET['productId'] : 0;

        if ($productId <= 0) {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu productId',
            ], 422);
        }

        $statement = $pdo->prepare(
            'SELECT id, reviewer_name, rating, content, COALESCE(review_date, DATE(created_at)) AS review_date
             FROM product_reviews
             WHERE product_id = :product_id
             ORDER BY created_at DESC, id DESC'
        );
        $statement->execute(['product_id' => $productId]);

        $data = array_map(
            static fn(array $row): array => [
                'id' => (int) $row['id'],
                'name' => (string) ($row['reviewer_name'] ?? 'Khách hàng'),
                'rating' => (int) ($row['rating'] ?? 5),
                'content' => (string) ($row['content'] ?? ''),
                'date' => (string) ($row['review_date'] ?? date('Y-m-d')),
            ],
            $statement->fetchAll()
        );

        jsonResponse([
            'success' => true,
            'data' => $data,
        ]);
    }

    if ($method === 'POST') {
        $input = readJsonInput();

        $productId = (int) ($input['productId'] ?? 0);
        $reviewerName = trim((string) ($input['name'] ?? $input['reviewerName'] ?? ''));
        $content = trim((string) ($input['content'] ?? ''));
        $rating = (int) ($input['rating'] ?? 5);

        if ($productId <= 0 || $reviewerName === '' || $content === '') {
            jsonResponse([
                'success' => false,
                'message' => 'Thiếu thông tin đánh giá',
            ], 422);
        }

        $rating = max(1, min(5, $rating));

        $insert = $pdo->prepare(
            'INSERT INTO product_reviews (product_id, reviewer_name, rating, content, review_date)
             VALUES (:product_id, :reviewer_name, :rating, :content, CURDATE())'
        );
        $insert->execute([
            'product_id' => $productId,
            'reviewer_name' => $reviewerName,
            'rating' => $rating,
            'content' => $content,
        ]);

        $agg = $pdo->prepare('SELECT ROUND(AVG(rating), 1) AS rating, COUNT(*) AS review_count FROM product_reviews WHERE product_id = :product_id');
        $agg->execute(['product_id' => $productId]);
        $summary = $agg->fetch() ?: ['rating' => 5, 'review_count' => 1];

        $updateProduct = $pdo->prepare('UPDATE products SET rating = :rating, review_count = :review_count WHERE id = :id');
        $updateProduct->execute([
            'id' => $productId,
            'rating' => (float) $summary['rating'],
            'review_count' => (int) $summary['review_count'],
        ]);

        jsonResponse([
            'success' => true,
            'message' => 'Gửi đánh giá thành công',
        ], 201);
    }

    methodNotAllowed(['GET', 'POST']);
} catch (Throwable $exception) {
    jsonResponse([
        'success' => false,
        'message' => 'Không thể xử lý đánh giá',
        'error' => $exception->getMessage(),
    ], 500);
}
