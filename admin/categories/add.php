<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $parentId = (int)($_POST['parent_id'] ?? 0) ?: null;
    $status = isset($_POST['status']) ? 1 : 0;
    $slug = slugify($name);
    if ($name === '') {
        flash('error', 'Category name is required');
    } else {
        $stmt = $db->prepare("INSERT INTO categories (parent_id, name, slug, status) VALUES (?,?,?,?)");
        $stmt->bind_param('issi', $parentId, $name, $slug, $status);
        $stmt->execute();
        flash('success', 'Category added');
        redirect('index.php');
    }
}

$parents = getCategories(false);

$activePage = 'categories';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>🏷 Add Category</h1></div>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post">
    <div class="form-row">
      <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name" required>
      </div>
      <div class="form-group">
        <label>Parent Category</label>
        <select name="parent_id">
          <option value="0">-- None (Top Level) --</option>
          <?php foreach ($parents as $p): ?>
            <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="max-width:150px;">
        <label>Status</label>
        <select name="status">
          <option value="1" selected>Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn">Save Category</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
