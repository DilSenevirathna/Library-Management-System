<?php
// Handle logout request
if (isset($_GET['logout'])) {
    require_once 'includes/config.php';
    
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    
    // Redirect to login page
    header("Location: " . BASE_URL . "/login.php");
    exit();
}
?>



<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Book_Bridge - Library Management System' ?></title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/dark-mode.css" id="dark-mode-style" disabled>
    
    <style>
        /* Fix for dropdown visibility */
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        .dropdown-menu {
            margin-top: 0;
        }
        .navbar-nav .dropdown-menu {
            position: absolute;
        }


    /* Navbar Gradient Background */
.navbar {
    background: linear-gradient(135deg,rgb(150, 17, 203) 0%, #2575fc 100%);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    padding: 0.8rem 0;
}

/* Navbar Brand */
.navbar-brand {
    font-weight: 600;
    font-size: 1.3rem;
    transition: all 0.3s ease;
    padding: 0.5rem 0;
}

.navbar-brand i {
    margin-right: 8px;
    color: rgba(255, 255, 255, 0.9);
}

/* Nav Links */
.nav-link {
    color: rgba(255, 255, 255, 0.85) !important;
    font-weight: 500;
    padding: 0.5rem 1rem !important;
    margin: 0 2px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.nav-link i {
    margin-right: 6px;
}

.nav-link:hover {
    color: white !important;
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

/* Active Nav Link */
.nav-link.active {
    background: rgba(255, 255, 255, 0.25);
    color: white !important;
    font-weight: 600;
}

/* Dropdown Menu */
.dropdown-menu {
    border: none;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    margin-top: 8px;
    background: white;
}

.dropdown-item {
    padding: 0.5rem 1.25rem;
    font-weight: 500;
    color: #4a5568;
    transition: all 0.2s ease;
}

.dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 8px;
    color: #6a11cb;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f6f7ff 0%, #e9f0ff 100%);
    color: #2d3748;
    transform: translateX(3px);
}

.dropdown-divider {
    border-color: rgba(106, 17, 203, 0.1);
}

/* User Dropdown Specific */
.navbar-nav .dropdown-menu-end {
    right: 0;
    left: auto;
}

/* Toggler Icon */
.navbar-toggler {
    border-color: rgba(255, 255, 255, 0.2);
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
}

/* Alerts */
.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.alert-danger {
    background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%);
    color: white;
}

.alert-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.btn-close {
    filter: invert(1);
}

/* Responsive Adjustments */
@media (max-width: 992px) {
    .navbar-collapse {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        padding: 1rem;
        border-radius: 10px;
        margin-top: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .nav-link {
        margin: 3px 0;
        padding: 0.75rem 1rem !important;
    }
    
    .dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
        margin-left: 1rem;
        width: calc(100% - 2rem);
    }
}








    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fas fa-book"></i> Library Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>"><i class="fas fa-home"></i> Home</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-tachometer-alt"></i> Admin
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/books.php"><i class="fas fa-book"></i> Manage Books</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/users.php"><i class="fas fa-users"></i> Manage Users</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/transactions.php"><i class="fas fa-exchange-alt"></i> Transactions</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/reservations.php"><i class="fas fa-bookmark"></i> Reservations</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/reports.php"><i class="fas fa-chart-pie"></i> Reports</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/user/books.php"><i class="fas fa-book-open"></i>Books Catelog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= BASE_URL ?>/user/reservations.php"><i class="fas fa-bookmark"></i> My Reservations</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['full_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/user/dashboard.php"><i class="fas fa-bookmark"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="#" id="dark-mode-toggle"><i class="fas fa-moon"></i> Dark Mode</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/register.php"><i class="fas fa-user-plus"></i> Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>