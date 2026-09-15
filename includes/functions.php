<?php
require_once __DIR__ . '/database.php';

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function money($num) {
    return '৳' . number_format((float)$num, 2);
}

function getSettings() {
    $db = getDB();
    $res = $db->query("SELECT * FROM settings LIMIT 1");
    return $res ? $res->fetch_assoc() : [];
}

function getCategories($onlyActive = true) {
    $db = getDB();
    $sql = "SELECT c.*, p.name AS parent_name FROM categories c LEFT JOIN categories p ON p.id = c.parent_id"
         . ($onlyActive ? " WHERE c.status = 1" : "") . " ORDER BY c.name ASC";
    $res = $db->query($sql);
    $out = [];
    while ($row = $res->fetch_assoc()) $out[] = $row;
    return $out;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text ?: 'n-a';
}

function redirect($path) {
    header("Location: $path");
    exit;
}

function flash($key, $msg = null) {
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return;
    }
    if (isset($_SESSION['flash'][$key])) {
        $val = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $val;
    }
    return null;
}
