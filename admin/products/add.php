<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/product-functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (saveProduct($_POST)) {
        redirect('index.php');
    }
}

$categories = getCategories(false);
$brands = getBrands();
$activePage = 'products_add';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>📦 Add Product</h1></div>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="0">
    <div class="form-row">
      <div class="form-group">
        <label>Product Name</label>
        <input type="text" name="name" required>
      </div>
      <div class="form-group">
        <label>Category</label>
        <select name="category_id">
          <option value="">-- Select --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Brand</label>
        <input type="text" name="brand" list="brandList" placeholder="Type or select a brand" autocomplete="off">
        <datalist id="brandList">
          <?php foreach ($brands as $b): ?>
            <option value="<?= e($b['brand']) ?>">
          <?php endforeach; ?>
        </datalist>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>SKU</label>
        <input type="text" name="sku">
      </div>
      <div class="form-group">
        <label>Price (৳)</label>
        <input type="number" step="0.01" name="price" required>
      </div>
      <div class="form-group">
        <label>Stock</label>
        <input type="number" name="stock" required value="0">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Product Image</label>
        <input type="file" name="image" accept="image/*">
      </div>
      <div class="form-group" style="max-width:150px;">
        <label>Status</label>
        <select name="status">
          <option value="1" selected>Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Description (optional)</label>
      <textarea name="description"></textarea>
    </div>
    <button type="submit" class="btn">Save Product</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
