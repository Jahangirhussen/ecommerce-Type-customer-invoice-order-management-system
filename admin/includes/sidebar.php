<?php $nb = $navBase ?? ''; ?>
<div class="sidebar">
  <div class="brand">🛠 Admin Panel</div>
  <a href="<?= $nb ?>index.php" class="<?= $activePage=='dashboard'?'active':'' ?>">📊 Dashboard</a>
  <a href="<?= $nb ?>brands/index.php" class="<?= $activePage=='brands'?'active':'' ?>">🏭 Brands</a>
  <a href="<?= $nb ?>categories/index.php" class="<?= $activePage=='categories'?'active':'' ?>">🏷 Categories</a>
  <a href="<?= $nb ?>products/index.php" class="<?= $activePage=='products'?'active':'' ?>">📦 Product List</a>
  <a href="<?= $nb ?>products/add.php" class="<?= $activePage=='products_add'?'active':'' ?>">➕ Add Product</a>
  <a href="<?= $nb ?>products/inventory.php" class="<?= $activePage=='inventory'?'active':'' ?>">📊 Inventory</a>
  <a href="<?= $nb ?>orders/index.php" class="<?= $activePage=='orders'?'active':'' ?>">🧾 Orders</a>
  <a href="<?= $nb ?>customers/index.php" class="<?= $activePage=='customers'?'active':'' ?>">👥 Customers</a>
  <a href="<?= $nb ?>settings/shop.php" class="<?= $activePage=='settings'?'active':'' ?>">⚙️ Settings</a>
  <a href="<?= $nb ?>../customer/index.php" target="_blank">🛍 View Customer Page</a>
  <a href="<?= $nb ?>../logout.php" class="logout">🚪 Logout</a>
</div>
