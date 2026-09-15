<?php
require_once __DIR__ . '/includes/auth.php';

if (isAdminLoggedIn()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (adminLogin($email, $password)) {
        redirect('admin/index.php');
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-box">
    <h1>🛠 Admin Login</h1>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required value="admin@shop.com">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>
      <button type="submit" class="btn" style="width:100%;">Login</button>
    </form>
    <p style="text-align:center;color:#6b7280;font-size:12px;margin-top:14px;">
      Default: admin@shop.com / admin123
    </p>
  </div>
</div>
</body>
</html>
