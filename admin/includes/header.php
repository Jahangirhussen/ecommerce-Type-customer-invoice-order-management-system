<?php
// Expects $activePage to be set by the including file
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - <?= e(getSettings()['shop_name'] ?? 'Shop') ?></title>
<link rel="stylesheet" href="<?= $adminAssetPath ?? '../assets/css/admin.css' ?>">
</head>
<body>
<div class="admin-wrap">
  <?php require __DIR__ . '/sidebar.php'; ?>
  <div class="main">
