<?php
require_once __DIR__ . '/../includes/product-functions.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);
$qty = (int)($input['qty'] ?? 1);

$product = getProduct($id);
if (!$product || !$product['status']) {
    echo json_encode(['success' => false, 'message' => 'Product not available']);
    exit;
}
if ($qty > $product['stock']) {
    echo json_encode(['success' => false, 'message' => "Only {$product['stock']} available", 'stock' => (int)$product['stock']]);
    exit;
}

echo json_encode([
    'success' => true,
    'product' => [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => (float)$product['price'],
        'stock' => (int)$product['stock'],
    ],
]);
