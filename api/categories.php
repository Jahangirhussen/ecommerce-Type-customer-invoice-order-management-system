<?php
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');

echo json_encode(['success' => true, 'categories' => getCategories(true)]);
