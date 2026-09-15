<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$product = getProduct($id);
if (!$product) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    updateProductStock($id, (int)$_POST['stock']);
    flash('success', 'Stock updated');
    redirect('index.php');
}

$activePage = 'products';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>📦 Update Stock — <?= e($product['name']) ?></h1></div>

<div class="card" style="max-width:420px;">
  <form method="post">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <div class="form-group">
      <label>Current Stock: <?= (int)$product['stock'] ?></label>
      <input type="number" name="stock" required value="<?= (int)$product['stock'] ?>">
    </div>
    <button type="submit" class="btn">Update Stock</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
