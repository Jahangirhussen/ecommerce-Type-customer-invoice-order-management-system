<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

$search = trim($_GET['q'] ?? '');
$brands = getBrands();
if ($search !== '') {
    $s = mb_strtolower($search);
    $brands = array_values(array_filter($brands, fn($b) => str_contains(mb_strtolower($b['brand']), $s)));
}

$activePage = 'brands';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>🏭 Brands</h1></div>

<div class="card">
  <form method="get" style="margin-bottom:14px;">
    <input type="text" name="q" placeholder="Search brand..." value="<?= e($search) ?>" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:7px;width:280px;">
    <button class="btn btn-sm" type="submit">Search</button>
  </form>
  <table class="admin-table">
    <tr><th>Brand</th><th>Products</th><th></th></tr>
    <?php foreach ($brands as $b): ?>
    <tr>
      <td><?= e($b['brand']) ?></td>
      <td><?= (int)$b['product_count'] ?></td>
      <td><a href="../products/index.php?q=<?= urlencode($b['brand']) ?>" class="btn btn-sm btn-outline">View Products</a></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$brands): ?>
      <tr><td colspan="3" style="text-align:center;color:#6b7280;">No brands found. Brands are set on the product add/edit form or via CSV import.</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
