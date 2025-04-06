<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Issue New Book";
require_once '../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $issue_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+14 days')); // 2 weeks default

    // Check book availability
    $stmt = $pdo->prepare("SELECT available_quantity FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book && $book['available_quantity'] > 0) {
        // Insert transaction
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, book_id, issue_date, due_date, status) 
                               VALUES (?, ?, ?, ?, 'issued')");
        $stmt->execute([$user_id, $book_id, $issue_date, $due_date]);

        // Decrease available quantity
        $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = ?");
        $stmt->execute([$book_id]);

        $_SESSION['success'] = "Book issued successfully.";
        redirect('transactions.php');
    } else {
        $_SESSION['error'] = "Selected book is not available.";
    }
}

// Fetch users and available books
$users = $pdo->query("SELECT user_id, full_name FROM users ORDER BY full_name")->fetchAll();
$books = $pdo->query("SELECT book_id, title FROM books WHERE available_quantity > 0 ORDER BY title")->fetchAll();
?>

<style>
    /* Gradient button for Issue New Book */
.btn-gradient-success {
    background: linear-gradient(to right, #2196f3, #21cbf3);
    color: white;
    border: none;
    transition: all 0.3s ease-in-out;
}

.btn-gradient-success:hover {
    background: linear-gradient(to right, #21cbf3, #2196f3);
    color: white;
}

</style>

<div class="container mt-4">
    <h2><i class="fas fa-book-medical"></i> Issue New Book</h2>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <form method="POST" class="card p-4 mt-3">
        <div class="mb-3">
            <label for="user_id" class="form-label">Select User</label>
            <select name="user_id" id="user_id" class="form-select" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="book_id" class="form-label">Select Book</label>
            <select name="book_id" id="book_id" class="form-select" required>
                <option value="">-- Select Book --</option>
                <?php foreach ($books as $book): ?>
                    <option value="<?= $book['book_id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-gradient-success">
            <i class="fas fa-plus-circle"></i> Issue Book
        </button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
