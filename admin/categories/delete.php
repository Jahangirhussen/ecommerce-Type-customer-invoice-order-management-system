<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('success', 'Category deleted');
}
redirect('index.php');
