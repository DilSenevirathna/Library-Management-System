<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Only admin can access this page
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['transaction_id'])) {
    $_SESSION['error'] = "No transaction specified";
    header('Location: transactions.php');
    exit;
}

$transaction_id = intval($_GET['transaction_id']);

try {
    $pdo->beginTransaction();
    
    // 1. Get transaction details
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
    $stmt->execute([$transaction_id]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        throw new Exception("Transaction not found");
    }
    
    // 2. Calculate fine if overdue
    $return_date = date('Y-m-d');
    $due_date = $transaction['due_date'];
    $fine_amount = 0;
    
    if (strtotime($return_date) > strtotime($due_date)) {
        $days_overdue = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));
        $fine_amount = $days_overdue * FINE_PER_DAY; // Define this in config.php
    }
    
    // 3. Update transaction as returned
    $stmt = $pdo->prepare("UPDATE transactions 
                          SET return_date = ?, fine_amount = ?, status = 'returned' 
                          WHERE transaction_id = ?");
    $stmt->execute([$return_date, $fine_amount, $transaction_id]);
    
    // 4. Update book quantity
    $stmt = $pdo->prepare("UPDATE books 
                          SET available_quantity = available_quantity + 1 
                          WHERE book_id = ?");
    $stmt->execute([$transaction['book_id']]);
    
    // 5. Add fine record if applicable
    if ($fine_amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO fines 
                              (user_id, transaction_id, amount, reason, status) 
                              VALUES (?, ?, ?, ?, 'unpaid')");
        $stmt->execute([
            $transaction['user_id'],
            $transaction_id,
            $fine_amount,
            "Overdue fine for $days_overdue days"
        ]);
    }
    
    // 6. Check for pending reservations
    $stmt = $pdo->prepare("SELECT r.reservation_id, r.user_id 
                          FROM reservations r
                          WHERE r.book_id = ? AND r.status = 'pending'
                          ORDER BY r.reservation_date ASC
                          LIMIT 1");
    $stmt->execute([$transaction['book_id']]);
    $reservation = $stmt->fetch();
    
    if ($reservation) {
        // Mark as ready for pickup
        $stmt = $pdo->prepare("UPDATE reservations 
                              SET status = 'ready', notification_sent = 0 
                              WHERE reservation_id = ?");
        $stmt->execute([$reservation['reservation_id']]);
        
        // Here you would typically send a notification
    }
    
    $pdo->commit();
    
    $_SESSION['success'] = "Book returned successfully!" . 
                         ($fine_amount > 0 ? " Fine of $" . number_format($fine_amount, 2) . " applied." : "") .
                         ($reservation ? " Next reservation notified." : "");
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error processing return: " . $e->getMessage();
}

header('Location: transactions.php');
exit;