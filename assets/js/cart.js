// Cart drawer (used on customer/products.php)

function openCart() {
  document.getElementById('overlay').classList.add('show');
  document.getElementById('cartDrawer').classList.add('show');
  renderCartDrawer();
}

function closeCart() {
  document.getElementById('overlay').classList.remove('show');
  document.getElementById('cartDrawer').classList.remove('show');
}

function renderCartDrawer() {
  const cart = getCart();
  const box = document.getElementById('cartItems');
  const totalEl = document.getElementById('cartTotal');
  const checkoutBtn = document.getElementById('checkoutBtn');
  if (!box) return;

  if (!cart.length) {
    box.innerHTML = '<p style="color:#6b7280;text-align:center;padding:30px 0;">Cart is empty.</p>';
    totalEl.textContent = money(0);
    if (checkoutBtn) checkoutBtn.disabled = true;
    return;
  }

  let total = 0;
  box.innerHTML = cart.map(i => {
    total += i.price * i.qty;
    return `<div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${escapeHtml(i.name)}</div>
        <div class="cart-item-price">${money(i.price)} × ${i.qty} = ${money(i.price * i.qty)}</div>
      </div>
      <div class="cart-item-qty">
        <button class="qty-btn" onclick="changeQty(${i.id},-1);renderCartDrawer()">−</button>
        <span>${i.qty}</span>
        <button class="qty-btn" onclick="changeQty(${i.id},1);renderCartDrawer()">+</button>
        <button class="remove-item" onclick="removeFromCart(${i.id});renderCartDrawer()">×</button>
      </div>
    </div>`;
  }).join('');

  totalEl.textContent = money(total);
  if (checkoutBtn) checkoutBtn.disabled = false;
}
