<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = "My Reservations";
require_once '../includes/header.php';

// Get user's active reservations with book details
$stmt = $pdo->prepare("SELECT r.*, b.title, b.author, b.cover_image, 
                      (SELECT COUNT(*) FROM reservations r2 
                       WHERE r2.book_id = r.book_id AND r2.status = 'pending' 
                       AND r2.reservation_date < r.reservation_date) AS ahead_in_queue
                      FROM reservations r
                      JOIN books b ON r.book_id = b.book_id
                      WHERE r.user_id = ? AND r.status = 'pending'
                      ORDER BY r.queue_position");
$stmt->execute([$_SESSION['user_id']]);
$active_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get reservation history
$stmt = $pdo->prepare("SELECT r.*, b.title, b.author, b.cover_image
                      FROM reservations r
                      JOIN books b ON r.book_id = b.book_id
                      WHERE r.user_id = ? AND r.status != 'pending'
                      ORDER BY r.reservation_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$reservation_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2><i class="fas fa-bookmark"></i> My Reservations</h2>
    
    <!-- Active Reservations -->
    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Active Reservations</h5>
                <span class="badge bg-light text-dark">
                    <?= count($active_reservations) ?> active
                </span>
            </div>
        </div>
        <div class="card-body">
            <?php if (count($active_reservations) > 0): ?>
                <div class="row">
                    <?php foreach ($active_reservations as $reservation): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="../assets/images/book-covers/<?= $reservation['cover_image'] ?: 'default.jpg' ?>" 
                                             class="img-fluid rounded-start h-100" style="object-fit: cover;" 
                                             alt="<?= htmlspecialchars($reservation['title']) ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($reservation['title']) ?></h5>
                                            <p class="card-text">by <?= htmlspecialchars($reservation['author']) ?></p>
                                            
                                            <div class="mb-3">
                                                <span class="badge bg-warning">Queue Position: <?= $reservation['queue_position'] ?></span>
                                                <?php if ($reservation['ahead_in_queue'] > 0): ?>
                                                    <span class="ms-2 text-muted">(<?= $reservation['ahead_in_queue'] ?> ahead of you)</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Reserved on <?= date('M j, Y', strtotime($reservation['reservation_date'])) ?>
                                                </small>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="cancel_reservation.php?id=<?= $reservation['reservation_id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    You have no active reservations. <a href="books.php">Browse books</a> to make a reservation.
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Reservation History -->
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white">
            <h5>Reservation History</h5>
        </div>
        <div class="card-body">
            <?php if (count($reservation_history) > 0): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Book</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservation_history as $reservation): ?>
                                <tr>
                                    <td>
                                        <img src="../assets/images/book-covers/<?= $reservation['cover_image'] ?: 'default.jpg' ?>" 
                                             width="40" class="me-2">
                                        <?= htmlspecialchars($reservation['title']) ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= 
                                            $reservation['status'] == 'fulfilled' ? 'bg-success' : 'bg-secondary'
                                        ?>">
                                            <?= ucfirst($reservation['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($reservation['reservation_date'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No reservation history found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>