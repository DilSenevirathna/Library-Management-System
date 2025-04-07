<?php
require_once 'includes/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log logout activity
if (isset($_SESSION['user_id'])) {
    $logMessage = "User " . $_SESSION['username'] . " logged out";
    file_put_contents('logs/logout.log', date('Y-m-d H:i:s') . " - " . $logMessage . PHP_EOL, FILE_APPEND);
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Clear any remember me cookies
setcookie('remember_token', '', time() - 3600, '/');

// Redirect to login page with success message
$_SESSION['success'] = "You have been logged out successfully.";
header("Location: /lms/login.php");
exit();
?>