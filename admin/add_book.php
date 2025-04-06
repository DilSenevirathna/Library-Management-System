<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Add New Book";
require_once '../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $isbn = trim($_POST['isbn']);
    $publisher = trim($_POST['publisher']);
    $publication_year = intval($_POST['publication_year']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $shelf_location = trim($_POST['shelf_location']);

    // Handle file upload
    $cover_image = 'default.jpg';
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/book-covers/';
        $file_ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $cover_image = uniqid('book_', true) . '.' . $file_ext;
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir . $cover_image);
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO books 
                              (title, author, isbn, publisher, publication_year, category, 
                               description, quantity, available_quantity, shelf_location, cover_image) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $title,
            $author,
            $isbn,
            $publisher,
            $publication_year,
            $category,
            $description,
            $quantity,
            $quantity,
            $shelf_location,
            $cover_image
        ]);

        $_SESSION['success'] = "Book added successfully!";
        redirect('books.php');
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error adding book: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <h2><i class="fas fa-book-medical"></i> Add New Book</h2>

    <div class="card mt-4">
        <div class="card-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="author" class="form-label">Author *</label>
                            <input type="text" class="form-control" id="author" name="author" required>
                        </div>

                        <div class="mb-3">
                            <label for="isbn" class="form-label">ISBN *</label>
                            <input type="text" class="form-control" id="isbn" name="isbn" required>
                        </div>

                        <div class="mb-3">
                            <label for="publisher" class="form-label">Publisher</label>
                            <input type="text" class="form-control" id="publisher" name="publisher">
                        </div>

                        <div class="mb-3">
                            <label for="publication_year" class="form-label">Publication Year</label>
                            <input type="number" class="form-control" id="publication_year" name="publication_year"
                                min="1000" max="<?= date('Y') ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="Fiction">Fiction</option>
                                <option value="Non-Fiction">Non-Fiction</option>
                                <option value="Science">Science</option>
                                <option value="Technology">Technology</option>
                                <option value="History">History</option>
                                <option value="Biography">Biography</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" value="1"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="shelf_location" class="form-label">Shelf Location</label>
                            <input type="text" class="form-control" id="shelf_location" name="shelf_location">
                        </div>

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" class="form-control" id="cover_image" name="cover_image"
                                accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Book
                    </button>
                    <a href="books.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>