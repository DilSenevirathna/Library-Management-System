<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = "Book Details";
require_once '../includes/header.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid book ID.</div>";
    require_once '../includes/footer.php';
    exit;
}

$book_id = intval($_GET['id']);

// Get book details
$stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    echo "<div class='alert alert-warning'>Book not found.</div>";
    require_once '../includes/footer.php';
    exit;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    $review = trim($_POST['review']);
    $rating = intval($_POST['rating']);

    if (!empty($review) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, review_text, rating, review_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$book_id, $_SESSION['user_id'], $review, $rating]);
        echo "<div class='alert alert-success'>Thank you for your review!</div>";
    } else {
        echo "<div class='alert alert-danger'>Please provide a valid rating and review.</div>";
    }
}

// Get reviews
$reviewStmt = $pdo->prepare("
    SELECT r.*, u.username 
    FROM reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.book_id = ? 
    ORDER BY r.review_date DESC
");
$reviewStmt->execute([$book_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-4">
    <a href="books.php" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Catalog
    </a>

    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-4 text-center p-4">
                <img src="../assets/images/book-covers/<?= $book['cover_image'] ?: 'default.jpg' ?>"
                     alt="<?= htmlspecialchars($book['title']) ?>" class="img-fluid rounded shadow"
                     style="max-height: 400px;">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h3 class="card-title"><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                    <p class="card-text"><strong>Category:</strong> <?= htmlspecialchars($book['category']) ?></p>
                    <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                    <p class="card-text">
                        <strong>Availability:</strong>
                        <?php if ($book['available_quantity'] > 0): ?>
                            <span class="badge bg-success">Available</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Out of Stock</span>
                        <?php endif; ?>
                        <span class="ms-3">Quantity: <?= $book['available_quantity'] ?> / <?= $book['quantity'] ?></span>
                    </p>
                    <p class="card-text mt-3">
                        <a href="books.php" class="btn btn-primary">
                            <i class="fas fa-book"></i> Back to Books
                        </a>
                        <?php if ($book['available_quantity'] > 0): ?>
                            <form method="POST" action="books.php" class="d-inline">
                                <input type="hidden" name="borrow_book_id" value="<?= $book['book_id'] ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-book-open"></i> Borrow
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="books.php?reserve=<?= $book['book_id'] ?>" class="btn btn-warning">
                                <i class="fas fa-bookmark"></i> Reserve
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Review Form -->
    <div class="card mt-5 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Write a Review</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating:</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Select Rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="review" class="form-label">Review:</label>
                    <textarea name="review" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> Submit Review</button>
            </form>
        </div>
    </div>

    <!-- Review List -->
    <div class="card mt-4 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">User Reviews</h5>
        </div>
        <div class="card-body">
            <?php if ($reviews): ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="border-bottom mb-3 pb-2">
                        <div class="d-flex justify-content-between">
                            <strong><?= htmlspecialchars($r['username']) ?></strong>
                            <span class="text-warning"><?= str_repeat('★', $r['rating']) . str_repeat('☆', 5 - $r['rating']) ?></span>
                        </div>
                        <p><?= nl2br(htmlspecialchars($r['review_text'])) ?></p>
                        <small class="text-muted"><?= date('M d, Y', strtotime($r['review_date'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">No reviews yet. Be the first to review this book!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Animate.css CDN (optional but recommended) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<?php require_once '../includes/footer.php'; ?>
