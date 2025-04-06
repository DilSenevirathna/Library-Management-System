<?php
// Ensure no output is sent before headers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'library_management_system');

// Base URL
define('BASE_URL', 'http://localhost/lms');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin()
{
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

// Redirect function
function redirect($url)
{
    // Clear output buffer before redirect
    if (ob_get_length()) {
        ob_end_clean();
    }
    header("Location: $url");
    exit();
}

// Email and fine configuration
define('LIBRARY_EMAIL', 'library@yourdomain.com');
define('NOTIFICATION_EMAIL_ENABLED', true);
define('FINE_PER_DAY', 1.00);
?>