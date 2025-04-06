<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid book ID.";
    redirect('books.php');
}

$book_id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    $_SESSION['error'] = "Book not found.";
    redirect('books.php');
}

$pageTitle = "Book Details";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <a href="books.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Books
    </a>

    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4 p-4 text-center">
                <img src="../assets/images/book-covers/<?= $book['cover_image'] ?: 'default.jpg' ?>"
                     alt="<?= htmlspecialchars($book['title']) ?>" style="max-width: 100%; height: auto;" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($book['category'] ?? 'Uncategorized') ?></p>
                    <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                    <p class="card-text">
                        <strong>Quantity:</strong> <?= $book['available_quantity'] ?> available out of <?= $book['quantity'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
