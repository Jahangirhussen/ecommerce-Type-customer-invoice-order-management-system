<?php
// ==== DATABASE CONFIGURATION ====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'invoice_system');

// ==== SITE CONFIG ====
define('SITE_URL', ''); // leave blank for relative paths
define('UPLOAD_PATH', __DIR__ . '/uploads/products/');
define('UPLOAD_URL', 'uploads/products/');

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Dhaka');
