<?php
// ============================================================
//  Database Configuration
//  includes/config.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');          // change if you have a password
define('DB_NAME', 'food_donation_db');
define('SITE_NAME', 'FoodShare BD');

// Connect to MySQL
$conn = mysqli_connect(DB_HOST, DB_USER, "", DB_NAME);

if (!$conn) {
    die("<h2 style='color:red;text-align:center;margin-top:50px;'>
        Database connection failed: " . mysqli_connect_error() . "<br>
        Please check includes/config.php
    </h2>");
}

mysqli_set_charset($conn, 'utf8mb4');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper: redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper: check login
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('/food_system/login.php');
    }
}

// Helper: check role
function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        redirect('/food_system/login.php');
    }
}

// Helper: sanitize input
function clean($conn, $val) {
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($val)));
}

// Helper: time ago
function timeAgo($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d > 0) return $diff->d . "d ago";
    if ($diff->h > 0) return $diff->h . "h ago";
    return $diff->i . "m ago";
}
?>
