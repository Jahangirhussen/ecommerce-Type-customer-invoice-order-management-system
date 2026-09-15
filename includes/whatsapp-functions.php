<?php
require_once __DIR__ . '/functions.php';

function buildWhatsAppMessage($order, $items) {
    $shop = getSettings();
    $shopName = $shop['shop_name'] ?? 'Shop';

    $msg = "New Product Order\n\n";
    $msg .= "Invoice: {$order['invoice_number']}\n";
    $msg .= "Shop: {$shopName}\n\n";

    $n = 1;
    foreach ($items as $it) {
        $msg .= "{$n}. {$it['product_name']}\n";
        $msg .= "Qty: {$it['quantity']}\n";
        $msg .= "Price: " . money($it['unit_price']) . "\n";
        $msg .= "Total: " . money($it['total_price']) . "\n\n";
        $n++;
    }

    $msg .= "----------------\n";
    $msg .= "Grand Total: " . money($order['grand_total']) . "\n\n";
    $msg .= "Customer:\n";
    $msg .= "Name: {$order['customer_name']}\n";
    $msg .= "Phone: {$order['customer_phone']}\n";
    if (!empty($order['customer_address'])) $msg .= "Address: {$order['customer_address']}\n";
    if (!empty($order['customer_note'])) $msg .= "Note: {$order['customer_note']}\n";
    $msg .= "\nPlease prepare the above products.";

    return $msg;
}

function buildWhatsAppLink($order, $items) {
    $shop = getSettings();
    $phone = preg_replace('/\D+/', '', $shop['whatsapp_number'] ?? '');
    $message = buildWhatsAppMessage($order, $items);
    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);
}
