<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$search = trim($_GET['q'] ?? '');
$searchSql = '';
if ($search !== '') {
    $s = $db->real_escape_string($search);
    $searchSql = " HAVING name LIKE '%$s%' OR phone LIKE '%$s%' ";
}

$sql = "
SELECT name, phone, location, total_orders, completed_orders, pending_orders, total_sales FROM (
  SELECT
    c.name AS name,
    c.phone AS phone,
    c.location AS location,
    COUNT(o.id) AS total_orders,
    SUM(CASE WHEN o.status='Completed' THEN 1 ELSE 0 END) AS completed_orders,
    SUM(CASE WHEN o.status='Pending' THEN 1 ELSE 0 END) AS pending_orders,
    COALESCE(SUM(CASE WHEN o.status!='Cancelled' THEN o.grand_total ELSE 0 END),0) AS total_sales
  FROM customers c
  LEFT JOIN orders o ON o.customer_phone = c.phone
  GROUP BY c.id

  UNION ALL

  SELECT
    o.customer_name AS name,
    o.customer_phone AS phone,
    NULL AS location,
    COUNT(*) AS total_orders,
    SUM(CASE WHEN o.status='Completed' THEN 1 ELSE 0 END) AS completed_orders,
    SUM(CASE WHEN o.status='Pending' THEN 1 ELSE 0 END) AS pending_orders,
    COALESCE(SUM(CASE WHEN o.status!='Cancelled' THEN o.grand_total ELSE 0 END),0) AS total_sales
  FROM orders o
  WHERE o.customer_phone NOT IN (SELECT phone FROM customers)
  GROUP BY o.customer_phone
) x
$searchSql
ORDER BY total_sales DESC
";
$customers = $db->query($sql);

$overall = $db->query("SELECT COALESCE(SUM(grand_total),0) s FROM orders WHERE status != 'Cancelled'")->fetch_assoc()['s'];

$activePage = 'customers';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin">
  <h1>👥 Customers</h1>
  <a href="add.php" class="btn btn-sm">+ Add Customer</a>
</div>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="stat-grid">
  <div class="stat-card"><div class="num"><?= money($overall) ?></div><div class="label">Total Sales (Overall)</div></div>
</div>

<div class="card">
  <form method="get" style="margin-bottom:14px;">
    <input type="text" name="q" placeholder="Search name or phone..." value="<?= e($search) ?>" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:7px;width:280px;">
    <button class="btn btn-sm" type="submit">Search</button>
  </form>
  <table class="admin-table">
    <tr><th>Name</th><th>Phone</th><th>Location</th><th>Total Orders</th><th>Completed</th><th>Pending</th><th>Total Sales</th><th></th></tr>
    <?php while ($c = $customers->fetch_assoc()): ?>
    <tr>
      <td><?= e($c['name']) ?></td>
      <td><?= e($c['phone']) ?></td>
      <td><?= e($c['location'] ?: '-') ?></td>
      <td><?= (int)$c['total_orders'] ?></td>
      <td><?= (int)$c['completed_orders'] ?></td>
      <td><?= (int)$c['pending_orders'] ?></td>
      <td><?= money($c['total_sales']) ?></td>
      <td><a href="view.php?phone=<?= urlencode($c['phone']) ?>" class="btn btn-sm btn-outline">View</a></td>
    </tr>
    <?php endwhile; ?>
    <?php if ($customers->num_rows === 0): ?>
      <tr><td colspan="8" style="text-align:center;color:#6b7280;">No customers found</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
