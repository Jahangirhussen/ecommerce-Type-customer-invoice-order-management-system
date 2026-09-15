<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="products_export_' . date('Ymd_His') . '.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Name', 'Code', 'Brand', 'Category', 'Quantity', 'Price', 'Status']);

$res = $db->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC");
while ($p = $res->fetch_assoc()) {
    fputcsv($out, [
        $p['name'],
        $p['sku'],
        $p['brand'],
        $p['category_name'],
        $p['stock'],
        $p['price'],
        $p['status'] ? 'Active' : 'Inactive',
    ]);
}
fclose($out);
