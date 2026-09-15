<?php
require_once __DIR__ . '/../../includes/auth.php';
requireAdmin();
$db = getDB();

$settings = getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shopName = trim($_POST['shop_name'] ?? '');
    $shopAddress = trim($_POST['shop_address'] ?? '');
    $shopPhone = trim($_POST['shop_phone'] ?? '');
    $logo = $settings['shop_logo'] ?? '';

    if (!empty($_FILES['shop_logo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['shop_logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $newName = 'logo_' . time() . '.' . $ext;
            $logoDir = __DIR__ . '/../../uploads/logo/';
            if (!is_dir($logoDir)) mkdir($logoDir, 0777, true);
            if (move_uploaded_file($_FILES['shop_logo']['tmp_name'], $logoDir . $newName)) {
                $logo = $newName;
            }
        }
    }

    if (!empty($settings)) {
        $stmt = $db->prepare("UPDATE settings SET shop_name=?, shop_logo=?, shop_phone=?, shop_address=? WHERE id=?");
        $stmt->bind_param('ssssi', $shopName, $logo, $shopPhone, $shopAddress, $settings['id']);
        $stmt->execute();
    } else {
        $stmt = $db->prepare("INSERT INTO settings (shop_name, shop_logo, shop_phone, shop_address) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $shopName, $logo, $shopPhone, $shopAddress);
        $stmt->execute();
    }
    flash('success', 'Shop settings saved');
    redirect('shop.php');
}

$activePage = 'settings';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>
<div class="topbar-admin"><h1>⚙️ Shop Settings</h1></div>
<div style="margin-bottom:14px;"><a href="shop.php" class="btn btn-sm">Shop Info</a> <a href="whatsapp.php" class="btn btn-sm btn-secondary">WhatsApp</a></div>
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card">
  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>Shop Name</label>
      <input type="text" name="shop_name" required value="<?= e($settings['shop_name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Shop Address</label>
      <textarea name="shop_address"><?= e($settings['shop_address'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
      <label>Shop Phone</label>
      <input type="text" name="shop_phone" value="<?= e($settings['shop_phone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Shop Logo</label>
      <input type="file" name="shop_logo" accept="image/*">
      <?php if (!empty($settings['shop_logo'])): ?>
        <img src="../../uploads/logo/<?= e($settings['shop_logo']) ?>" style="width:60px;margin-top:6px;border-radius:6px;">
      <?php endif; ?>
    </div>
    <button type="submit" class="btn">Save Settings</button>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
