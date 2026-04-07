<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

if ($new_password !== $confirm_password) {
    header("Location: profile.php?status=error&msg=New passwords do not match");
    exit;
}

try {
    // Fetch current hashed password
    $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verify current password
    if ($user && password_verify($current_password, $user['password'])) {
        // Hash and update
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_hashed_password, $admin_id);

        if ($update_stmt->execute()) {
            header("Location: profile.php?status=success");
        }
        else {
            header("Location: profile.php?status=error&msg=Database update failed");
        }
    }
    else {
        header("Location: profile.php?status=error&msg=Current password is incorrect");
    }
}
catch (mysqli_sql_exception $e) {
    header("Location: profile.php?status=error&msg=System error occurred");
}

$conn->close();
?>