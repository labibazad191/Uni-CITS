<?php
session_start();
include 'db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($full_name)) {
        $error = "All fields are required.";
    }
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    }
    else {
        try {
            // Check if username exists
            $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            if ($check_stmt->get_result()->num_rows > 0) {
                $error = "Username is already taken.";
            }
            else {
                // Hash password and insert
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO admins (username, password, full_name) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $username, $hashed_password, $full_name);

                if ($insert_stmt->execute()) {
                    header("Location: login.php?signup=success");
                    exit;
                }
                else {
                    $error = "Record could not be saved. Please try again.";
                }
            }
        }
        catch (mysqli_sql_exception $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Uni-CITS Admin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/uiu.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f4f7f6; }
        .login-card { width: 100%; max-width: 450px; padding: 20px; border-radius: 15px; }
    </style>
</head>
<body>
<div class="card login-card shadow">
    <div class="card-body">
        <div class="text-center mb-4">
            <img src="assets/img/uiu.png" alt="Logo" width="50">
            <h4 class="mt-2 fw-bold">Admin Registration</h4>
        </div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php
endif; ?>
        <form method="POST" id="signupForm">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                <div id="pwError" class="text-danger small mt-1" style="display:none;">Passwords do not match.</div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
        </form>
        <div class="text-center mt-3">
            <p class="small">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
        </div>
    </div>
</div>
</body>
<script>
    const form = document.getElementById('signupForm');
    const pw = document.getElementById('password');
    const cpw = document.getElementById('confirm_password');
    const error = document.getElementById('pwError');

    form.addEventListener('submit', function(e) {
        if (pw.value !== cpw.value) {
            e.preventDefault();
            error.style.display = 'block';
            cpw.classList.add('is-invalid');
        }
    });

    cpw.addEventListener('input', () => {
        error.style.display = 'none';
        cpw.classList.remove('is-invalid');
    });
</script>
</html>