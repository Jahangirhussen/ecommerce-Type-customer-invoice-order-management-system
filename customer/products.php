<?php
require_once __DIR__ . '/../includes/product-functions.php';

$settings = getSettings();
$shopName = $settings['shop_name'] ?? 'My Shop';
$categories = getCategories(true);
$products = searchProducts('', 0);
foreach ($products as &$p) {
    $p['image_url'] = $p['image'] ? '../' . UPLOAD_URL . $p['image'] : null;
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($shopName) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<div class="topbar">
  <div class="inner">
    <h1><?= e($shopName) ?></h1>
    <button class="cart-btn" onclick="openCart()">🛒 Cart <span class="cart-badge" id="cartCount">0</span></button>
  </div>
</div>

<div class="filters">
  <input type="text" class="search-box" id="searchBox" placeholder="Search products...">
  <div class="category-scroll" id="categoryScroll">
    <div class="category-chip active" data-id="0">All</div>
    <?php foreach ($categories as $c): ?>
      <div class="category-chip" data-id="<?= $c['id'] ?>"><?= e($c['name']) ?></div>
    <?php endforeach; ?>
  </div>
</div>

<div class="product-grid" id="productGrid" data-products='<?= e(json_encode($products)) ?>'>
</div>
<div class="empty-state" id="emptyState" style="display:none;">No products found.</div>

<div class="overlay" id="overlay" onclick="closeCart()"></div>
<div class="cart-drawer" id="cartDrawer">
  <div class="cart-header">
    <h2>Your Cart</h2>
    <button class="close-btn" onclick="closeCart()">×</button>
  </div>
  <div class="cart-items" id="cartItems"></div>
  <div class="cart-footer">
    <div class="cart-total-row"><span>Total</span><span id="cartTotal">৳0.00</span></div>
    <button class="checkout-btn" id="checkoutBtn" onclick="location.href='checkout.php'">Generate Invoice</button>
  </div>
</div>

<div class="toast" id="toast"></div>

<script src="../assets/js/app.js"></script>
<script src="../assets/js/cart.js"></script>
<script src="../assets/js/products.js"></script>
</body>
</html>
