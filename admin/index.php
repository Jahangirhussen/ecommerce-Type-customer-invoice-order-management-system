<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
$db = getDB();

$range = $_GET['range'] ?? '7day';
$dateSql = '';
switch ($range) {
    case 'today': $dateSql = "AND DATE(created_at) = CURDATE()"; break;
    case '7day': $dateSql = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"; break;
    case 'month': $dateSql = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"; break;
    case 'year': $dateSql = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)"; break;
    default: $dateSql = ''; $range = 'all'; break;
}

// order-based metrics (respect date range)
$totalOrders = $db->query("SELECT COUNT(*) c FROM orders WHERE 1=1 $dateSql")->fetch_assoc()['c'];
$totalDelivered = $db->query("SELECT COUNT(*) c FROM orders WHERE status='Completed' $dateSql")->fetch_assoc()['c'];
$totalOngoing = $db->query("SELECT COUNT(*) c FROM orders WHERE status IN ('Pending','Confirmed','Processing','Ready') $dateSql")->fetch_assoc()['c'];
$totalComplete = $totalDelivered;
$totalSales = $db->query("SELECT COALESCE(SUM(grand_total),0) s FROM orders WHERE status != 'Cancelled' $dateSql")->fetch_assoc()['s'];
$totalCustomers = $db->query("SELECT COUNT(DISTINCT customer_phone) c FROM orders WHERE 1=1 $dateSql")->fetch_assoc()['c'];

// inventory-wide metrics (not date-filtered)
$totalStockProducts = $db->query("SELECT COUNT(*) c FROM products WHERE stock > 0 AND status = 1")->fetch_assoc()['c'];
$totalStockCount = $db->query("SELECT COALESCE(SUM(stock),0) s FROM products")->fetch_assoc()['s'];
$totalStockCategories = $db->query("SELECT COUNT(*) c FROM categories")->fetch_assoc()['c'];
$totalBrands = $db->query("SELECT COUNT(DISTINCT brand) c FROM products WHERE brand IS NOT NULL AND brand != ''")->fetch_assoc()['c'];
$lowStock = $db->query("SELECT COUNT(*) c FROM products WHERE stock <= 5 AND status = 1")->fetch_assoc()['c'];

$recentOrders = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 8");

$activePage = 'dashboard';
$navBase = '';
$adminAssetPath = '../assets/css/admin.css';
require __DIR__ . '/includes/header.php';
?>

<div class="topbar-admin"><h1>📊 Dashboard</h1></div>

<div class="card" style="margin-bottom:18px;">
  <b style="margin-right:10px;">Period:</b>
  <a href="?range=today" class="btn btn-sm <?= $range=='today' ? '' : 'btn-secondary' ?>">Today</a>
  <a href="?range=7day" class="btn btn-sm <?= $range=='7day' ? '' : 'btn-secondary' ?>">7 Days</a>
  <a href="?range=month" class="btn btn-sm <?= $range=='month' ? '' : 'btn-secondary' ?>">1 Month</a>
  <a href="?range=year" class="btn btn-sm <?= $range=='year' ? '' : 'btn-secondary' ?>">1 Year</a>
  <a href="?range=all" class="btn btn-sm <?= $range=='all' ? '' : 'btn-secondary' ?>">All Time</a>
</div>

<h3 style="margin:0 0 10px;">Orders & Customers (<?= e(ucfirst($range)) ?>)</h3>
<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= (int)$totalCustomers ?></div><div class="label">Total Customers</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalOrders ?></div><div class="label">Total Orders</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalOngoing ?></div><div class="label">Total Ongoing</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalComplete ?></div><div class="label">Total Delivered / Completed</div></div>
  <div class="stat-card"><div class="num"><?= money($totalSales) ?></div><div class="label">Total Sales</div></div>
</div>

<h3 style="margin:22px 0 10px;">Inventory Overview (All Time)</h3>
<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= (int)$totalStockProducts ?></div><div class="label">Products In Stock</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalStockCount ?></div><div class="label">Total Stock Count (Units)</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalStockCategories ?></div><div class="label">Total Categories</div></div>
  <div class="stat-card"><div class="num"><?= (int)$totalBrands ?></div><div class="label">Total Brands</div></div>
  <div class="stat-card"><div class="num"><?= (int)$lowStock ?></div><div class="label">Low Stock (≤5)</div></div>
</div>

<div class="card" style="margin-top:22px;">
  <h3 style="margin-top:0;">Recent Orders</h3>
  <table class="admin-table">
    <tr><th>Invoice</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th></th></tr>
    <?php while ($o = $recentOrders->fetch_assoc()): ?>
    <tr>
      <td><?= e($o['invoice_number']) ?></td>
      <td><?= e($o['customer_name']) ?></td>
      <td><?= money($o['grand_total']) ?></td>
      <td><span class="badge badge-active"><?= e($o['status']) ?></span></td>
      <td><?= date('d M, h:i A', strtotime($o['created_at'])) ?></td>
      <td><a href="orders/view.php?id=<?= $o['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
    </tr>
    <?php endwhile; ?>
    <?php if ($recentOrders->num_rows === 0): ?>
      <tr><td colspan="6" style="text-align:center;color:#6b7280;">No orders yet</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
