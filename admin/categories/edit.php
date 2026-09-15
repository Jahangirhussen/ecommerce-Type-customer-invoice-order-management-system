<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$id = (int)($_GET['id'] ?? 0);
$stmt = $db->prepare("SELECT * FROM categories WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$editCat = $stmt->get_result()->fetch_assoc();
if (!$editCat) redirect('index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $parentId = (int)($_POST['parent_id'] ?? 0) ?: null;
    $status = isset($_POST['status']) ? 1 : 0;
    $slug = slugify($name);

    if ($name === '') {
        flash('error', 'Category name is required');
    } elseif ($parentId === $id) {
        flash('error', 'A category cannot be its own parent');
    } else {
        $stmt = $db->prepare("UPDATE categories SET parent_id=?, name=?, slug=?, status=? WHERE id=?");
        $stmt->bind_param('issii', $parentId, $name, $slug, $status, $id);
        $stmt->execute();
        flash('success', 'Category updated');
        redirect('index.php');
    }
}

$parents = array_filter(getCategories(false), fn($p) => $p['id'] != $id);

$activePage = 'categories';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>🏷 Edit Category</h1></div>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post">
    <div class="form-row">
      <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name" required value="<?= e($editCat['name']) ?>">
      </div>
      <div class="form-group">
        <label>Parent Category</label>
        <select name="parent_id">
          <option value="0">-- None (Top Level) --</option>
          <?php foreach ($parents as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $editCat['parent_id'] == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group" style="max-width:150px;">
        <label>Status</label>
        <select name="status">
          <option value="1" <?= $editCat['status'] ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$editCat['status'] ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
    </div>
    <button type="submit" class="btn">Update</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
