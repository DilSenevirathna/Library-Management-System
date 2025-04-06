<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = "User Dashboard";
require_once '../includes/header.php';

// Get user's borrowed books
$stmt = $pdo->prepare("SELECT t.*, b.title, b.author, b.cover_image 
                      FROM transactions t
                      JOIN books b ON t.book_id = b.book_id
                      WHERE t.user_id = ? AND t.status IN ('issued', 'overdue')
                      ORDER BY t.due_date");
$stmt->execute([$_SESSION['user_id']]);
$borrowed_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's reservation history
$stmt = $pdo->prepare("SELECT r.*, b.title, b.author 
                      FROM reservations r
                      JOIN books b ON r.book_id = b.book_id
                      WHERE r.user_id = ?
                      ORDER BY r.reservation_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2><i class="fas fa-tachometer-alt"></i> User Dashboard</h2>

    <!-- Welcome Message -->
    <div class="alert alert-primary mt-4">
        <h4>Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?>!</h4>
        <p class="mb-0">Here you can manage your borrowed books and reservations.</p>
    </div>

    <!-- Borrowed Books Section -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-book-open"></i> Your Borrowed Books</h5>
        </div>
        <div class="card-body">
            <?php if (count($borrowed_books) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrowed_books as $book): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/images/book-covers/<?= $book['cover_image'] ?: 'default.jpg' ?>"
                                            alt="Cover" style="width: 50px;">
                                    </td>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= date('M d, Y', strtotime($book['due_date'])) ?></td>
                                    <td>
                                        <span class="badge <?=
                                            $book['status'] == 'issued' ? 'bg-warning' : 'bg-danger'
                                            ?>">
                                            <?= ucfirst($book['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success return-btn"
                                            data-transaction-id="<?= $book['transaction_id'] ?>">
                                            <i class="fas fa-undo"></i> Return
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You don't have any borrowed books at the moment.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reservations Section -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-bookmark"></i> Your Reservations</h5>
        </div>
        <div class="card-body">
            <?php if (count($reservations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Reservation Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td><?= htmlspecialchars($reservation['title']) ?></td>
                                    <td><?= htmlspecialchars($reservation['author']) ?></td>
                                    <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                                    <td>
                                        <span class="badge <?=
                                            $reservation['status'] == 'pending' ? 'bg-warning' :
                                            ($reservation['status'] == 'fulfilled' ? 'bg-success' : 'bg-secondary')
                                            ?>">
                                            <?= ucfirst($reservation['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You don't have any active reservations.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>