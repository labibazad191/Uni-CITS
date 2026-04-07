<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password

try {
    // Set MySQLi to throw exceptions for better error handling
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // 1. Connect to MySQL Server
    $conn = new mysqli($host, $user, $pass);

    // 2. Create Database with modern character sets
    $conn->query("CREATE DATABASE IF NOT EXISTS university CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<span style='color:green;'>✔</span> Database 'university' initialized.<br>";

    // 3. Select the database
    $conn->select_db('university');

    // Disable foreign key checks to allow dropping/recreating tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    $tables = [
        "admins" => "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        role ENUM('Admin', 'Teacher') DEFAULT 'Admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "departments" => "CREATE TABLE IF NOT EXISTS departments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        dept_name VARCHAR(100) NOT NULL,
        dept_code VARCHAR(10) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "academic_sessions" => "CREATE TABLE IF NOT EXISTS academic_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        session_name VARCHAR(50) NOT NULL,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "faculty" => "CREATE TABLE IF NOT EXISTS faculty (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        designation VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        dept_id INT,
        image_url VARCHAR(255),
        FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "courses" => "CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(100) NOT NULL,
        course_code VARCHAR(20) NOT NULL UNIQUE,
        credits DECIMAL(3,2),
        dept_id INT,
        FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "students" => "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        roll_no VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(100) UNIQUE,
        phone VARCHAR(20),
        password VARCHAR(255),
        dept_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "attendance" => "CREATE TABLE IF NOT EXISTS attendance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_id INT NOT NULL,
        status ENUM('Present', 'Absent') NOT NULL,
        attendance_date DATE DEFAULT (CURRENT_DATE),
        FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "marks" => "CREATE TABLE IF NOT EXISTS marks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_id INT NOT NULL,
        mid_term DECIMAL(5,2) DEFAULT 0,
        final_exam DECIMAL(5,2) DEFAULT 0,
        gpa DECIMAL(3,2) DEFAULT 0,
        FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "assignments" => "CREATE TABLE IF NOT EXISTS assignments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        course_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        file_path VARCHAR(255),
        deadline DATETIME,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "enrollments" => "CREATE TABLE IF NOT EXISTS enrollments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        session_id INT NOT NULL,
        enrollment_date DATE DEFAULT (CURRENT_DATE),
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
        FOREIGN KEY (session_id) REFERENCES academic_sessions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "notices" => "CREATE TABLE IF NOT EXISTS notices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        priority ENUM('Normal', 'Important', 'Urgent') DEFAULT 'Normal',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

        "events" => "CREATE TABLE IF NOT EXISTS events (
        id INT AUTO_INCREMENT PRIMARY KEY,
        event_name VARCHAR(255) NOT NULL,
        event_date DATE NOT NULL,
        location VARCHAR(255),
        description TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
    ];

    // Disable foreign key checks to allow dropping/recreating tables
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    foreach ($tables as $name => $sql) {
        $conn->query("DROP TABLE IF EXISTS $name");
        $conn->query($sql);
        echo "<span style='color:green;'>✔</span> Table '$name' initialized.<br>";
    }

    // 4. Insert Seed Data
    echo "<h3>Seeding Initial Data...</h3>";

    $seedData = [
        "INSERT IGNORE INTO departments (id, dept_name, dept_code) VALUES
        (1, 'Computer Science and Engineering', 'CSE'),
        (2, 'Electrical and Electronic Engineering', 'EEE'),
        (3, 'Business Administration', 'BBA')",

        "INSERT IGNORE INTO academic_sessions (session_name, is_active) VALUES
        ('Fall 2023', 0),
        ('Spring 2024', 0),
        ('Fall 2024', 0),
        ('Spring 2025', 0),
        ('Fall 2025', 0),
        ('Spring 2026', 1)",

        "INSERT IGNORE INTO faculty (name, designation, email, dept_id, image_url) VALUES
        ('Dr. Suman Ahmmed', 'Professor & Head of Department', 'suman@cse.uiu.ac.bd', 1, 'https://cse.uiu.ac.bd/faculty/suman/'),
        ('Grace Hopper', 'Associate Professor', 'hopper@uni.edu', 1, 'https://via.placeholder.com/300x400?text=Grace+Hopper'),
        ('John von Neumann', 'Assistant Professor', 'neumann@uni.edu', 1, 'https://via.placeholder.com/300x400?text=John+von+Neumann')",

        "INSERT IGNORE INTO admins (username, password, full_name) VALUES 
        ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator')",

        "INSERT IGNORE INTO notices (title, description, priority) VALUES
        ('Welcome to the New Academic Session', 'We are excited to welcome all students back to the Department of Computer Science.', 'Normal'),
        ('Mid-Semester Examination Schedule', 'The mid-semester examinations are scheduled to begin on October 15th.', 'Important'),
        ('Workshop on Modern Web Technologies', 'A hands-on workshop on React, Node.js, and MySQL will be conducted this Friday.', 'Normal')",

        "INSERT IGNORE INTO courses (course_name, course_code, credits, dept_id) VALUES
        ('Introduction to Programming', 'CSE101', 3.0, 1),
        ('Database Management Systems', 'CSE301', 4.0, 1)",

        "INSERT IGNORE INTO students (name, roll_no, email, dept_id) VALUES
        ('Test Student', '011191145', 'student@uiu.ac.bd', 1),
        ('Jane Doe', '011191146', 'jane@uiu.ac.bd', 1)"
    ];

    foreach ($seedData as $query) {
        $conn->query($query);
    }
    echo "<span style='color:green;'>✔</span> All seed data inserted successfully.<br>";
    echo "<hr><strong>Setup Complete!</strong> You can now use the <a href='admin.php'>Admin Panel</a> or view the <a href='index.php'>Homepage</a>.";

}
catch (Exception $e) {
    echo "<h4 style='color:red;'>Setup Failed</h4>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
finally {
    if (isset($conn)) {
        // Ensure foreign key checks are re-enabled even if an exception was thrown
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        $conn->close();
    }
}
?>