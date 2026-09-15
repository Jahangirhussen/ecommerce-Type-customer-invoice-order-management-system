<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/order-functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
$order = getOrder($id);
if (!$order) redirect('index.php');
$items = getOrderItems($id);

$activePage = 'orders';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>🧾 Order Detail</h1></div>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
    <div>
      <h3 style="margin:0 0 4px;"><?= e($order['invoice_number']) ?></h3>
      <div style="color:#6b7280;font-size:13px;"><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></div>
    </div>
    <form method="post" action="update-status.php">
      <input type="hidden" name="id" value="<?= $order['id'] ?>">
      <select name="status" class="status-select" onchange="this.form.submit()">
        <?php foreach (['Pending','Confirmed','Processing','Ready','Completed','Cancelled'] as $s): ?>
          <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <div style="margin-top:16px;background:#f8f9fa;padding:12px;border-radius:8px;font-size:13.5px;">
    <strong>Customer:</strong> <?= e($order['customer_name']) ?><br>
    <strong>Phone:</strong> <?= e($order['customer_phone']) ?><br>
    <?php if ($order['customer_address']): ?><strong>Address:</strong> <?= e($order['customer_address']) ?><br><?php endif; ?>
    <?php if ($order['customer_note']): ?><strong>Note:</strong> <?= e($order['customer_note']) ?><?php endif; ?>
  </div>

  <table class="admin-table" style="margin-top:16px;">
    <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
    <?php foreach ($items as $it): ?>
    <tr>
      <td><?= e($it['product_name']) ?></td>
      <td><?= (int)$it['quantity'] ?></td>
      <td><?= money($it['unit_price']) ?></td>
      <td><?= money($it['total_price']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <div style="text-align:right;font-size:17px;font-weight:800;margin-top:10px;">
    Grand Total: <?= money($order['grand_total']) ?>
  </div>
</div>
<a href="index.php" class="btn btn-secondary">← Back to Orders</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
