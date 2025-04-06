
<style>

footer.bg-dark {
    background: linear-gradient(135deg, #0d1b2a, #1b263b, #415a77);
    color: #ffffff;
}

footer a {
    color: #ffffff;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
    color: #adb5bd;
}

footer hr {
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}


</style>


</div> <!-- Close container from header -->

<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Library Management System</h5>
                <p>A complete solution for managing your library resources.</p>
            </div>
            <div class="col-md-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo BASE_URL; ?>" class="text-white">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/search.php" class="text-white">Books</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/login.php" class="text-white">Login</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h5>Contact</h5>
                <address>
                    <i class="fas fa-map-marker-alt"></i> 123 Library St, Bookville<br>
                    <i class="fas fa-phone"></i> (123) 456-7890<br>
                    <i class="fas fa-envelope"></i> info@library.com
                </address>
            </div>
        </div>
        <hr>
        <div class="text-center">
            <p>&copy; <?php echo date('Y'); ?> Library Management System. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>

<!-- At the end of your footer.php -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Initialize all dropdowns
var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
    return new bootstrap.Dropdown(dropdownToggleEl)
});
</script>
</body>

</html>