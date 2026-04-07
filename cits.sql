-- University Department Notice Management System
-- SQL Schema and Initial Seed Data

-- Drop database if it exists to ensure a clean setup
DROP DATABASE IF EXISTS university;
CREATE DATABASE university CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE university;

-- 1. Administrative Users
CREATE TABLE admins (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100),
  `role` ENUM('Admin', 'Teacher') DEFAULT 'Admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Departments
CREATE TABLE departments (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `dept_name` VARCHAR(100) NOT NULL,
  `dept_code` VARCHAR(10) NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Academic Sessions (e.g., Fall 2023, Spring 2024)
CREATE TABLE academic_sessions (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `session_name` VARCHAR(50) NOT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Faculty Members
CREATE TABLE faculty (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `designation` VARCHAR(100),
  `email` VARCHAR(100) UNIQUE,
  `phone` VARCHAR(20),
  `dept_id` INT(11),
  `image_url` VARCHAR(255),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`dept_id`) REFERENCES departments(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Students
CREATE TABLE students (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `roll_no` VARCHAR(20) NOT NULL UNIQUE,
  `email` VARCHAR(100) UNIQUE,
  `phone` VARCHAR(20),
  `password` VARCHAR(255) DEFAULT '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  `dept_id` INT(11),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`dept_id`) REFERENCES departments(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Courses
CREATE TABLE courses (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `course_name` VARCHAR(100) NOT NULL,
  `course_code` VARCHAR(20) NOT NULL UNIQUE,
  `credits` DECIMAL(3,2),
  `dept_id` INT(11),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`dept_id`) REFERENCES departments(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Enrollments
CREATE TABLE enrollments (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `student_id` INT(11) NOT NULL,
  `course_id` INT(11) NOT NULL,
  `session_id` INT(11) NOT NULL,
  `enrollment_date` DATE DEFAULT (CURRENT_DATE),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES students(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES courses(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`session_id`) REFERENCES academic_sessions(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Notices
CREATE TABLE notices (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `priority` ENUM('Normal', 'Important', 'Urgent') DEFAULT 'Normal',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Events
CREATE TABLE events (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(255) NOT NULL,
  `event_date` DATE NOT NULL,
  `location` VARCHAR(255),
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data for the University Notice Board
INSERT INTO notices (title, description, priority) VALUES
('Welcome to the New Academic Session', 'We are excited to welcome all students back to the Department of Computer Science. Orientation for freshmen starts next Monday at 10:00 AM in Hall A.', 'Normal'),
('Mid-Semester Examination Schedule', 'The mid-semester examinations are scheduled to begin on October 15th. Please check the department office or your student portal for the detailed time table.', 'Important'),
('Workshop on Modern Web Technologies', 'A hands-on workshop on React, Node.js, and MySQL will be conducted by industry experts this Friday. Interested students must register by Wednesday.', 'Normal'),
('Annual Sports Meet 2024', 'Registration for the annual sports meet is now open! Students can sign up for various track and field events at the sports office. Let the games begin.', 'Normal'),
('Holiday Notice', 'The University will remain closed for the upcoming public holiday. Regular classes will resume from the following day.', 'Urgent'),
('Library Extended Hours', 'To assist students during the exam season, the departmental library will now remain open until 9:00 PM on weekdays starting from next week.', 'Normal'),
('Placement Drive: TechCorp Solutions', 'Final year students are invited for a recruitment presentation by TechCorp Solutions on Thursday. Please bring 3 copies of your updated resume.', 'Important');

-- Sample Data for Admins (Password: password)
INSERT INTO admins (username, password, full_name) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator');

-- Sample Data for Departments
INSERT INTO departments (dept_name, dept_code) VALUES
('Computer Science and Engineering', 'CSE'),
('Electrical and Electronic Engineering', 'EEE'),
('Business Administration', 'BBA');

-- Sample Data for Sessions
INSERT INTO academic_sessions (session_name, is_active) VALUES
('Fall 2023', 0),
('Spring 2024', 0),
('Fall 2024', 0),
('Spring 2025', 0),
('Fall 2025', 0),
('Spring 2026', 1);

-- Sample Data for Faculty
INSERT INTO faculty (name, designation, email, dept_id, image_url) VALUES
('Dr. Alan Turing', 'Professor & Head', 'turing@uni.edu', 1, 'https://via.placeholder.com/300x400?text=Dr.+Alan+Turing'),
('Grace Hopper', 'Associate Professor', 'hopper@uni.edu', 1, 'https://via.placeholder.com/300x400?text=Grace+Hopper'),
('John von Neumann', 'Assistant Professor', 'neumann@uni.edu', 1, 'https://via.placeholder.com/300x400?text=John+von+Neumann');

-- Sample Data for Courses
INSERT INTO courses (course_name, course_code, credits, dept_id) VALUES
('Introduction to Programming', 'CSE101', 3.0, 1),
('Database Management Systems', 'CSE301', 4.0, 1);