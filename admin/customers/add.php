<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if ($name === '' || $phone === '') {
        flash('error', 'Name and phone are required');
    } else {
        $stmt = $db->prepare("INSERT INTO customers (name, phone, location) VALUES (?,?,?)
            ON DUPLICATE KEY UPDATE name=VALUES(name), location=VALUES(location)");
        $stmt->bind_param('sss', $name, $phone, $location);
        $stmt->execute();
        flash('success', 'Customer saved');
        redirect('index.php');
    }
}

$activePage = 'customers';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>👥 Add Customer</h1></div>
<?php if ($msg = flash('error')): ?><div class="alert alert-error"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post">
    <div class="form-group">
      <label>Name *</label>
      <input type="text" name="name" required>
    </div>
    <div class="form-group">
      <label>Phone *</label>
      <input type="text" name="phone" required>
    </div>
    <div class="form-group">
      <label>Location</label>
      <input type="text" name="location">
    </div>
    <button type="submit" class="btn">Save Customer</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
