<?php
require_once __DIR__ . '/functions.php';

function generateInvoiceNumber() {
    $db = getDB();
    $prefix = 'INV-' . date('Ymd') . '-';
    $stmt = $db->prepare("SELECT invoice_number FROM orders WHERE invoice_number LIKE ? ORDER BY id DESC LIMIT 1");
    $like = $prefix . '%';
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    $next = 1;
    if ($row = $res->fetch_assoc()) {
        $lastNum = (int) substr($row['invoice_number'], -4);
        $next = $lastNum + 1;
    }
    return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
}

function createOrder($cart, $customerName, $customerPhone, $customerAddress, $customerNote) {
    if (empty($cart) || !is_array($cart)) {
        return ['success' => false, 'message' => 'Cart is empty'];
    }
    if ($customerName === '' || $customerPhone === '') {
        return ['success' => false, 'message' => 'Name and phone are required'];
    }

    $db = getDB();
    $db->begin_transaction();

    try {
        $items = [];
        $subtotal = 0;

        foreach ($cart as $ci) {
            $productId = (int)($ci['id'] ?? 0);
            $qty = (int)($ci['qty'] ?? 0);
            if ($productId <= 0 || $qty <= 0) continue;

            $stmt = $db->prepare("SELECT id, name, price, stock, status FROM products WHERE id = ? FOR UPDATE");
            $stmt->bind_param('i', $productId);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if (!$product || !$product['status']) {
                throw new Exception("Product not available");
            }
            if ($qty > $product['stock']) {
                throw new Exception("Sorry, only {$product['stock']} of \"{$product['name']}\" left in stock");
            }

            $lineTotal = $product['price'] * $qty;
            $subtotal += $lineTotal;
            $items[] = [
                'product_id' => $product['id'],
                'name' => $product['name'],
                'qty' => $qty,
                'price' => $product['price'],
                'total' => $lineTotal,
            ];
        }

        if (empty($items)) {
            throw new Exception('Cart is empty');
        }

        $grandTotal = $subtotal;
        $invoiceNumber = generateInvoiceNumber();

        $stmt = $db->prepare("INSERT INTO orders (invoice_number, customer_name, customer_phone, customer_address, customer_note, subtotal, discount, grand_total, status) VALUES (?,?,?,?,?,?,0,?, 'Pending')");
        $stmt->bind_param('sssssdd', $invoiceNumber, $customerName, $customerPhone, $customerAddress, $customerNote, $subtotal, $grandTotal);
        $stmt->execute();
        $orderId = $db->insert_id;

        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, total_price) VALUES (?,?,?,?,?,?)");
        foreach ($items as $it) {
            $stmt->bind_param('iisidd', $orderId, $it['product_id'], $it['name'], $it['qty'], $it['price'], $it['total']);
            $stmt->execute();
        }

        $db->commit();

        return ['success' => true, 'order_id' => $orderId, 'invoice_number' => $invoiceNumber];

    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getOrder($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getOrderItems($orderId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id=?");
    $stmt->bind_param('i', $orderId);
    $stmt->execute();
    $res = $stmt->get_result();
    $out = [];
    while ($row = $res->fetch_assoc()) $out[] = $row;
    return $out;
}

function updateOrderStatus($id, $status) {
    $db = getDB();
    $allowed = ['Pending', 'Confirmed', 'Processing', 'Ready', 'Completed', 'Cancelled'];
    if (!in_array($status, $allowed)) return false;

    $current = getOrder($id);
    if (!$current) return false;

    if ($current['status'] !== 'Confirmed' && $status === 'Confirmed') {
        foreach (getOrderItems($id) as $it) {
            if ($it['product_id']) {
                $db->query("UPDATE products SET stock = GREATEST(stock - " . (int)$it['quantity'] . ", 0) WHERE id=" . (int)$it['product_id']);
            }
        }
    }

    $stmt = $db->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    return true;
}
