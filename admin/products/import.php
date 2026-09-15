<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();

$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['csv']['tmp_name'])) {
    $db = getDB();
    $fh = fopen($_FILES['csv']['tmp_name'], 'r');
    $header = fgetcsv($fh);

    // map header names (case-insensitive, trimmed) to column index
    $colIndex = [];
    foreach ($header as $i => $h) {
        $colIndex[strtolower(trim($h))] = $i;
    }

    function findCol($colIndex, array $names) {
        foreach ($names as $n) {
            if (isset($colIndex[$n])) return $colIndex[$n];
        }
        return null;
    }

    $iName = findCol($colIndex, ['name']);
    $iCode = findCol($colIndex, ['code', 'sku']);
    $iBrand = findCol($colIndex, ['brand']);
    $iCategory = findCol($colIndex, ['category']);
    $iQty = findCol($colIndex, ['quantity', 'stock']);
    $iPrice = findCol($colIndex, ['price', 'body price', 'sale price', 'selling price']);

    $catCache = [];
    foreach (getCategories(false) as $c) $catCache[strtolower($c['name'])] = $c['id'];

    $inserted = 0; $updated = 0; $skipped = 0;

    if ($iName === null || $iPrice === null) {
        $result = ['error' => 'CSV must contain at least "Name" and "Price" columns.'];
    } else {
        while (($row = fgetcsv($fh)) !== false) {
            $name = trim($row[$iName] ?? '');
            if ($name === '') { $skipped++; continue; }

            $sku = $iCode !== null ? trim($row[$iCode] ?? '') : '';
            $brand = $iBrand !== null ? trim($row[$iBrand] ?? '') : '';
            $category = $iCategory !== null ? trim($row[$iCategory] ?? '') : '';
            $qty = $iQty !== null ? trim($row[$iQty] ?? '') : '0';
            $price = trim($row[$iPrice] ?? '0');

            $categoryId = null;
            if ($category !== '') {
                $key = strtolower($category);
                if (isset($catCache[$key])) {
                    $categoryId = $catCache[$key];
                } else {
                    $slug = slugify($category);
                    $stmt = $db->prepare("INSERT INTO categories (name, slug, status) VALUES (?,?,1)");
                    $stmt->bind_param('ss', $category, $slug);
                    $stmt->execute();
                    $categoryId = $db->insert_id;
                    $catCache[$key] = $categoryId;
                }
            }

            $stock = (int)($qty !== '' ? $qty : 0);
            $priceVal = (float)($price !== '' ? $price : 0);
            $skuVal = $sku !== '' ? $sku : null;
            $brandVal = $brand !== '' ? $brand : null;

            $existing = null;
            if ($skuVal) {
                $stmt = $db->prepare("SELECT id FROM products WHERE sku=?");
                $stmt->bind_param('s', $skuVal);
                $stmt->execute();
                $existing = $stmt->get_result()->fetch_assoc();
            }

            if ($existing) {
                $stmt = $db->prepare("UPDATE products SET category_id=?, brand=?, name=?, price=?, stock=?, status=1 WHERE id=?");
                $stmt->bind_param('issdii', $categoryId, $brandVal, $name, $priceVal, $stock, $existing['id']);
                $stmt->execute();
                $updated++;
            } else {
                $stmt = $db->prepare("INSERT INTO products (category_id, brand, name, sku, price, stock, status) VALUES (?,?,?,?,?,?,1)");
                $stmt->bind_param('isssdi', $categoryId, $brandVal, $name, $skuVal, $priceVal, $stock);
                $stmt->execute();
                $inserted++;
            }
        }
        $result = ['inserted' => $inserted, 'updated' => $updated, 'skipped' => $skipped];
    }
    fclose($fh);
}

$activePage = 'products';
$navBase = '../';
$adminAssetPath = '../../assets/css/admin.css';
require __DIR__ . '/../includes/header.php';
?>

<div class="topbar-admin"><h1>📦 Import Products (CSV)</h1></div>

<?php if ($result && isset($result['error'])): ?>
  <div class="alert alert-error"><?= e($result['error']) ?></div>
<?php elseif ($result): ?>
  <div class="alert alert-success">
    Inserted: <?= (int)$result['inserted'] ?>, Updated: <?= (int)$result['updated'] ?>, Skipped: <?= (int)$result['skipped'] ?>
  </div>
<?php endif; ?>

<div class="card">
  <p style="color:#6b7280;font-size:13.5px;margin-top:0;">
    CSV must have a header row with column names: <b>Name</b>, <b>Code</b> (SKU), <b>Brand</b>, <b>Category</b>, <b>Quantity</b>, <b>Price</b>.
    Products matched by existing SKU are updated; new SKUs (or no SKU) are added as new products.
  </p>
  <p><a href="template.php" class="btn btn-sm btn-secondary">⬇ Download CSV Template</a></p>
  <form method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label>CSV File</label>
      <input type="file" name="csv" accept=".csv" required>
    </div>
    <button type="submit" class="btn">Import Products</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
