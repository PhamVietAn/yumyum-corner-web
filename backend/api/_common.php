<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOriginsRaw = (string) envValue('CORS_ALLOWED_ORIGINS', '');
$allowedOrigins = array_filter(array_map('trim', explode(',', $allowedOriginsRaw)));

if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Accept');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

const ORDER_STATUS_VALUES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

function jsonResponse(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function readJsonInput(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }

    return $decoded;
}

function currentUser(): ?array
{
    $user = $_SESSION['auth_user'] ?? null;
    if (!is_array($user)) {
        return null;
    }

    return [
        'id' => (int) ($user['id'] ?? 0),
        'fullName' => (string) ($user['fullName'] ?? ''),
        'email' => (string) ($user['email'] ?? ''),
        'role' => (string) ($user['role'] ?? 'user'),
    ];
}

function requireAuth(): array
{
    $user = currentUser();
    if ($user === null || (int) $user['id'] <= 0) {
        jsonResponse([
            'success' => false,
            'message' => 'Bạn chưa đăng nhập',
        ], 401);
    }

    return $user;
}

function requireAdmin(): array
{
    $user = requireAuth();
    if (($user['role'] ?? 'user') !== 'admin') {
        jsonResponse([
            'success' => false,
            'message' => 'Bạn không có quyền thực hiện thao tác này',
        ], 403);
    }

    return $user;
}

function methodNotAllowed(array $allowedMethods): never
{
    jsonResponse([
        'success' => false,
        'message' => 'Method not allowed',
        'allowed' => $allowedMethods,
    ], 405);
}

function mapUserRow(array $row): array
{
    return [
        'id' => (int) $row['id'],
        'fullName' => (string) ($row['full_name'] ?? ''),
        'email' => (string) ($row['email'] ?? ''),
        'phone' => (string) ($row['phone'] ?? ''),
        'address' => [
            'detail' => (string) ($row['address_detail'] ?? ''),
            'district' => (string) ($row['address_district'] ?? ''),
            'city' => (string) ($row['address_city'] ?? ''),
        ],
        'role' => (string) ($row['role'] ?? 'user'),
        'status' => (string) ($row['status'] ?? 'active'),
        'joinedAt' => isset($row['created_at']) ? substr((string) $row['created_at'], 0, 10) : date('Y-m-d'),
    ];
}

function normalizeOrderStatus(string $status): string
{
    $normalized = strtolower(trim($status));
    if (!in_array($normalized, ORDER_STATUS_VALUES, true)) {
        return 'pending';
    }

    return $normalized;
}

function parsePositiveInt(mixed $value, int $fallback = 1): int
{
    $number = (int) $value;
    if ($number <= 0) {
        return $fallback;
    }

    return $number;
}
