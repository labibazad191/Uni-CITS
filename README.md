# Uni-CITS: University Department Notice Management System

Uni-CITS is a web-based management portal designed for university departments to streamline administrative tasks. It provides a centralized platform for managing academic notices, faculty directories, course listings, student records, and enrollments.

## 🚀 Features

### Administrative Management
- **Secure Authentication:** Admin registration and login system with password hashing (`password_hash`).
- **Profile Management:** Admins can update their full names and change passwords.
- **Session Control:** Persistent login sessions to protect administrative routes.

### Academic Operations (CRUD)
- **Notice Board:** Create, update, and delete notices with priority levels (Normal, Important, Urgent).
- **Faculty Directory:** Manage faculty profiles including designations, contact info, and profile images.
- **Course Management:** Maintain a list of courses mapped to specific departments and credit hours.
- **Student Records:** Track student information and unique roll numbers.
- **Enrollment System:** Link students to specific courses within active academic sessions.
- **Academic Sessions:** Manage active semesters (e.g., Fall 2024, Spring 2025) and toggle their active status.

## 🛠️ Tech Stack

- **Backend:** PHP 8.x
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5, HTML5, CSS3, JavaScript (Vanilla)
- **Icons/Assets:** UIU Logo, Google Fonts (Poppins)

## 📁 Project Structure

| File | Description |
| :--- | :--- |
| `signup.php` | Admin registration page with client-side and server-side validation. |
| `profile.php` | User profile page for viewing account details and triggering password updates. |
| `update_password.php` | (Referenced) Backend logic for validating and updating admin passwords. |
| `process.php` | The core controller handling all CRUD operations for notices, courses, faculty, and students. |
| `db.php` | (Referenced) Database connection configuration using `mysqli`. |
| `cits.sql` | Complete database schema including tables, constraints, and sample seed data. |
| `admin.php` | (Referenced) The main dashboard for administrative actions. |

## ⚙️ Installation & Setup

1. **Environment Requirements:**
   - Install a local server environment like XAMPP or WAMP.
   - Ensure PHP and MySQL services are running.

2. **Database Setup:**
   - Open **phpMyAdmin**.
   - Create a new database named `university`.
   - Import the `cits.sql` file provided in the project root.

3. **Configuration:**
   - Create/Edit `db.php` to match your local database credentials:
     ```php
     $conn = new mysqli("localhost", "root", "", "university");
     ```

4. **Deployment:**
   - Place the project folder in `C:\xampp\htdocs\university-cits\`.
   - Access the application via `http://localhost/university-cits/login.php`.

## 🗄️ Database Schema

The system uses a relational database with the following key tables:

- **admins:** Stores administrative credentials and roles.
- **departments:** Lists university departments (CSE, EEE, BBA, etc.).
- **faculty:** Contains faculty contact details and department associations.
- **students:** Stores student profiles.
- **courses:** Contains academic course details.
- **enrollments:** Junction table linking students, courses, and sessions.
- **notices:** Stores department announcements.
- **academic_sessions:** Manages semester timelines.

## 🔒 Security Measures

- **Prepared Statements:** All SQL queries in `process.php` and `signup.php` use `mysqli::prepare` to prevent SQL Injection.
- **Password Hashing:** Uses `PASSWORD_DEFAULT` (Bcrypt) for storing sensitive user credentials.
- **Input Validation:** Trims inputs and checks for empty fields on the server side.
- **Access Control:** Every administrative page checks for `$_SESSION['admin_id']` to prevent unauthorized access.

## 📝 License

This project is developed for educational purposes within the University Department Management context.

---
*Developed for Uni-CITS Administration*