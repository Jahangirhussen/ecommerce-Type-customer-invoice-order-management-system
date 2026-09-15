<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $product = getProduct($id);
    if ($product) {
        if (($_POST['op'] ?? '') === 'inc') {
            updateProductStock($id, (int)$product['stock'] + 1);
        } elseif (($_POST['op'] ?? '') === 'dec') {
            updateProductStock($id, max(0, (int)$product['stock'] - 1));
        } elseif (($_POST['op'] ?? '') === 'set') {
            updateProductStock($id, max(0, (int)$_POST['stock']));
        }
    }
    $qs = $_GET['q'] ?? '';
    redirect('inventory.php' . ($qs !== '' ? '?q=' . urlencode($qs) : ''));
}

$search = trim($_GET['q'] ?? '');
$products = getAllProductsAdmin($search);

$activePage = 'inventory';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>📊 Inventory Management</h1><a href="index.php" class="btn btn-sm btn-secondary">← Products</a></div>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="get" style="margin-bottom:14px;">
    <input type="text" name="q" placeholder="Search product name, SKU or brand..." value="<?= e($search) ?>" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:7px;width:300px;">
    <button class="btn btn-sm" type="submit">Search</button>
  </form>
  <table class="admin-table">
    <tr><th>Name</th><th>Brand</th><th>Category</th><th>SKU</th><th>Stock</th><th style="width:220px;">Update Stock</th></tr>
    <?php while ($p = $products->fetch_assoc()): ?>
    <tr>
      <td><?= e($p['name']) ?></td>
      <td><?= e($p['brand'] ?: '-') ?></td>
      <td><?= e($p['category_name'] ?? '-') ?></td>
      <td><?= e($p['sku']) ?></td>
      <td>
        <b><?= (int)$p['stock'] ?></b>
        <?php if ($p['stock'] <= 5): ?><span class="badge badge-low">Low</span><?php endif; ?>
      </td>
      <td>
        <div style="display:flex;gap:6px;align-items:center;">
          <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="op" value="dec">
            <button type="submit" class="btn btn-sm btn-outline">−</button>
          </form>
          <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="op" value="inc">
            <button type="submit" class="btn btn-sm btn-outline">+</button>
          </form>
          <form method="post" style="display:flex;gap:4px;align-items:center;">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">
            <input type="hidden" name="op" value="set">
            <input type="number" name="stock" value="<?= (int)$p['stock'] ?>" min="0" style="width:70px;padding:5px 6px;border:1px solid #e5e7eb;border-radius:6px;">
            <button type="submit" class="btn btn-sm">Set</button>
          </form>
        </div>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if ($products->num_rows === 0): ?>
      <tr><td colspan="6" style="text-align:center;color:#6b7280;">No products found</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
