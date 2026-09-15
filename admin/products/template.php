<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="product_import_template.csv"');

$out = fopen('php://output', 'w');
fputcsv($out, ['Name', 'Code', 'Brand', 'Category', 'Quantity', 'Price']);
fputcsv($out, ['Actifit Premium Maida 1kg', 'GRC-0001', 'Actifit', 'Grocery & Staples', 12, 80]);
fputcsv($out, ['Activo Soap Bar Germ Shield 25gm', 'GRC-0002', 'Activo', 'Personal Care', 10, 10]);
fclose($out);
