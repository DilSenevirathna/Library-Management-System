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

$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;

if ($book_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
    exit;
}

try {
    // Check if book is available
    $stmt = $pdo->prepare("SELECT available_quantity FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Book not found']);
        exit;
    }

    if ($book['available_quantity'] <= 0) {
        echo json_encode(['success' => false, 'message' => 'Book is not available']);
        exit;
    }

    // Calculate due date (14 days from now)
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days'));

    // Start transaction
    $pdo->beginTransaction();

    // Create transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (book_id, user_id, issue_date, due_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$book_id, $_SESSION['user_id'], $issue_date, $due_date]);

    // Update book quantity
    $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = ?");
    $stmt->execute([$book_id]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Book borrowed successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>