// Dark mode toggle
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const darkModeStyle = document.getElementById('dark-mode-style');
    
    // Check for saved dark mode preference
    const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';
    
    if (darkModeEnabled) {
        darkModeStyle.disabled = false;
        if (darkModeToggle) darkModeToggle.textContent = 'Light Mode';
    }
    
    // Toggle dark mode
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            const isDark = darkModeStyle.disabled;
            darkModeStyle.disabled = !isDark;
            this.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
        });
    }
    
    // Borrow button functionality
    document.querySelectorAll('.borrow-btn').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.getAttribute('data-book-id');
            if (confirm('Are you sure you want to borrow this book?')) {
                fetch('../api/borrow.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `book_id=${bookId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Book borrowed successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request.');
                });
            }
        });
    });
    
    // Return button functionality
    document.querySelectorAll('.return-btn').forEach(button => {
        button.addEventListener('click', function() {
            const transactionId = this.getAttribute('data-transaction-id');
            if (confirm('Are you sure you want to return this book?')) {
                fetch('../api/return.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `transaction_id=${transactionId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Book returned successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request.');
                });
            }
        });
    });
});