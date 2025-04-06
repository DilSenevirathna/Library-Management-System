<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/notifications.php';

if (!isLoggedIn()) {
    $_SESSION['error'] = "You must be logged in to reserve books.";
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['book_id'])) {
    $_SESSION['error'] = "Invalid request.";
    header('Location: books.php');
    exit;
}

$book_id = intval($_POST['book_id']);
$user_id = $_SESSION['user_id'];
$notes = isset($_POST['reservation_notes']) ? trim($_POST['reservation_notes']) : '';
$reservation_date = date('Y-m-d');

try {
    $pdo->beginTransaction();
    
    // Check if user already borrowed this book
    $stmt = $pdo->prepare("SELECT transaction_id FROM transactions 
                          WHERE book_id = ? AND user_id = ? AND status IN ('issued', 'overdue')");
    $stmt->execute([$book_id, $user_id]);
    
    if ($stmt->fetch()) {
        throw new Exception("You already have this book borrowed.");
    }

    // Check book availability and existing reservations
    $stmt = $pdo->prepare("SELECT 
                          b.available_quantity,
                          (SELECT COUNT(*) FROM reservations r 
                           WHERE r.book_id = b.book_id AND r.user_id = ? AND r.status = 'pending') AS user_reserved
                          FROM books b 
                          WHERE b.book_id = ?");
    $stmt->execute([$user_id, $book_id]);
    $book_status = $stmt->fetch();
    
    if (!$book_status) {
        throw new Exception("Book not found.");
    }
    
    if ($book_status['user_reserved'] > 0) {
        throw new Exception("You already have a pending reservation for this book.");
    }
    
    if ($book_status['available_quantity'] > 0) {
        throw new Exception("This book is available. Please borrow it directly.");
    }
    
    // Get queue position
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations 
                          WHERE book_id = ? AND status = 'pending'");
    $stmt->execute([$book_id]);
    $queue_position = $stmt->fetchColumn() + 1;
    
    // Create reservation
    $stmt = $pdo->prepare("INSERT INTO reservations 
                          (book_id, user_id, reservation_date, notes, status, queue_position) 
                          VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$book_id, $user_id, $reservation_date, $notes, $queue_position]);
    
    $pdo->commit();
    
    $_SESSION['success'] = "Reservation placed successfully! You are #$queue_position in queue.";
    header('Location: books.php');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = $e->getMessage();
    header('Location: books.php');
    exit;
}
?>