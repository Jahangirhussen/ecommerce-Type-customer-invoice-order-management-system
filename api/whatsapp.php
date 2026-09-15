<?php
require_once __DIR__ . '/../includes/order-functions.php';
require_once __DIR__ . '/../includes/whatsapp-functions.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_GET['id'] ?? 0);
$order = getOrder($id);
if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}
$items = getOrderItems($id);

echo json_encode([
    'success' => true,
    'link' => buildWhatsAppLink($order, $items),
    'message' => buildWhatsAppMessage($order, $items),
]);
