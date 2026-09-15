<?php
require_once __DIR__ . '/../includes/database.php';
header('Content-Type: application/json; charset=utf-8');

$db = getDB();
$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['success' => true, 'suggestions' => []]);
    exit;
}

$stmt = $db->prepare("SELECT id, name, sku FROM products WHERE status = 1 AND (name LIKE ? OR sku LIKE ?) ORDER BY name ASC LIMIT 10");
$like = "%$q%";
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) $out[] = $row;

echo json_encode(['success' => true, 'suggestions' => $out]);
