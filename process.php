<?php
session_start();
include 'db.php';

// Security Check: Only allow logged in admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Create Notice
if (isset($_POST['save'])) {
    try {
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $priority = $_POST['priority'];

        $stmt = $conn->prepare("INSERT INTO notices (title, description, priority) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $desc, $priority);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Update Notice
if (isset($_POST['update'])) {
    try {
        $id = (int)$_POST['id'];
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $priority = $_POST['priority'];

        $stmt = $conn->prepare("UPDATE notices SET title = ?, description = ?, priority = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $desc, $priority, $id);
        $stmt->execute();
        header("Location: admin.php?status=updated");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Delete Notice
if (isset($_GET['delete'])) {
    try {
        $id = (int)$_GET['delete'];

        $stmt = $conn->prepare("DELETE FROM notices WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Course Management CRUD
if (isset($_POST['save_course'])) {
    try {
        $name = $_POST['course_name'];
        $code = $_POST['course_code'];
        $credits = $_POST['credits'];
        $dept = $_POST['dept_id'];

        $stmt = $conn->prepare("INSERT INTO courses (course_name, course_code, credits, dept_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $name, $code, $credits, $dept);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_POST['update_course'])) {
    try {
        $id = (int)$_POST['id'];
        $name = $_POST['course_name'];
        $code = $_POST['course_code'];
        $credits = $_POST['credits'];
        $dept = $_POST['dept_id'];

        $stmt = $conn->prepare("UPDATE courses SET course_name = ?, course_code = ?, credits = ?, dept_id = ? WHERE id = ?");
        $stmt->bind_param("ssdii", $name, $code, $credits, $dept, $id);
        $stmt->execute();
        header("Location: admin.php?status=updated");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_GET['delete_course'])) {
    try {
        $id = (int)$_GET['delete_course'];
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Faculty Management CRUD
if (isset($_POST['save_faculty'])) {
    try {
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dept = $_POST['dept_id'];
        $img = $_POST['image_url'];

        $stmt = $conn->prepare("INSERT INTO faculty (name, designation, email, phone, dept_id, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $name, $designation, $email, $phone, $dept, $img);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_POST['update_faculty'])) {
    try {
        $id = (int)$_POST['id'];
        $name = $_POST['name'];
        $designation = $_POST['designation'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dept = $_POST['dept_id'];
        $img = $_POST['image_url'];

        $stmt = $conn->prepare("UPDATE faculty SET name = ?, designation = ?, email = ?, phone = ?, dept_id = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssssisi", $name, $designation, $email, $phone, $dept, $img, $id);
        $stmt->execute();
        header("Location: admin.php?status=updated");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_GET['delete_faculty'])) {
    try {
        $id = (int)$_GET['delete_faculty'];
        $stmt = $conn->prepare("DELETE FROM faculty WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Student Management
if (isset($_POST['save_student'])) {
    try {
        $name = $_POST['student_name'];
        $roll = $_POST['student_roll'];
        $email = $_POST['student_email'];
        $dept = $_POST['student_dept'];

        $stmt = $conn->prepare("INSERT INTO students (name, roll_no, email, dept_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $roll, $email, $dept);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_GET['delete_student'])) {
    try {
        $id = (int)$_GET['delete_student'];
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode("Cannot delete student with active enrollments."));
    }
}

// Enrollment Management
if (isset($_POST['save_enrollment'])) {
    try {
        $roll_no = $_POST['roll_no'];
        $course_id = $_POST['course_id'];
        $session_id = $_POST['session_id'];

        // Lookup internal student ID by roll number
        $lookup = $conn->prepare("SELECT id FROM students WHERE roll_no = ?");
        $lookup->bind_param("s", $roll_no);
        $lookup->execute();
        $student = $lookup->get_result()->fetch_assoc();

        if (!$student) {
            header("Location: admin.php?status=error&msg=" . urlencode("Student with Roll No '$roll_no' not found."));
            exit;
        }

        $student_id = $student['id'];

        $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, session_id) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $student_id, $course_id, $session_id);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_GET['delete_enrollment'])) {
    try {
        $id = (int)$_GET['delete_enrollment'];
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

// Session Management
if (isset($_POST['save_session'])) {
    try {
        $session_name = $_POST['session_name'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        // If setting this as active, deactivate others
        if ($is_active) {
            $conn->query("UPDATE academic_sessions SET is_active = 0");
        }

        $stmt = $conn->prepare("INSERT INTO academic_sessions (session_name, is_active) VALUES (?, ?)");
        $stmt->bind_param("si", $session_name, $is_active);
        $stmt->execute();
        header("Location: admin.php?status=success");
    }
    catch (mysqli_sql_exception $e) {
        header("Location: admin.php?status=error&msg=" . urlencode($e->getMessage()));
    }
}

if (isset($_GET['delete_session'])) {
    try {
        $id = (int)$_GET['delete_session'];

        // The database will prevent deletion if the session is linked to enrollments
        $stmt = $conn->prepare("DELETE FROM academic_sessions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: admin.php?status=deleted");
    }
    catch (mysqli_sql_exception $e) {
        // Handle foreign key constraint violations gracefully
        header("Location: admin.php?status=error&msg=" . urlencode("Cannot delete session linked to student enrollments."));
    }
}

$conn->close();
?>