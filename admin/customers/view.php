<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$phone = trim($_GET['phone'] ?? '');
if ($phone === '') redirect('index.php');

$stmt = $db->prepare("SELECT * FROM customers WHERE phone=?");
$stmt->bind_param('s', $phone);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

$stmt = $db->prepare("SELECT * FROM orders WHERE customer_phone=? ORDER BY id DESC");
$stmt->bind_param('s', $phone);
$stmt->execute();
$orders = $stmt->get_result();

$stmt = $db->prepare("SELECT COUNT(*) total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) completed, SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) pending, COALESCE(SUM(CASE WHEN status!='Cancelled' THEN grand_total ELSE 0 END),0) sales FROM orders WHERE customer_phone=?");
$stmt->bind_param('s', $phone);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$displayName = $customer['name'] ?? ($orders->num_rows ? '' : $phone);

$activePage = 'customers';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>👥 Customer — <?= e($phone) ?></h1></div>

<div class="card">
  <p><strong>Name:</strong> <?= e($customer['name'] ?? '-') ?></p>
  <p><strong>Phone:</strong> <?= e($phone) ?></p>
  <p><strong>Location:</strong> <?= e($customer['location'] ?? '-') ?></p>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= (int)$stats['total'] ?></div><div class="label">Total Orders</div></div>
  <div class="stat-card"><div class="num"><?= (int)$stats['completed'] ?></div><div class="label">Completed</div></div>
  <div class="stat-card"><div class="num"><?= (int)$stats['pending'] ?></div><div class="label">Pending</div></div>
  <div class="stat-card"><div class="num"><?= money($stats['sales']) ?></div><div class="label">Total Sales</div></div>
</div>

<div class="card">
  <table class="admin-table">
    <tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
    <?php while ($o = $orders->fetch_assoc()): ?>
    <tr>
      <td><?= e($o['invoice_number']) ?></td>
      <td><?= e($o['customer_name']) ?></td>
      <td><?= money($o['grand_total']) ?></td>
      <td><span class="badge badge-active"><?= e($o['status']) ?></span></td>
      <td><?= date('d M, h:i A', strtotime($o['created_at'])) ?></td>
      <td><a href="../orders/view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<a href="index.php" class="btn btn-secondary">← Back to Customers</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
