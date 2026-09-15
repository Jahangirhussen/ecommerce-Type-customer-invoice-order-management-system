<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$categories = $db->query("SELECT c.*, p.name AS parent_name, (SELECT COUNT(*) FROM products pr WHERE pr.category_id=c.id) AS product_count FROM categories c LEFT JOIN categories p ON p.id=c.parent_id ORDER BY c.name ASC");

$activePage = 'categories';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>🏷 Categories</h1><a href="add.php" class="btn btn-sm">+ Add Category</a></div>

<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <table class="admin-table">
    <tr><th>Name</th><th>Parent</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr>
    <?php while ($c = $categories->fetch_assoc()): ?>
    <tr>
      <td><?= e($c['name']) ?></td>
      <td><?= e($c['parent_name'] ?: '-') ?></td>
      <td><?= e($c['slug']) ?></td>
      <td><?= (int)$c['product_count'] ?></td>
      <td><span class="badge <?= $c['status'] ? 'badge-active' : 'badge-inactive' ?>"><?= $c['status'] ? 'Active' : 'Inactive' ?></span></td>
      <td>
        <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
        <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
