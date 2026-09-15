<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    deleteProduct($id);
    flash('success', 'Product deleted');
}
redirect('index.php');
