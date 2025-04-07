<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$type = $_GET['type'] ?? 'transactions';
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_report.csv"');

$output = fopen('php://output', 'w');

switch ($type) {
    case 'books':
        fputcsv($output, ['Book ID', 'Title', 'Author', 'Available Quantity']);
        $stmt = $pdo->query("SELECT book_id, title, author, available_quantity FROM books");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        break;

    case 'users':
        fputcsv($output, ['User ID', 'Full Name', 'Username', 'Email', 'User Type']);
        $stmt = $pdo->query("SELECT user_id, full_name, username, email, user_type FROM users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        break;

    case 'transactions':
    default:
        fputcsv($output, ['Transaction ID', 'User ID', 'Book ID', 'Issue Date', 'Return Date', 'Status', 'Fine Amount']);
        $stmt = $pdo->prepare("SELECT transaction_id, user_id, book_id, issue_date, return_date, status, fine_amount 
                               FROM transactions 
                               WHERE issue_date BETWEEN ? AND ?");
        $stmt->execute([$start_date, $end_date]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        break;
}

fclose($output);
exit;
