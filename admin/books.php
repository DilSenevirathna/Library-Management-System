<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Manage Books";
require_once '../includes/header.php';

// Handle book deletion
if (isset($_GET['delete'])) {
    $book_id = intval($_GET['delete']);
    try {
        $pdo->beginTransaction();

        // Delete associated transactions first
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE book_id = ?");
        $stmt->execute([$book_id]);

        // Delete the book
        $stmt = $pdo->prepare("DELETE FROM books WHERE book_id = ?");
        $stmt->execute([$book_id]);

        $pdo->commit();
        $_SESSION['success'] = "Book deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting book: " . $e->getMessage();
    }
    redirect('books.php');
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-book"></i> Manage Books</h2>
        <a href="add_book.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Book
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="booksTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Available</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM books ORDER BY title");
                        while ($book = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $book['book_id'] ?></td>
                                <td>
                                    <img src="../assets/images/book-covers/<?= $book['cover_image'] ?: 'default.jpg' ?>"
                                        alt="Cover" style="width: 50px; height: auto;">
                                </td>
                                <td><?= htmlspecialchars($book['title']) ?></td>
                                <td><?= htmlspecialchars($book['author']) ?></td>
                                <td><?= htmlspecialchars($book['isbn']) ?></td>
                                <td>
                                    <span class="badge <?= $book['available_quantity'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                        <?= $book['available_quantity'] ?> / <?= $book['quantity'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_book.php?id=<?= $book['book_id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?= $book['book_id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this book?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function () {
        $('#booksTable').DataTable({
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [1, 6] } // Disable sorting for cover and actions columns
            ]
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>