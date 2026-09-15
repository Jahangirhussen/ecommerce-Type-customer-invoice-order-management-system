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
<title>Checkout - <?= e($shopName) ?></title>
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
    <h2 style="margin-top:0;">Customer Information</h2>
    <div id="orderSummary" style="margin-bottom:16px;"></div>
    <form id="checkoutForm">
      <div class="form-group">
        <label>Customer Name *</label>
        <input type="text" id="customer_name" required>
      </div>
      <div class="form-group">
        <label>Phone *</label>
        <input type="text" id="customer_phone" required>
      </div>
      <div class="form-group">
        <label>Address</label>
        <textarea id="customer_address"></textarea>
      </div>
      <div class="form-group">
        <label>Order Note</label>
        <textarea id="customer_note"></textarea>
      </div>
      <button type="submit" class="btn btn-block" id="submitBtn">Generate Invoice</button>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>
<script src="../assets/js/app.js"></script>
<script>
function renderSummary(){
  const cart = getCart();
  const box = document.getElementById('orderSummary');
  if (!cart.length) { location.href = 'products.php'; return; }
  let total = 0;
  box.innerHTML = cart.map(i => {
    total += i.price * i.qty;
    return `<div class="cart-item"><div class="cart-item-info"><div class="cart-item-name">${escapeHtml(i.name)} × ${i.qty}</div></div><b>${money(i.price*i.qty)}</b></div>`;
  }).join('') + `<div class="cart-total-row" style="margin-top:10px;"><span>Total</span><span>${money(total)}</span></div>`;
}
renderSummary();

document.getElementById('checkoutForm').addEventListener('submit', function(e){
  e.preventDefault();
  const cart = getCart();
  if (!cart.length) { showToast('Cart is empty'); return; }
  const btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.textContent = 'Processing...';

  const payload = {
    cart: cart.map(i => ({ id: i.id, qty: i.qty })),
    customer_name: document.getElementById('customer_name').value.trim(),
    customer_phone: document.getElementById('customer_phone').value.trim(),
    customer_address: document.getElementById('customer_address').value.trim(),
    customer_note: document.getElementById('customer_note').value.trim(),
  };

  fetch('../api/checkout.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(payload)
  }).then(r => r.json()).then(res => {
    if (res.success) {
      clearCart();
      location.href = 'success.php?id=' + res.order_id;
    } else {
      showToast(res.message || 'Order failed');
      btn.disabled = false; btn.textContent = 'Generate Invoice';
    }
  }).catch(() => {
    showToast('Network error');
    btn.disabled = false; btn.textContent = 'Generate Invoice';
  });
});
</script>
</body>
</html>
