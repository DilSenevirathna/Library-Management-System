<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$pageTitle = "User Profile";
require_once '../includes/header.php';

// Get user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    // Password change handling
    $password_changed = false;
    if (!empty($_POST['current_password'])) {
        if (password_verify($_POST['current_password'], $user['password'])) {
            if (!empty($_POST['new_password'])) {
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $password_changed = true;
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect!";
        }
    }

    try {
        if ($password_changed) {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $phone, $address, $new_password, $_SESSION['user_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ? WHERE user_id = ?");
            $stmt->execute([$full_name, $email, $phone, $address, $_SESSION['user_id']]);
        }

        $_SESSION['success'] = "Profile updated successfully!";
        $_SESSION['full_name'] = $full_name;
        redirect('profile.php');
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    }
}
?>

<div class="container mt-4">
    <h2><i class="fas fa-user-circle"></i> User Profile</h2>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="../assets/images/user-avatars/default.png" class="rounded-circle mb-3"
                        style="width: 150px; height: 150px; object-fit: cover;" alt="Profile Picture">
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="text-muted">Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                        value="<?= htmlspecialchars($user['full_name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username"
                                        value="<?= htmlspecialchars($user['username']) ?>" disabled>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        value="<?= htmlspecialchars($user['phone']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        rows="3"><?= htmlspecialchars($user['address']) ?></textarea>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5>Change Password</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password"
                                        name="current_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password">
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>