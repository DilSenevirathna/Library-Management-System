<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Validate & sanitize input
$full_name = trim($_POST['full_name'] ?? '');
$username  = trim($_POST['username'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = trim($_POST['password'] ?? '');
$user_type = trim($_POST['user_type'] ?? '');

if (!$full_name || !$username || !$email || !$password || !$user_type) {
    redirect('add_user.php?error=Please fill all fields.');
}

// Check if username or email already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);

if ($stmt->fetch()) {
    redirect('add_user.php?error=Username or email already exists.');
}

// Insert into database
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (full_name, username, email, password, user_type, status) VALUES (?, ?, ?, ?, ?, 'active')");

if ($stmt->execute([$full_name, $username, $email, $hashedPassword, $user_type])) {
    $_SESSION['success'] = "New user added successfully!";
    redirect('users.php');
} else {
    redirect('add_user.php?error=Something went wrong. Try again.');
}
