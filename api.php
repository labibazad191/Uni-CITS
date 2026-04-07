<?php
session_start();
include 'db.php';

// Security Check: Only allow logged in users to access the search API
if (!isset($_SESSION['admin_id'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

if (isset($_GET['search'])) {
    $query = "%" . $_GET['search'] . "%";
    try {
        $stmt = $conn->prepare("SELECT students.name, students.roll_no, departments.dept_code 
                                FROM students 
                                LEFT JOIN departments ON students.dept_id = departments.id 
                                WHERE students.name LIKE ? OR students.roll_no LIKE ? LIMIT 5");
        $stmt->bind_param("ss", $query, $query);
        $stmt->execute();
        $result = $stmt->get_result();

        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode($students);
    }
    catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}