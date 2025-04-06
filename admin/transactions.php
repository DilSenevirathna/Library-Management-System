<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/notifications.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Manage Transactions";
require_once '../includes/header.php';

// Handle return action
if (isset($_GET['return']) && is_numeric($_GET['return'])) {
    $transaction_id = intval($_GET['return']);
    
    try {
        $pdo->beginTransaction();
        
        // Get transaction details
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$transaction) {
            throw new Exception("Transaction not found!");
        }
        
        $return_date = date('Y-m-d');
        $due_date = $transaction['due_date'];
        $fine_amount = 0;
        
        // Calculate fine if overdue
        if (strtotime($return_date) > strtotime($due_date)) {
            $days_overdue = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));
            $fine_amount = $days_overdue * FINE_PER_DAY;
        }
        
        // Update transaction
        $stmt = $pdo->prepare("UPDATE transactions 
                              SET return_date = ?, fine_amount = ?, status = 'returned' 
                              WHERE transaction_id = ?");
        $stmt->execute([$return_date, $fine_amount, $transaction_id]);
        
        // Update book quantity
        $stmt = $pdo->prepare("UPDATE books 
                              SET available_quantity = available_quantity + 1 
                              WHERE book_id = ?");
        $stmt->execute([$transaction['book_id']]);
        
        // Add fine if applicable
        if ($fine_amount > 0) {
            $stmt = $pdo->prepare("INSERT INTO fines (user_id, transaction_id, amount, reason, status) 
                                  VALUES (?, ?, ?, ?, 'unpaid')");
            $stmt->execute([
                $transaction['user_id'],
                $transaction_id,
                $fine_amount,
                "Overdue fine for $days_overdue days"
            ]);
        }
        
        // Check for and fulfill pending reservations for this book
        $stmt = $pdo->prepare("SELECT r.reservation_id, r.user_id 
                              FROM reservations r
                              WHERE r.book_id = ? AND r.status = 'pending'
                              ORDER BY r.reservation_date ASC
                              LIMIT 1");
        $stmt->execute([$transaction['book_id']]);
        $reservation = $stmt->fetch();
        
        if ($reservation) {
            // Mark as ready for pickup and send notification
            $stmt = $pdo->prepare("UPDATE reservations 
                                  SET status = 'ready', notification_sent = 0 
                                  WHERE reservation_id = ?");
            $stmt->execute([$reservation['reservation_id']]);
            
            // Send notification to next user in queue
            sendReservationAvailableNotification($reservation['user_id'], $transaction['book_id']);
            
            // Update queue positions for remaining reservations
            $stmt = $pdo->prepare("UPDATE reservations 
                                  SET queue_position = queue_position - 1 
                                  WHERE book_id = ? AND status = 'pending'");
            $stmt->execute([$transaction['book_id']]);
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Book returned successfully!" . 
                             ($fine_amount > 0 ? " Fine of $" . number_format($fine_amount, 2) . " applied." : "") .
                             ($reservation ? " Next reservation has been notified." : "");
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error processing return: " . $e->getMessage();
    }
    
    redirect('transactions.php');
}

// Handle status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$where_clause = '';
$params = [];

if ($status_filter !== 'all') {
    if ($status_filter === 'overdue') {
        $where_clause = "WHERE t.status = 'issued' AND t.due_date < CURDATE()";
    } else {
        $where_clause = "WHERE t.status = ?";
        $params[] = $status_filter;
    }
}

// Get all transactions with book and user details
$stmt = $pdo->prepare("SELECT t.*, b.title, b.isbn, u.full_name, u.username, 
                      (SELECT COUNT(*) FROM reservations r WHERE r.book_id = b.book_id AND r.status = 'pending') AS pending_reservations,
                      CASE 
                          WHEN t.status = 'issued' AND t.due_date < CURDATE() THEN 'overdue'
                          ELSE t.status
                      END AS display_status
                      FROM transactions t
                      JOIN books b ON t.book_id = b.book_id
                      JOIN users u ON t.user_id = u.user_id
                      $where_clause
                      ORDER BY t.issue_date DESC");
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<style>

    /* Gradient button for Return */
    .btn-gradient-success {
        background: linear-gradient(to right, #00c853, #64dd17);
        border: none;
        color: white;
        transition: all 0.3s ease-in-out;
    }
    .btn-gradient-success:hover {
        background: linear-gradient(to right, #64dd17, #00c853);
        color: white;
    }

    /* Gradient status badges */
    .badge-status-issued {
        background: linear-gradient(to right, #ff9800, #ffc107);
        color: #fff;
    }

    .badge-status-overdue {
        background: linear-gradient(to right, #d50000, #ff1744);
        color: #fff;
    }

    .badge-status-returned {
        background: linear-gradient(to right, #00c853, #64dd17);
        color: #fff;
    }
</style>



<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-exchange-alt"></i> Manage Transactions</h2>
        <div>
            <a href="issue_book.php" class="btn btn-gradient-primary">
                <i class="fas fa-book-medical"></i> Issue New Book
            </a>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter by Status:</label>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Transactions</option>
                        <option value="issued" <?= $status_filter === 'issued' ? 'selected' : '' ?>>Currently Issued</option>
                        <option value="overdue" <?= $status_filter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
                        <option value="returned" <?= $status_filter === 'returned' ? 'selected' : '' ?>>Returned</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="transactionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Book</th>
                            <th>User</th>
                            <th>Issue Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Fine</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= $transaction['transaction_id'] ?></td>
                                <td>
                                    <?= htmlspecialchars($transaction['title']) ?><br>
                                    <small class="text-muted">ISBN: <?= htmlspecialchars($transaction['isbn']) ?></small>
                                    <?php if ($transaction['pending_reservations'] > 0): ?>
                                        <br><span class="badge bg-warning">
                                            <?= $transaction['pending_reservations'] ?> reservation(s)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($transaction['full_name']) ?><br>
                                    <small class="text-muted">@<?= htmlspecialchars($transaction['username']) ?></small>
                                </td>
                                <td><?= date('M d, Y', strtotime($transaction['issue_date'])) ?></td>
                                <td>
                                    <?= date('M d, Y', strtotime($transaction['due_date'])) ?>
                                    <?php if ($transaction['display_status'] === 'overdue'): ?>
                                        <br><small class="text-danger">Overdue!</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $transaction['return_date'] ? date('M d, Y', strtotime($transaction['return_date'])) : '--' ?>
                                </td>
                                <td>
                                    <?= $transaction['fine_amount'] > 0 ? '$' . number_format($transaction['fine_amount'], 2) : '--' ?>
                                </td>
                                <td>
                               <?php
                                          $statusClass = '';
                                               switch ($transaction['display_status']) {
                                                     case 'issued':
                                                     $statusClass = 'badge-status-issued';
                                                           break;
                                                     case 'overdue':
                                                     $statusClass = 'badge-status-overdue';
                                                            break;
                                                     case 'returned':
                                                     $statusClass = 'badge-status-returned';
                                                         break;
                                       }
                                ?>
                              <span class="badge <?= $statusClass ?>">
                                     <?= ucfirst($transaction['display_status']) ?>
                              </span>
                               </td>

                                <td>
                                    <?php if ($transaction['status'] !== 'returned'): ?>
                                        <a href="?return=<?= $transaction['transaction_id'] ?>" 
                                           class="btn btn-sm btn-gradient-success"
                                           onclick="return confirm('Mark this book as returned?')">
                                            <i class="fas fa-check"></i> Return
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

<!-- Initialize DataTables -->
<script>
$(document).ready(function() {
    $('#transactionsTable').DataTable({
        responsive: true,
        order: [[0, 'desc']],
        columnDefs: [
            { orderable: false, targets: [8] } // Disable sorting for actions column
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search transactions..."
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>