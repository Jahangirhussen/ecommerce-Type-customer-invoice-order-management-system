<?php
require_once __DIR__ . '/functions.php';

function getProduct($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllProductsAdmin($search = '') {
    $db = getDB();
    $sql = "SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    $params = []; $types = '';
    if ($search !== '') {
        $sql .= " AND (p.name LIKE ? OR p.sku LIKE ? OR p.brand LIKE ?)";
        $like = "%$search%";
        $params[] = $like; $params[] = $like; $params[] = $like; $types .= 'sss';
    }
    $sql .= " ORDER BY p.id DESC";
    $stmt = $db->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

function handleProductImageUpload($existing = '') {
    $imageName = $existing;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $allowed)) {
            $newName = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], UPLOAD_PATH . $newName)) {
                $imageName = $newName;
            }
        }
    }
    return $imageName;
}

function saveProduct($data) {
    $db = getDB();
    $id = (int)($data['id'] ?? 0);
    $category_id = (int)($data['category_id'] ?? 0) ?: null;
    $brand = trim($data['brand'] ?? '');
    $name = trim($data['name'] ?? '');
    $sku = trim($data['sku'] ?? '');
    $price = (float)($data['price'] ?? 0);
    $stock = (int)($data['stock'] ?? 0);
    $description = trim($data['description'] ?? '');
    $status = isset($data['status']) ? 1 : 0;
    $imageName = handleProductImageUpload($data['existing_image'] ?? '');

    if ($name === '') {
        flash('error', 'Product name is required');
        return false;
    }
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE products SET category_id=?, brand=?, name=?, sku=?, price=?, stock=?, image=?, description=?, status=? WHERE id=?");
        $stmt->bind_param('isssdisssi', $category_id, $brand, $name, $sku, $price, $stock, $imageName, $description, $status, $id);
        $stmt->execute();
        flash('success', 'Product updated');
    } else {
        $stmt = $db->prepare("INSERT INTO products (category_id, brand, name, sku, price, stock, image, description, status) VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param('isssdisss', $category_id, $brand, $name, $sku, $price, $stock, $imageName, $description, $status);
        $stmt->execute();
        flash('success', 'Product added');
    }
    return true;
}

function deleteProduct($id) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM products WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

function updateProductStock($id, $stock) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE products SET stock=? WHERE id=?");
    $stmt->bind_param('ii', $stock, $id);
    $stmt->execute();
}

function getBrands() {
    $db = getDB();
    $res = $db->query("SELECT brand, COUNT(*) AS product_count FROM products WHERE brand IS NOT NULL AND brand != '' GROUP BY brand ORDER BY brand ASC");
    $out = [];
    while ($row = $res->fetch_assoc()) $out[] = $row;
    return $out;
}

function searchProducts($search = '', $categoryId = 0) {
    $db = getDB();
    $sql = "SELECT id, name, sku, price, stock, image FROM products WHERE status = 1";
    $params = []; $types = '';
    if ($search !== '') {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%"; $types .= 's';
    }
    if ($categoryId > 0) {
        $sql .= " AND category_id = ?";
        $params[] = $categoryId; $types .= 'i';
    }
    $sql .= " ORDER BY name ASC LIMIT 200";
    $stmt = $db->prepare($sql);
    if ($params) $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
    $products = [];
    while ($row = $res->fetch_assoc()) {
        $row['price'] = (float)$row['price'];
        $row['stock'] = (int)$row['stock'];
        $row['image_url'] = $row['image'] ? UPLOAD_URL . $row['image'] : null;
        $products[] = $row;
    }
    return $products;
}
