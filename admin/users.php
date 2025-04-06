<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$pageTitle = "Manage Users";
require_once '../includes/header.php';

// Handle user status change
if (isset($_GET['toggle_status'])) {
    $user_id = intval($_GET['toggle_status']);
    $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','inactive','active') WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $_SESSION['success'] = "User status updated!";
    redirect('users.php');
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    try {
        $pdo->beginTransaction();

        // Delete associated transactions
        $stmt = $pdo->prepare("DELETE FROM transactions WHERE user_id = ?");
        $stmt->execute([$user_id]);

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        $_SESSION['success'] = "User deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
    redirect('users.php');
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users"></i> Manage Users</h2>
        <a href="add_user.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM users ORDER BY full_name");
                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $user['user_id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= ucfirst($user['user_type']) ?></td>
                                <td>
                                    <span class="badge <?= $user['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?toggle_status=<?= $user['user_id'] ?>"
                                        class="btn btn-sm <?= $user['status'] == 'active' ? 'btn-danger' : 'btn-success' ?>">
                                        <i class="fas <?= $user['status'] == 'active' ? 'fa-ban' : 'fa-check' ?>"></i>
                                    </a>
                                    <a href="?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?')">
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
        $('#usersTable').DataTable({
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [6] } // Disable sorting for actions column
            ]
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>