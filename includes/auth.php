<?php
require_once 'config.php';

// Handle user registration
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $user_type = 'member'; // Default user type

    // Validate inputs
    if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
        $_SESSION['error'] = "All fields are required!";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, full_name, user_type) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $email, $full_name, $user_type])) {
                $_SESSION['success'] = "Registration successful! Please login.";
                redirect('login.php');
            } else {
                $_SESSION['error'] = "Registration failed!";
            }
        }
    }
}

// Handle user login
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username and password are required!";
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['full_name'] = $user['full_name'];

                // Redirect based on user type
                if ($user['user_type'] === 'admin') {
                    redirect('admin/dashboard.php');
                } else {
                    redirect('user/dashboard.php');
                }
            } else {
                $_SESSION['error'] = "Invalid password!";
            }
        } else {
            $_SESSION['error'] = "User not found!";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    redirect('login.php');
}
?>