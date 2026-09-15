<?php
require_once __DIR__ . '/functions.php';

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        $pos = strpos($scriptDir, '/admin');
        $base = $pos !== false ? substr($scriptDir, 0, $pos) : $scriptDir;
        redirect($base . '/login.php');
    }
}

function adminLogin($email, $password) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($admin = $res->fetch_assoc()) {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            return true;
        }
    }
    return false;
}
