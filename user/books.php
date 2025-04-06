<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = "Book Catalog";
require_once '../includes/header.php';

// Handle book borrowing via POST request (from modal confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book_id'])) {
    $book_id = intval($_POST['borrow_book_id']);

    // Check if book is available
    $stmt = $pdo->prepare("SELECT available_quantity FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book && $book['available_quantity'] > 0) {
        try {
            $issue_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+14 days'));

            $pdo->beginTransaction();

            // Create transaction
            $stmt = $pdo->prepare("INSERT INTO transactions (book_id, user_id, issue_date, due_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$book_id, $_SESSION['user_id'], $issue_date, $due_date]);

            // Update book quantity
            $stmt = $pdo->prepare("UPDATE books SET available_quantity = available_quantity - 1 WHERE book_id = ?");
            $stmt->execute([$book_id]);

            $pdo->commit();
            $_SESSION['success'] = "Book borrowed successfully! Due date: " . date('M d, Y', strtotime($due_date));
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error'] = "Error borrowing book: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Book is no longer available for borrowing!";
    }

    redirect('books.php');
}

// Handle book reservation
if (isset($_GET['reserve']) && is_numeric($_GET['reserve'])) {
    $book_id = intval($_GET['reserve']);

    try {
        $reservation_date = date('Y-m-d');
        $stmt = $pdo->prepare("INSERT INTO reservations (book_id, user_id, reservation_date) VALUES (?, ?, ?)");
        $stmt->execute([$book_id, $_SESSION['user_id'], $reservation_date]);

        $_SESSION['success'] = "Book reserved successfully! You'll be notified when it's available.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error reserving book: " . $e->getMessage();
    }

    redirect('books.php');
}

// Get filter values from GET parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$availability = isset($_GET['availability']) ? $_GET['availability'] : '';

// Build the SQL query with filters
$sql = "SELECT * FROM books WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR author LIKE ? OR isbn LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($author)) {
    $sql .= " AND author = ?";
    $params[] = $author;
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (!empty($availability)) {
    if ($availability === 'available') {
        $sql .= " AND available_quantity > 0";
    } elseif ($availability === 'unavailable') {
        $sql .= " AND available_quantity = 0";
    }
}

$sql .= " ORDER BY title";

// Get all books with filters applied
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get distinct authors and categories for filter dropdowns
$authors = $pdo->query("SELECT DISTINCT author FROM books ORDER BY author")->fetchAll(PDO::FETCH_COLUMN);
$categories = $pdo->query("SELECT DISTINCT category FROM books ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book"></i> Book Catalog</h2>
        <form class="d-flex" method="GET" action="books.php">
            <input class="form-control me-2" type="search" name="search" placeholder="Search books..."
                value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-primary" type="submit">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <div class="row">
        <!-- Filter Sidebar - Left Column -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="books.php">
                        <div class="mb-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   placeholder="Title, author or ISBN" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <select class="form-select" id="author" name="author">
                                <option value="">All Authors</option>
                                <?php foreach ($authors as $a): ?>
                                    <option value="<?= htmlspecialchars($a) ?>" <?= $author === $a ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($a) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="availability" class="form-label">Availability</label>
                            <select class="form-select" id="availability" name="availability">
                                <option value="">All</option>
                                <option value="available" <?= $availability === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="unavailable" <?= $availability === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                            <a href="books.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Clear Filters
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Book Listings - Right Column -->
        <div class="col-md-9">
            <div class="row">
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100">
                                <img src="../assets/images/book-covers/<?= $book['cover_image'] ?: 'default.jpg' ?>"
                                    class="card-img-top" alt="<?= htmlspecialchars($book['title']) ?>"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($book['title']) ?></h5>
                                    <p class="card-text text-muted">by <?= htmlspecialchars($book['author']) ?></p>
                                    <p class="card-text">
                                        <small class="text-muted"><?= htmlspecialchars($book['category']) ?></small>
                                    </p>
                                    <p class="card-text">
                                        <span class="badge <?= ($book['available_quantity'] > 0) ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= ($book['available_quantity'] > 0) ? 'Available' : 'Out of Stock' ?>
                                        </span>
                                        <span class="float-end">
                                            Qty: <?= $book['available_quantity'] ?> / <?= $book['quantity'] ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="book_detail.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-info-circle"></i> Details
                                    </a>
                                    <?php if ($book['available_quantity'] > 0): ?>
                                        <button class="btn btn-sm btn-success float-end borrow-btn" 
                                                data-book-id="<?= $book['book_id'] ?>"
                                                data-book-title="<?= htmlspecialchars($book['title']) ?>"
                                                data-bs-toggle="modal" data-bs-target="#borrowModal">
                                            <i class="fas fa-book-open"></i> Borrow
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-warning float-end reserve-btn" 
                                                data-book-id="<?= $book['book_id'] ?>"
                                                data-bs-toggle="modal" data-bs-target="#reservationModal">
                                            <i class="fas fa-bookmark"></i> Reserve
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No books found matching your filters.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Borrow Confirmation Modal -->
<div class="modal fade" id="borrowModal" tabindex="-1" aria-labelledby="borrowModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="borrowModalLabel">Confirm Borrow</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="books.php">
                <div class="modal-body">
                    <input type="hidden" name="borrow_book_id" id="borrowBookId" value="">
                    <p>You are about to borrow the book: <strong id="borrowBookTitle"></strong></p>
                    <p>Due date will be: <strong><?= date('M d, Y', strtotime('+14 days')) ?></strong></p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Please return the book by the due date to avoid fines.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Borrow</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reservation Modal -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reservationModalLabel">Make Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reservationForm" method="POST" action="make_reservation.php">
                <div class="modal-body">
                    <input type="hidden" name="book_id" id="reserveBookId" value="">
                    <p>You are about to reserve the book: <strong id="reserveBookTitle"></strong></p>
                    <div class="mb-3">
                        <label for="reservation_notes" class="form-label">Special Notes (optional):</label>
                        <textarea class="form-control" id="reservation_notes" name="reservation_notes" rows="3"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> You will be notified when this book becomes available.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Reservation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set book details in borrow modal when button is clicked
document.querySelectorAll('.borrow-btn').forEach(button => {
    button.addEventListener('click', function() {
        const bookId = this.getAttribute('data-book-id');
        const bookTitle = this.getAttribute('data-book-title');
        document.getElementById('borrowBookId').value = bookId;
        document.getElementById('borrowBookTitle').textContent = bookTitle;
    });
});

// Set book details in reserve modal when button is clicked
document.querySelectorAll('.reserve-btn').forEach(button => {
    button.addEventListener('click', function() {
        const bookId = this.getAttribute('data-book-id');
        const bookTitle = this.closest('.card').querySelector('.card-title').textContent;
        document.getElementById('reserveBookId').value = bookId;
        document.getElementById('reserveBookTitle').textContent = bookTitle;
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>