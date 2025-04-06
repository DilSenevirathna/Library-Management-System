<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Library Reports";
require_once '../includes/header.php';

// Default report period (last 30 days)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get report data
$stmt = $pdo->prepare("SELECT 
                        COUNT(*) AS total_books,
                        SUM(CASE WHEN available_quantity > 0 THEN 1 ELSE 0 END) AS available_books,
                        SUM(CASE WHEN available_quantity = 0 THEN 1 ELSE 0 END) AS unavailable_books
                      FROM books");
$stmt->execute();
$books_stats = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT 
                        COUNT(*) AS total_users,
                        SUM(CASE WHEN user_type = 'admin' THEN 1 ELSE 0 END) AS admin_users,
                        SUM(CASE WHEN user_type = 'librarian' THEN 1 ELSE 0 END) AS librarian_users,
                        SUM(CASE WHEN user_type = 'member' THEN 1 ELSE 0 END) AS member_users
                      FROM users");
$stmt->execute();
$users_stats = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT 
                        COUNT(*) AS total_transactions,
                        SUM(CASE WHEN status = 'issued' THEN 1 ELSE 0 END) AS issued_books,
                        SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS overdue_books,
                        SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) AS returned_books,
                        SUM(fine_amount) AS total_fines
                      FROM transactions
                      WHERE issue_date BETWEEN ? AND ?");
$stmt->execute([$start_date, $end_date]);
$transactions_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// Get most popular books
$stmt = $pdo->prepare("SELECT b.title, b.author, COUNT(t.transaction_id) AS borrow_count
                      FROM books b
                      LEFT JOIN transactions t ON b.book_id = t.book_id
                      WHERE t.issue_date BETWEEN ? AND ?
                      GROUP BY b.book_id
                      ORDER BY borrow_count DESC
                      LIMIT 5");
$stmt->execute([$start_date, $end_date]);
$popular_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get most active users
$stmt = $pdo->prepare("SELECT u.full_name, u.username, COUNT(t.transaction_id) AS borrow_count
                      FROM users u
                      LEFT JOIN transactions t ON u.user_id = t.user_id
                      WHERE t.issue_date BETWEEN ? AND ? AND u.user_type = 'member'
                      GROUP BY u.user_id
                      ORDER BY borrow_count DESC
                      LIMIT 5");
$stmt->execute([$start_date, $end_date]);
$active_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2><i class="fas fa-chart-bar"></i> Library Reports</h2>
    
    <!-- Date Range Filter -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5>Report Period</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?= htmlspecialchars($start_date) ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?= htmlspecialchars($end_date) ?>" max="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Summary Stats -->
    <div class="row mt-4">
        <div class="col-md-4 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Books Summary</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total Books: <?= $books_stats['total_books'] ?></p>
                            <p class="mb-1">Available: <?= $books_stats['available_books'] ?></p>
                            <p class="mb-0">Unavailable: <?= $books_stats['unavailable_books'] ?></p>
                        </div>
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Users Summary</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total Users: <?= $users_stats['total_users'] ?></p>
                            <p class="mb-1">Admins: <?= $users_stats['admin_users'] ?></p>
                            <p class="mb-1">Librarians: <?= $users_stats['librarian_users'] ?></p>
                            <p class="mb-0">Members: <?= $users_stats['member_users'] ?></p>
                        </div>
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Transactions Summary</h5>
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1">Total: <?= $transactions_stats['total_transactions'] ?></p>
                            <p class="mb-1">Issued: <?= $transactions_stats['issued_books'] ?></p>
                            <p class="mb-1">Overdue: <?= $transactions_stats['overdue_books'] ?></p>
                            <p class="mb-0">Fines: $<?= number_format($transactions_stats['total_fines'], 2) ?></p>
                        </div>
                        <i class="fas fa-exchange-alt fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popular Books -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5>Most Popular Books</h5>
        </div>
        <div class="card-body">
            <?php if (count($popular_books) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Borrow Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popular_books as $index => $book): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= $book['borrow_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No borrowing data available for the selected period.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Active Users -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5>Most Active Users</h5>
        </div>
        <div class="card-body">
            <?php if (count($active_users) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Borrow Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($active_users as $index => $user): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                                    <td>@<?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= $user['borrow_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No user activity data available for the selected period.</div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Export Options -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5>Export Reports</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="export_reports.php?type=transactions&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                       class="btn btn-success w-100">
                        <i class="fas fa-file-excel"></i> Export Transactions
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="export_reports.php?type=books&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                       class="btn btn-success w-100">
                        <i class="fas fa-file-excel"></i> Export Books
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="export_reports.php?type=users&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" 
                       class="btn btn-success w-100">
                        <i class="fas fa-file-excel"></i> Export Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>