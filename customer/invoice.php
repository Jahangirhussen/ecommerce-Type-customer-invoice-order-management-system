<?php
require_once __DIR__ . '/../includes/order-functions.php';

$id = (int)($_GET['id'] ?? 0);
$order = getOrder($id);
if (!$order) redirect('products.php');
$items = getOrderItems($id);
$settings = getSettings();
$shopName = $settings['shop_name'] ?? 'My Shop';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?= e($order['invoice_number']) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<div class="topbar">
  <div class="inner">
    <h1><?= e($shopName) ?></h1>
  </div>
</div>

<div class="invoice-box" id="invoiceBox">
  <h2><?= e($shopName) ?></h2>
  <div class="invoice-sub">Customer Order Invoice</div>
  <div class="invoice-meta">
    <span><?= e($order['invoice_number']) ?></span>
    <span><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></span>
  </div>

  <table class="invoice-table">
    <tr><th>Product</th><th class="num">Qty</th><th class="num">Price</th><th class="num">Total</th></tr>
    <?php foreach ($items as $it): ?>
    <tr>
      <td><?= e($it['product_name']) ?></td>
      <td class="num"><?= (int)$it['quantity'] ?></td>
      <td class="num"><?= money($it['unit_price']) ?></td>
      <td class="num"><?= money($it['total_price']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <div class="invoice-total">
    <span>Total</span>
    <span><?= money($order['grand_total']) ?></span>
  </div>

  <div class="invoice-customer">
    <div><b>Customer:</b> <?= e($order['customer_name']) ?></div>
    <div><b>Phone:</b> <?= e($order['customer_phone']) ?></div>
    <?php if ($order['customer_address']): ?><div><b>Address:</b> <?= e($order['customer_address']) ?></div><?php endif; ?>
    <?php if ($order['customer_note']): ?><div><b>Note:</b> <?= e($order['customer_note']) ?></div><?php endif; ?>
    <div><b>Status:</b> <span class="status-badge status-<?= e($order['status']) ?>"><?= e($order['status']) ?></span></div>
  </div>

  <div class="invoice-actions">
    <button class="btn btn-whatsapp" id="waBtn" onclick="shareWhatsApp(<?= $order['id'] ?>)">🟢 Share on WhatsApp</button>
    <button class="btn btn-secondary" onclick="window.print()">🖨 Print</button>
    <a class="btn btn-secondary" href="products.php">+ New Order</a>
  </div>
</div>

<div class="toast" id="toast"></div>
<script src="../assets/js/app.js"></script>
<script src="../assets/js/invoice.js"></script>
</body>
</html>
