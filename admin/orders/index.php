<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT * FROM orders WHERE 1=1";
if ($statusFilter !== '') {
    $sql .= " AND status = '" . $db->real_escape_string($statusFilter) . "'";
}
$sql .= " ORDER BY id DESC";
$orders = $db->query($sql);

$activePage = 'orders';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>🧾 Orders</h1></div>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <div style="margin-bottom:12px;">
    <a href="index.php" class="btn btn-sm <?= $statusFilter=='' ? '' : 'btn-secondary' ?>">All</a>
    <?php foreach (['Pending','Confirmed','Processing','Ready','Completed','Cancelled'] as $s): ?>
      <a href="index.php?status=<?= $s ?>" class="btn btn-sm <?= $statusFilter==$s ? '' : 'btn-secondary' ?>"><?= $s ?></a>
    <?php endforeach; ?>
  </div>
  <table class="admin-table">
    <tr><th>Invoice</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
    <?php while ($o = $orders->fetch_assoc()): ?>
    <tr>
      <td><?= e($o['invoice_number']) ?></td>
      <td><?= e($o['customer_name']) ?></td>
      <td><?= e($o['customer_phone']) ?></td>
      <td><?= money($o['grand_total']) ?></td>
      <td><span class="badge badge-active"><?= e($o['status']) ?></span></td>
      <td><?= date('d M, h:i A', strtotime($o['created_at'])) ?></td>
      <td><a href="view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
    </tr>
    <?php endwhile; ?>
    <?php if ($orders->num_rows === 0): ?>
      <tr><td colspan="7" style="text-align:center;color:#6b7280;">No orders found</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
