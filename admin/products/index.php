<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

$search = trim($_GET['q'] ?? '');
$products = getAllProductsAdmin($search);

$activePage = 'products';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin">
  <h1>📦 Products</h1>
  <div>
    <a href="add.php" class="btn btn-sm">+ Add Product</a>
    <a href="inventory.php" class="btn btn-sm btn-secondary">📊 Inventory</a>
    <a href="import.php" class="btn btn-sm btn-secondary">⬆ Import CSV</a>
    <a href="export.php" class="btn btn-sm btn-secondary">⬇ Export CSV</a>
    <a href="template.php" class="btn btn-sm btn-secondary">⬇ Template</a>
  </div>
</div>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="get" style="margin-bottom:14px;">
    <input type="text" name="q" placeholder="Search product name or SKU..." value="<?= e($search) ?>" style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:7px;width:280px;">
    <button class="btn btn-sm" type="submit">Search</button>
  </form>
  <table class="admin-table">
    <tr><th>Image</th><th>Name</th><th>Brand</th><th>Category</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
    <?php while ($p = $products->fetch_assoc()): ?>
    <tr>
      <td><?php if ($p['image']): ?><img src="../../uploads/products/<?= e($p['image']) ?>"><?php else: ?>—<?php endif; ?></td>
      <td><?= e($p['name']) ?></td>
      <td><?= e($p['brand'] ?: '-') ?></td>
      <td><?= e($p['category_name'] ?? '-') ?></td>
      <td><?= e($p['sku']) ?></td>
      <td><?= money($p['price']) ?></td>
      <td>
        <?= (int)$p['stock'] ?>
        <?php if ($p['stock'] <= 5): ?><span class="badge badge-low">Low</span><?php endif; ?>
      </td>
      <td><span class="badge <?= $p['status'] ? 'badge-active' : 'badge-inactive' ?>"><?= $p['status'] ? 'Active' : 'Inactive' ?></span></td>
      <td>
        <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
        <a href="stock.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline">Stock</a>
        <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
    <?php if ($products->num_rows === 0): ?>
      <tr><td colspan="9" style="text-align:center;color:#6b7280;">No products found</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
