<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$settings = getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsapp = trim($_POST['whatsapp_number'] ?? '');
    if (!empty($settings)) {
        $stmt = $db->prepare("UPDATE settings SET whatsapp_number=? WHERE id=?");
        $stmt->bind_param('si', $whatsapp, $settings['id']);
        $stmt->execute();
    } else {
        $stmt = $db->prepare("INSERT INTO settings (whatsapp_number) VALUES (?)");
        $stmt->bind_param('s', $whatsapp);
        $stmt->execute();
    }
    flash('success', 'WhatsApp settings saved');
    redirect('whatsapp.php');
}

$activePage = 'settings';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>⚙️ WhatsApp Settings</h1></div>
<div style="margin-bottom:14px;"><a href="shop.php" class="btn btn-sm btn-secondary">Shop Info</a> <a href="whatsapp.php" class="btn btn-sm">WhatsApp</a></div>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post">
    <div class="form-group">
      <label>Supplier WhatsApp Number (with country code, e.g. 8801XXXXXXXXX)</label>
      <input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>">
    </div>
    <button type="submit" class="btn">Save Settings</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
