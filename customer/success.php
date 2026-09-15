<?php
require_once __DIR__ . '/../includes/functions.php';
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('products.php');
redirect('invoice.php?id=' . $id);
