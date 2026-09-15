<?php
require_once __DIR__ . '/../includes/order-functions.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$cart = $input['cart'] ?? [];
$customerName = trim($input['customer_name'] ?? '');
$customerPhone = trim($input['customer_phone'] ?? '');
$customerAddress = trim($input['customer_address'] ?? '');
$customerNote = trim($input['customer_note'] ?? '');

$result = createOrder($cart, $customerName, $customerPhone, $customerAddress, $customerNote);
echo json_encode($result);
