<?php
require_once 'includes/header.php';
?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        const updateCount = () => {
            const current = +counter.innerText;
            const increment = Math.ceil(target / 200);
            if (current < target) {
                counter.innerText = current + increment;
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
});
</script>


<style>
    /* Hero Section - Gradient Background */
.hero-section {
    background: linear-gradient(135deg, #6e45e2 0%, #88d3ce 100%);
    padding: 4rem 0;
    border-radius: 0 0 20px 20px;
    margin-bottom: 3rem;
    box-shadow: 0 10px 30px rgba(110, 69, 226, 0.2);
}

.hero-section h1 {
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 1rem;
}

.hero-section .lead {
    font-size: 1.4rem;
    opacity: 0.9;
    margin-bottom: 2rem;
}

/* Search Form */
.hero-section .form-control {
    border: none;
    padding: 1rem;
    border-radius: 8px 0 0 8px;
    font-size: 1.1rem;
}

.hero-section .btn-light {
    background: white;
    border: none;
    border-radius: 0 8px 8px 0;
    padding: 0 1.5rem;
    font-weight: 600;
    color: #6e45e2;
    transition: all 0.3s ease;
}

.hero-section .btn-light:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
}

/* Feature Cards */
.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    border: 1px solid rgba(110, 69, 226, 0.1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(110, 69, 226, 0.15);
}

.card-body {
    padding: 2rem;
}

.card-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
}

.card-text {
    color: #4a5568;
}

/* Icons in Feature Cards */
.fa-3x {
    color: #6e45e2;
    background: linear-gradient(135deg, rgba(110, 69, 226, 0.1) 0%, rgba(136, 211, 206, 0.1) 100%);
    padding: 1.5rem;
    border-radius: 50%;
    margin-bottom: 1.5rem;
}

/* Book Cards */
.card-img-top {
    height: 250px;
    object-fit: cover;
    border-bottom: 1px solid rgba(110, 69, 226, 0.1);
}

.card-footer {
    border-top: none;
    background: white;
    padding: 1.25rem;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #6e45e2 0%, #88d3ce 100%);
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5d38c9 0%, #7bc4bd 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(110, 69, 226, 0.3);
}

/* Section Headings */
h2.text-center {
    position: relative;
    padding-bottom: 1rem;
    margin-bottom: 3rem;
    font-weight: 700;
    color: #2d3748;
}

h2.text-center:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #6e45e2 0%, #88d3ce 100%);
    border-radius: 2px;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 0;
        border-radius: 0 0 15px 15px;
    }
    
    .hero-section h1 {
        font-size: 2.2rem;
    }
    
    .hero-section .lead {
        font-size: 1.2rem;
    }
    
    .card {
        margin-bottom: 1.5rem;
    }
}


/* Category Cards */
.category-card {
    background: linear-gradient(135deg, #6e45e2 0%, #88d3ce 100%);
    color: white;
    border: none;
    border-radius: 12px;
    transition: transform 0.3s ease;
    box-shadow: 0 10px 20px rgba(110, 69, 226, 0.2);
}

.category-card:hover {
    transform: translateY(-5px);
}

/* Stats Section */
.stats-section {
    background: linear-gradient(135deg, #6e45e2, #88d3ce);
    padding: 4rem 2rem;
    border-radius: 20px;
    margin-top: 4rem;
}

.stats-section h2 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

/* Join CTA */
.join-card {
    background: linear-gradient(135deg, #6e45e2, #88d3ce);
    border-radius: 16px;
    color: white;
    box-shadow: 0 10px 20px rgba(110, 69, 226, 0.2);
}

/* Animated Counter */
.counter {
    transition: 0.3s;
}

</style>

<div class="hero-section bg-primary text-white py-5">
    <div class="container text-center">
        <h1 class="display-4">Welcome to Our Library</h1>
        <p class="lead">Discover, learn, and explore with our vast collection of books</p>
        <form action="search.php" method="GET" class="mt-4">
            <div class="input-group mb-3 mx-auto" style="max-width: 600px;">
                <input type="text" class="form-control" name="query" placeholder="Search for books..." required>
                <button class="btn btn-light" type="submit">Search</button>
            </div>
        </form>
    </div>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-book fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">Extensive Collection</h3>
                    <p class="card-text">Access thousands of books across various genres and subjects.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">User Management</h3>
                    <p class="card-text">Easy management of user accounts and borrowing privileges.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bell fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">Notifications</h3>
                    <p class="card-text">Get alerts for due dates, reservations, and more.</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="text-center mb-4">Featured Books</h2>
    <div class="row">
        <?php
        // Fetch featured books
        $stmt = $pdo->prepare("SELECT * FROM books ORDER BY RAND() LIMIT 3");
        $stmt->execute();
        $featured_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($featured_books as $book): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="<?php echo BASE_URL; ?>/assets/images/book-covers/<?php echo $book['cover_image'] ?: 'default.jpg'; ?>"
                        class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="card-text text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="card-text"><?php echo substr($book['description'], 0, 100) . '...'; ?></p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="search.php?query=<?php echo urlencode($book['title']); ?>" class="btn btn-primary">View
                            Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<!-- Top Categories -->
<div class="row mt-5">
    <h2 class="text-center mb-4">Top Categories</h2>
    <div class="col-md-3">
        <div class="card category-card text-center py-4">
            <i class="fas fa-code fa-2x mb-3"></i>
            <h5>Technology</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card category-card text-center py-4">
            <i class="fas fa-heartbeat fa-2x mb-3"></i>
            <h5>Health</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card category-card text-center py-4">
            <i class="fas fa-globe fa-2x mb-3"></i>
            <h5>Geography</h5>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card category-card text-center py-4">
            <i class="fas fa-pen-nib fa-2x mb-3"></i>
            <h5>Literature</h5>
        </div>
    </div>
</div>

<!-- Recently Added Books -->
<div class="row mt-5">
    <h2 class="text-center mb-4">Recently Added</h2>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM books ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $recent_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($recent_books as $book): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo BASE_URL; ?>/assets/images/book-covers/<?php echo $book['cover_image'] ?: 'default.jpg'; ?>"
                     class="card-img-top" alt="<?php echo htmlspecialchars($book['title']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                    <p class="card-text"><?php echo substr($book['description'], 0, 100) . '...'; ?></p>
                </div>
                <div class="card-footer bg-white">
                    <a href="search.php?query=<?php echo urlencode($book['title']); ?>" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Library Stats -->
<div class="row mt-5 stats-section text-white text-center">
    <div class="col-md-3">
        <h2 class="counter" data-target="3500">0</h2>
        <p>Books Available</p>
    </div>
    <div class="col-md-3">
        <h2 class="counter" data-target="750">0</h2>
        <p>Registered Users</p>
    </div>
    <div class="col-md-3">
        <h2 class="counter" data-target="1200">0</h2>
        <p>Books Issued</p>
    </div>
    <div class="col-md-3">
        <h2 class="counter" data-target="95">0</h2>
        <p>Categories</p>
    </div>
</div>

<!-- Join CTA -->
<div class="row mt-5">
    <div class="col text-center">
        <div class="p-5 join-card">
            <h2>Become a Member Today</h2>
            <p>Sign up to explore and enjoy full access to our library system.</p>
            <a href="<?php echo BASE_URL; ?>/register.php" class="btn btn-primary mt-3">Join Now</a>
        </div>
    </div>
</div>


</div>

<?php
require_once 'includes/footer.php';
?>