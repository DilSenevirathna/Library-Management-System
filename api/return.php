<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;

if ($transaction_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction ID']);
    exit;
}

try {
    // Get transaction details
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
    $stmt->execute([$transaction_id, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }

    if ($transaction['status'] === 'returned') {
        echo json_encode(['success' => false, 'message' => 'Book already returned']);
        exit;
    }

    // Calculate fine if overdue
    $return_date = date('Y-m-d');
    $due_date = $transaction['due_date'];
    $fine_amount = 0;

    if (strtotime($return_date) > strtotime($due_date)) {
        $days_overdue = floor((strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24));
        $fine_amount = $days_overdue * 1.00; // $1 per day fine
    }

    // Start transaction
    $pdo->beginTransaction();

    // Update transaction
    $stmt = $pdo->prepare("UPDATE transactions SET return_date = ?, fine_amount = ?, status = 'returned' WHERE transaction_id = ?");
    $stmt->execute([$return_date, $fine_amount, $transaction_id]);

    // Update book quantity
    $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity + 1 WHERE book_id = ?");
    $stmt->execute([$transaction['book_id']]);

    // If there's a fine, add to fines table
    if ($fine_amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO fines (user_id, transaction_id, amount, reason) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $transaction_id, $fine_amount, "Overdue fine for {$days_overdue} days"]);
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Book returned successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>