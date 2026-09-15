// Product grid: render, search, category filter, add to cart (customer/products.php)

let ALL_PRODUCTS = [];
let CURRENT_CATEGORY = 0;
let CURRENT_SEARCH = '';

document.addEventListener('DOMContentLoaded', function () {
  const grid = document.getElementById('productGrid');
  ALL_PRODUCTS = JSON.parse(grid.dataset.products || '[]');
  renderProducts();

  document.getElementById('searchBox').addEventListener('input', debounce(function (e) {
    CURRENT_SEARCH = e.target.value.trim();
    fetchProducts();
  }, 350));

  document.getElementById('categoryScroll').addEventListener('click', function (e) {
    const chip = e.target.closest('.category-chip');
    if (!chip) return;
    document.querySelectorAll('.category-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    CURRENT_CATEGORY = parseInt(chip.dataset.id, 10) || 0;
    fetchProducts();
  });
});

function debounce(fn, wait) {
  let t;
  return function (...args) {
    clearTimeout(t);
    t = setTimeout(() => fn.apply(this, args), wait);
  };
}

function fetchProducts() {
  const params = new URLSearchParams();
  if (CURRENT_SEARCH) params.set('q', CURRENT_SEARCH);
  if (CURRENT_CATEGORY) params.set('category_id', CURRENT_CATEGORY);
  fetch('../api/products.php?' + params.toString())
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        ALL_PRODUCTS = res.products;
        renderProducts();
      }
    });
}

function renderProducts() {
  const grid = document.getElementById('productGrid');
  const empty = document.getElementById('emptyState');
  if (!ALL_PRODUCTS.length) {
    grid.innerHTML = '';
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';

  grid.innerHTML = ALL_PRODUCTS.map(p => {
    const out = p.stock <= 0;
    const low = !out && p.stock <= 5;
    const img = p.image_url
      ? `<img src="${p.image_url}" alt="${escapeHtml(p.name)}">`
      : 'No Image';
    return `<div class="product-card">
      <div class="product-img">${img}</div>
      <div class="product-body">
        <div class="product-name">${escapeHtml(p.name)}</div>
        <div class="product-price">${money(p.price)}</div>
        <div class="product-stock ${out ? 'out' : low ? 'low' : ''}">${out ? 'Out of stock' : 'Stock: ' + p.stock}</div>
        <div class="qty-row">
          <button class="qty-btn" onclick="stepQty(${p.id},-1)" ${out ? 'disabled' : ''}>−</button>
          <input class="qty-input" type="number" min="1" max="${p.stock}" value="1" id="qty-${p.id}" ${out ? 'disabled' : ''}>
          <button class="qty-btn" onclick="stepQty(${p.id},1)" ${out ? 'disabled' : ''}>+</button>
        </div>
        <button class="add-cart-btn" onclick="handleAddToCart(${p.id})" ${out ? 'disabled' : ''}>${out ? 'Out of Stock' : 'Add to Cart'}</button>
      </div>
    </div>`;
  }).join('');
}

function stepQty(id, amount) {
  const input = document.getElementById('qty-' + id);
  let v = (parseInt(input.value, 10) || 1) + amount;
  if (v < 1) v = 1;
  if (v > parseInt(input.max, 10)) v = parseInt(input.max, 10);
  input.value = v;
}

function handleAddToCart(id) {
  const product = ALL_PRODUCTS.find(p => p.id == id);
  if (!product) return;
  const qty = parseInt(document.getElementById('qty-' + id).value, 10) || 1;
  if (addToCart(product, qty)) {
    showToast(product.name + ' added to cart');
    openCart();
  }
}
