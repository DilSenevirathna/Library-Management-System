<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Admin Dashboard";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>

    <!-- Stats Cards -->
    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Books</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM books");
                            $total_books = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <h2 class="mb-0"><?= $total_books ?></h2>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional stat cards (Users, Borrowed, Overdue) -->
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Users</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
                            $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <h2 class="mb-0"><?= $total_users ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Borrowed Books</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'issued'");
                            $borrowed_books = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <h2 class="mb-0"><?= $borrowed_books ?></h2>
                        </div>
                        <i class="fas fa-exchange-alt fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Overdue Books</h5>
                            <?php
                            $stmt = $pdo->query("SELECT COUNT(*) as total FROM transactions WHERE status = 'overdue'");
                            $overdue_books = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            ?>
                            <h2 class="mb-0"><?= $overdue_books ?></h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-history"></i> Recent Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Book</th>
                                    <th>User</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT t.*, b.title, u.full_name 
                                                     FROM transactions t
                                                     JOIN books b ON t.book_id = b.book_id
                                                     JOIN users u ON t.user_id = u.user_id
                                                     ORDER BY t.due_date DESC LIMIT 5");
                                while ($transaction = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transaction['title']) ?></td>
                                        <td><?= htmlspecialchars($transaction['full_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($transaction['due_date'])) ?></td>
                                        <td>
                                            <span class="badge <?=
                                                $transaction['status'] == 'issued' ? 'bg-warning' :
                                                ($transaction['status'] == 'overdue' ? 'bg-danger' : 'bg-success')
                                                ?>">
                                                <?= ucfirst($transaction['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="transactions.php" class="btn btn-primary btn-sm">View All</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-user-clock"></i> Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                                while ($user = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                                        <td><?= htmlspecialchars($user['username']) ?></td>
                                        <td><?= ucfirst($user['user_type']) ?></td>
                                        <td>
                                            <span
                                                class="badge <?= $user['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= ucfirst($user['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="users.php" class="btn btn-primary btn-sm">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>