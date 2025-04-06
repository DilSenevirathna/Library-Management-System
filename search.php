<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$author = isset($_GET['author']) ? $_GET['author'] : '';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="search.php">
                        <div class="mb-3">
                            <label for="query" class="form-label">Search</label>
                            <input type="text" class="form-control" id="query" name="query"
                                value="<?php echo htmlspecialchars($query); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <?php
                                $stmt = $pdo->query("SELECT DISTINCT category FROM books WHERE category IS NOT NULL ORDER BY category");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($category == $row['category']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['category']) . '" ' . $selected . '>' . htmlspecialchars($row['category']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="author" class="form-label">Author</label>
                            <select class="form-select" id="author" name="author">
                                <option value="">All Authors</option>
                                <?php
                                $stmt = $pdo->query("SELECT DISTINCT author FROM books ORDER BY author");
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($author == $row['author']) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['author']) . '" ' . $selected . '>' . htmlspecialchars($row['author']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Search Results</h2>

            <?php
            // Build SQL query based on filters
            $sql = "SELECT * FROM books WHERE 1=1";
            $params = [];

            if (!empty($query)) {
                $sql .= " AND (title LIKE ? OR author LIKE ? OR description LIKE ?)";
                $search_term = "%$query%";
                $params = array_merge($params, [$search_term, $search_term, $search_term]);
            }

            if (!empty($category)) {
                $sql .= " AND category = ?";
                $params[] = $category;
            }

            if (!empty($author)) {
                $sql .= " AND author = ?";
                $params[] = $author;
            }

            $sql .= " ORDER BY title";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($books) > 0): ?>
                <div class="row">
                    <?php foreach ($books as $book): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <img src="<?php echo BASE_URL; ?>/assets/images/book-covers/<?php echo $book['cover_image'] ?: 'default.jpg'; ?>"
                                    class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                                    <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                                    <p class="card-text"><small
                                            class="text-muted"><?php echo htmlspecialchars($book['category']); ?></small></p>
                                    <p class="card-text">
                                        <span
                                            class="badge <?php echo ($book['available_quantity'] > 0) ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo ($book['available_quantity'] > 0) ? 'Available' : 'Out of Stock'; ?>
                                        </span>
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="book-details.php?id=<?php echo $book['book_id']; ?>"
                                        class="btn btn-sm btn-primary">Details</a>
                                    <?php if ($book['available_quantity'] > 0 && isLoggedIn()): ?>
                                        <button class="btn btn-sm btn-success float-end borrow-btn"
                                            data-book-id="<?php echo $book['book_id']; ?>">Borrow</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No books found matching your criteria.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>