<?php
require_once 'config.php';

/**
 * Sends a reservation confirmation email to a user
 * 
 * @param int $user_id The ID of the user making the reservation
 * @param int $book_id The ID of the book being reserved
 * @param int $queue_position The user's position in the reservation queue
 * @return bool True on success, false on failure
 */
function sendReservationConfirmation($user_id, $book_id, $queue_position) {
    global $pdo;
    
    try {
        // Validate input parameters
        if (!is_numeric($user_id) || !is_numeric($book_id) || !is_numeric($queue_position)) {
            throw new InvalidArgumentException("Invalid parameters provided");
        }

        // Get user and book details with proper error handling
        $stmt = $pdo->prepare("SELECT u.email, u.full_name, b.title 
                             FROM users u, books b
                             WHERE u.user_id = ? AND b.book_id = ?");
        if (!$stmt->execute([$user_id, $book_id])) {
            throw new Exception("Failed to fetch user/book details");
        }
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            throw new Exception("User or book not found");
        }

        // Prepare email content
        $to = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$to) {
            throw new Exception("Invalid email address");
        }

        $subject = "Reservation Confirmation: " . htmlspecialchars($data['title']);
        $reservation_date = date('F j, Y');
        
        $message = sprintf('
        <html>
        <head>
            <title>Reservation Confirmation</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { color: #2c3e50; }
                .details { background: #f9f9f9; padding: 15px; border-radius: 5px; }
                .footer { margin-top: 20px; font-size: 0.9em; color: #7f8c8d; }
            </style>
        </head>
        <body>
            <h2 class="header">Reservation Confirmed</h2>
            <p>Dear %s,</p>
            
            <div class="details">
                <p><strong>Book Title:</strong> %s</p>
                <p><strong>Queue Position:</strong> %d</p>
                <p><strong>Reservation Date:</strong> %s</p>
            </div>
            
            <p>You will be notified when the book becomes available for pickup.</p>
            
            <div class="footer">
                <p>Thank you for using our library system.</p>
                <p>This is an automated message - please do not reply.</p>
            </div>
        </body>
        </html>',
        htmlspecialchars($data['full_name']),
        htmlspecialchars($data['title']),
        $queue_position,
        $reservation_date);

        // Prepare email headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: library@' . parse_url(BASE_URL, PHP_URL_HOST),
            'X-Mailer: PHP/' . phpversion()
        ];

        // Send email and log result
        $result = mail($to, $subject, $message, implode("\r\n", $headers));
        
        if (!$result) {
            error_log("Failed to send reservation confirmation to $to");
            return false;
        }
        
        // Log notification in database
        logNotification($user_id, 'reservation', "Book reservation confirmed: {$data['title']}");
        
        return true;
        
    } catch (Exception $e) {
        error_log("Reservation confirmation error: " . $e->getMessage());
        return false;
    }
}

/**
 * Sends a notification when a reserved book becomes available
 * 
 * @param int $user_id The ID of the user to notify
 * @param int $book_id The ID of the available book
 * @return bool True on success, false on failure
 */
function sendReservationAvailableNotification($user_id, $book_id) {
    global $pdo;
    
    try {
        // Validate input parameters
        if (!is_numeric($user_id) || !is_numeric($book_id)) {
            throw new InvalidArgumentException("Invalid parameters provided");
        }

        // Get user and book details
        $stmt = $pdo->prepare("SELECT u.email, u.full_name, b.title 
                             FROM users u, books b
                             WHERE u.user_id = ? AND b.book_id = ?");
        if (!$stmt->execute([$user_id, $book_id])) {
            throw new Exception("Failed to fetch user/book details");
        }
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$data) {
            throw new Exception("User or book not found");
        }

        // Prepare email content
        $to = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$to) {
            throw new Exception("Invalid email address");
        }

        $subject = "Book Available: " . htmlspecialchars($data['title']);
        
        $message = sprintf('
        <html>
        <head>
            <title>Book Available</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .header { color: #6e45e2; }
                .cta { 
                    background: linear-gradient(135deg, #6e45e2 0%%, #88d3ce 100%%); 
                    color: white; 
                    padding: 10px 15px; 
                    text-decoration: none; 
                    border-radius: 5px; 
                    display: inline-block;
                    margin: 10px 0;
                }
                .footer { margin-top: 20px; font-size: 0.9em; color: #7f8c8d; }
                .deadline { color: #e74c3c; font-weight: bold; }
            </style>
        </head>
        <body>
            <h2 class="header">Your Reserved Book Is Available</h2>
            <p>Dear %s,</p>
            
            <p>The book <strong>%s</strong> you reserved is now available for pickup!</p>
            
            <p>Please visit the library within <span class="deadline">3 days</span> to collect your book.</p>
            
            <p>
                <a href="%s/user/books.php" class="cta">View Available Books</a>
            </p>
            
            <div class="footer">
                <p>Library Hours: Monday-Friday 9am-5pm</p>
                <p>This is an automated message - please do not reply.</p>
            </div>
        </body>
        </html>',
        htmlspecialchars($data['full_name']),
        htmlspecialchars($data['title']),
        BASE_URL);

        // Prepare email headers
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: library@' . parse_url(BASE_URL, PHP_URL_HOST),
            'X-Mailer: PHP/' . phpversion()
        ];

        // Send email
        $result = mail($to, $subject, $message, implode("\r\n", $headers));
        
        if (!$result) {
            error_log("Failed to send availability notification to $to");
            return false;
        }
        
        // Log notification in database
        logNotification($user_id, 'availability', "Book available for pickup: {$data['title']}");
        
        return true;
        
    } catch (Exception $e) {
        error_log("Availability notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logs a notification in the database
 * 
 * @param int $user_id The ID of the user to notify
 * @param string $type The notification type (e.g., 'reservation', 'available')
 * @param string $message The notification message
 * @return bool True on success, false on failure
 */
function logNotification($user_id, $type, $message) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO notifications 
                             (user_id, type, message, is_read, created_at) 
                             VALUES (?, ?, ?, 0, NOW())");
        return $stmt->execute([
            $user_id,
            $type,
            $message
        ]);
    } catch (Exception $e) {
        error_log("Failed to log notification: " . $e->getMessage());
        return false;
    }
}
?>