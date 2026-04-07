<?php
session_start();
include 'db.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: admin.php");
            exit;
        }
        else {
            $error = "Invalid username or password.";
        }
    }
    catch (mysqli_sql_exception $e) {
        $error = "A system error occurred.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Uni-CITS Admin</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/uiu.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body { height: 100vh; display: flex; align-items: center; justify-content: center; background-color: #f4f7f6; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 15px; }
    </style>
</head>
<body>

<div class="card login-card shadow">
    <div class="card-body">
        <div class="text-center mb-4">
            <img src="assets/img/uiu.png" alt="Logo" width="50">
            <h4 class="mt-2 fw-bold">Admin Login</h4>
        </div>
        
        <?php if (isset($_GET['signup']) && $_GET['signup'] == 'success'): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php
endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php
endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">Sign In</button>
        </form>
        <div class="text-center mt-3">
            <p class="small mb-1">New staff? <a href="signup.php" class="text-decoration-none">Create an account</a></p>
            <a href="index.php" class="text-decoration-none small">← Back to Homepage</a>
        </div>
    </div>
</div>

</body>
</html>