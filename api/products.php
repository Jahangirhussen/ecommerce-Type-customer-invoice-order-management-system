<?php
require_once __DIR__ . '/../includes/product-functions.php';
header('Content-Type: application/json; charset=utf-8');

$search = trim($_GET['q'] ?? '');
$categoryId = (int)($_GET['category_id'] ?? 0);
$products = searchProducts($search, $categoryId);
foreach ($products as &$p) {
    $p['image_url'] = $p['image'] ? '../' . UPLOAD_URL . $p['image'] : null;
}
echo json_encode(['success' => true, 'products' => $products]);
