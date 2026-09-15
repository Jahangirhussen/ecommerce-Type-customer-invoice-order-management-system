// Shared helpers: cart storage (localStorage), money format, toast

function money(n) {
  return '৳' + Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function escapeHtml(s) {
  const d = document.createElement('div');
  d.textContent = s ?? '';
  return d.innerHTML;
}

function getCart() {
  try {
    return JSON.parse(localStorage.getItem('cart') || '[]');
  } catch (e) {
    return [];
  }
}

function setCart(cart) {
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartBadge();
}

function clearCart() {
  localStorage.removeItem('cart');
  updateCartBadge();
}

function cartCount() {
  return getCart().reduce((n, i) => n + i.qty, 0);
}

function updateCartBadge() {
  const el = document.getElementById('cartCount');
  if (el) el.textContent = cartCount();
}

function addToCart(product, qty) {
  const cart = getCart();
  const existing = cart.find(i => i.id === product.id);
  const newQty = (existing ? existing.qty : 0) + qty;
  if (newQty > product.stock) {
    showToast(`Only ${product.stock} available`);
    return false;
  }
  if (existing) {
    existing.qty = newQty;
  } else {
    cart.push({ id: product.id, name: product.name, price: product.price, stock: product.stock, qty: newQty });
  }
  setCart(cart);
  return true;
}

function changeQty(id, amount) {
  const cart = getCart();
  const item = cart.find(i => i.id === id);
  if (!item) return;
  item.qty += amount;
  if (item.qty <= 0) {
    removeFromCart(id);
    return;
  }
  if (item.qty > item.stock) item.qty = item.stock;
  setCart(cart);
}

function removeFromCart(id) {
  setCart(getCart().filter(i => i.id !== id));
}

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) { alert(msg); return; }
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(window._toastTimer);
  window._toastTimer = setTimeout(() => t.classList.remove('show'), 2500);
}

document.addEventListener('DOMContentLoaded', updateCartBadge);
