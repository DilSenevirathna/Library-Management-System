<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Manage Reservations";
require_once '../includes/header.php';

// Handle reservation actions
if (isset($_GET['action']) ){
    $reservation_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    try {
        $pdo->beginTransaction();
        
        // Get reservation details
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
        $stmt->execute([$reservation_id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            throw new Exception("Reservation not found");
        }
        
        if ($action === 'fulfill') {
            // Mark as fulfilled when book is available
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'fulfilled' WHERE reservation_id = ?");
            $stmt->execute([$reservation_id]);
            
            // In a real app, you would notify the user here
            $_SESSION['success'] = "Reservation marked as fulfilled";
            
        } elseif ($action === 'cancel') {
            // Cancel the reservation
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
            $stmt->execute([$reservation_id]);
            $_SESSION['success'] = "Reservation cancelled";
        }
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error processing reservation: " . $e->getMessage();
    }
    
    header('Location: reservations.php');
    exit;
}

// Get all reservations with book and user details
$stmt = $pdo->query("SELECT r.*, b.title, b.isbn, u.full_name, u.username, u.email
                    FROM reservations r
                    JOIN books b ON r.book_id = b.book_id
                    JOIN users u ON r.user_id = u.user_id
                    ORDER BY r.reservation_date DESC");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <h2><i class="fas fa-bookmark"></i> Manage Reservations</h2>
    
    <div class="card mt-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="reservationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Book</th>
                            <th>User</th>
                            <th>Reservation Date</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= $reservation['reservation_id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($reservation['title']) ?><br>
                                    <small class="text-muted">ISBN: <?= htmlspecialchars($reservation['isbn']) ?></small>
                                </td>
                                <td>
                                    <?= htmlspecialchars($reservation['full_name']) ?><br>
                                    <small class="text-muted"><?= htmlspecialchars($reservation['email']) ?></small>
                                </td>
                                <td><?= date('M d, Y', strtotime($reservation['reservation_date'])) ?></td>
                                <td><?= $reservation['notes'] ? htmlspecialchars($reservation['notes']) : '--' ?></td>
                                <td>
                                    <span class="badge <?= 
                                        $reservation['status'] == 'pending' ? 'bg-warning' : 
                                        ($reservation['status'] == 'fulfilled' ? 'bg-success' : 'bg-secondary')
                                    ?>">
                                        <?= ucfirst($reservation['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($reservation['status'] == 'pending'): ?>
                                        <a href="?action=fulfill&id=<?= $reservation['reservation_id'] ?>" 
                                           class="btn btn-sm btn-success"
                                           onclick="return confirm('Mark this reservation as fulfilled?')">
                                            <i class="fas fa-check"></i> Fulfill
                                        </a>
                                        <a href="?action=cancel&id=<?= $reservation['reservation_id'] ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Cancel this reservation?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#reservationsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [6] } // Disable sorting for actions column
        ]
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>