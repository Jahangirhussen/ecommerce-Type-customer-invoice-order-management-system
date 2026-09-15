<?php
require_once __DIR__ . '/../includes/functions.php';
$settings = getSettings();
$shopName = $settings['shop_name'] ?? 'My Shop';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Cart - <?= e($shopName) ?></title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/responsive.css">
</head>
<body>

<div class="topbar">
  <div class="inner">
    <h1><a href="products.php" style="color:#fff;">← <?= e($shopName) ?></a></h1>
  </div>
</div>

<div class="form-page">
  <div class="form-card">
    <h2 style="margin-top:0;">My Cart</h2>
    <div id="cartItems"></div>
    <div class="cart-total-row" style="margin-top:14px;"><span>Total</span><span id="cartTotal">৳0.00</span></div>
    <button class="btn btn-block" style="margin-top:14px;" onclick="location.href='checkout.php'">Proceed to Checkout</button>
    <a href="products.php" class="btn btn-secondary btn-block" style="margin-top:10px;">Continue Shopping</a>
  </div>
</div>

<div class="toast" id="toast"></div>
<script src="../assets/js/app.js"></script>
<script>
function renderCartPage(){
  const cart = getCart();
  const box = document.getElementById('cartItems');
  if (!cart.length) { box.innerHTML = '<p style="color:#6b7280;text-align:center;">Cart is empty.</p>'; document.getElementById('cartTotal').textContent = money(0); return; }
  let total = 0;
  box.innerHTML = cart.map(i => {
    total += i.price * i.qty;
    return `<div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${escapeHtml(i.name)}</div>
        <div class="cart-item-price">${money(i.price)} × ${i.qty}</div>
      </div>
      <div class="cart-item-qty">
        <button class="qty-btn" onclick="changeQty(${i.id},-1);renderCartPage()">−</button>
        <span>${i.qty}</span>
        <button class="qty-btn" onclick="changeQty(${i.id},1);renderCartPage()">+</button>
        <button class="remove-item" onclick="removeFromCart(${i.id});renderCartPage()">×</button>
      </div>
    </div>`;
  }).join('');
  document.getElementById('cartTotal').textContent = money(total);
}
renderCartPage();
</script>
</body>
</html>
