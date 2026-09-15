<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/order-functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    if (updateOrderStatus($id, $status)) {
        flash('success', 'Order status updated');
    }
    redirect('view.php?id=' . $id);
}
redirect('index.php');
