<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT username, full_name FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Safety check: if the user record no longer exists, invalidate session and redirect
if (!$user) {
    header("Location: logout.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Uni-CITS</title>
    <link rel="icon" type="image/png" href="assets/img/uiu.png">
  
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Admin Profile</h2>
                <a href="admin.php" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'success'): ?>
                    <div class="alert alert-success">Password updated successfully!</div>
                <?php
    elseif ($_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['msg'] ?? 'An error occurred.'); ?></div>
                <?php
    endif; ?>
            <?php
endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Account Information</h5>
                    <p class="mb-1 text-muted">Full Name: <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></p>
                    <p class="text-muted">Username: <strong><?php echo htmlspecialchars($user['username']); ?></strong></p>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Update Password</h5>
                    <form action="update_password.php" method="POST" id="profileForm">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" id="new_pw" class="form-control" required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_new_pw" class="form-control" required>
                            <div id="profilePwError" class="text-danger small mt-1" style="display:none;">New passwords do not match.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    const pForm = document.getElementById('profileForm');
    const nPw = document.getElementById('new_pw');
    const cNPw = document.getElementById('confirm_new_pw');
    const pError = document.getElementById('profilePwError');

    pForm.addEventListener('submit', function(e) {
        if (nPw.value !== cNPw.value) {
            e.preventDefault();
            pError.style.display = 'block';
            cNPw.classList.add('is-invalid');
        }
    });
</script>
</html>