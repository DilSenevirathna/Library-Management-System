<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid reservation ID";
    redirect('reservations.php');
}

$reservation_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();
    
    // Verify reservation belongs to user
    $stmt = $pdo->prepare("SELECT book_id, queue_position FROM reservations 
                          WHERE reservation_id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $user_id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        throw new Exception("Reservation not found or doesn't belong to you");
    }
    
    // Cancel the reservation
    $stmt = $pdo->prepare("UPDATE reservations 
                          SET status = 'cancelled' 
                          WHERE reservation_id = ?");
    $stmt->execute([$reservation_id]);
    
    // Update queue positions for others
    $stmt = $pdo->prepare("UPDATE reservations 
                          SET queue_position = queue_position - 1 
                          WHERE book_id = ? 
                          AND status = 'pending' 
                          AND queue_position > ?");
    $stmt->execute([$reservation['book_id'], $reservation['queue_position']]);
    
    $pdo->commit();
    $_SESSION['success'] = "Reservation cancelled successfully";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error cancelling reservation: " . $e->getMessage();
}

redirect('reservations.php');
?>