<?php

session_start();
include 'db.php';


// Restrict access
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch departments for course form dropdown
$departments = $conn->query("SELECT * FROM departments");

// Fetch students and sessions for enrollment form
$students = $conn->query("SELECT id, name, roll_no FROM students ORDER BY name ASC");
$sessions = $conn->query("SELECT id, session_name, is_active FROM academic_sessions ORDER BY id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Uni-CITS</title>
    <link rel="icon" type="image/png" href="assets/img/uiu.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Manage University Notices</h2>
            <p class="text-muted">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>
        </div>
        <div>
            <a href="index.php" class="btn btn-outline-secondary me-2">Back to Site</a>
            <a href="profile.php" class="btn btn-primary me-2">Profile</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- AJAX Student Search -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4><i class="bi bi-search"></i> Instant Student Search (REST API)</h4>
            <div class="input-group mb-3">
                <input type="text" id="apiSearch" class="form-control" placeholder="Enter student name or roll number...">
                <span class="input-group-text bg-primary text-white">Live</span>
            </div>
            <div id="searchResults" class="list-group"></div>
        </div>
    </div>

    <!-- Status Alerts -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show">Notice published successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php
    elseif ($_GET['status'] == 'updated'): ?>
            <div class="alert alert-info alert-dismissible fade show">Notice updated successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php
    elseif ($_GET['status'] == 'deleted'): ?>
            <div class="alert alert-warning alert-dismissible fade show">Notice has been deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php
    elseif ($_GET['status'] == 'error'):
        $errorMsg = isset($_GET['msg']) ? $_GET['msg'] : 'An unknown database error occurred.';
?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php
    endif; ?>
    <?php
endif; ?>

    <?php
// Course Edit Logic
$c_edit_mode = false;
$c_name = '';
$c_code = '';
$c_credits = '';
$c_dept = '';
$c_id = '';
if (isset($_GET['edit_course'])) {
    $c_edit_mode = true;
    $c_id = (int)$_GET['edit_course'];
    $c_stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $c_stmt->bind_param("i", $c_id);
    $c_stmt->execute();
    $c_data = $c_stmt->get_result()->fetch_assoc();
    if ($c_data) {
        $c_name = $c_data['course_name'];
        $c_code = $c_data['course_code'];
        $c_credits = $c_data['credits'];
        $c_dept = $c_data['dept_id'];
    }
    else {
        $c_edit_mode = false;
    }
}

// Faculty Edit Logic
$f_edit_mode = false;
$f_name = '';
$f_designation = '';
$f_email = '';
$f_phone = '';
$f_dept = '';
$f_img = '';
$f_id = '';
if (isset($_GET['edit_faculty'])) {
    $f_edit_mode = true;
    $f_id = (int)$_GET['edit_faculty'];
    $f_stmt = $conn->prepare("SELECT * FROM faculty WHERE id = ?");
    $f_stmt->bind_param("i", $f_id);
    $f_stmt->execute();
    $f_data = $f_stmt->get_result()->fetch_assoc();
    if ($f_data) {
        $f_name = $f_data['name'];
        $f_designation = $f_data['designation'];
        $f_email = $f_data['email'];
        $f_phone = $f_data['phone'];
        $f_dept = $f_data['dept_id'];
        $f_img = $f_data['image_url'];
    }
    else {
        $f_edit_mode = false;
    }
}

$edit_mode = false;
$title = '';
$desc = '';
$priority = 'Normal';
$id = '';
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    if ($data) {
        $title = $data['title'];
        $desc = $data['description'];
        $priority = $data['priority'];
    }
    else {
        $edit_mode = false;
    }
}
?>

    <!-- CRUD Form -->
    <div class="card shadow-sm mb-5 border-start border-primary border-4">
        <div class="card-body">
            <h4><i class="bi bi-book"></i> <?php echo $c_edit_mode ? 'Edit Course' : 'Add New Course'; ?></h4>
            <form action="process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $c_id; ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-control" value="<?php echo htmlspecialchars($c_name); ?>" required placeholder="e.g. Data Structures">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Course Code</label>
                        <input type="text" name="course_code" class="form-control" value="<?php echo htmlspecialchars($c_code); ?>" required placeholder="e.g. CSE201">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Credits</label>
                        <input type="number" step="0.5" name="credits" class="form-control" value="<?php echo htmlspecialchars($c_credits); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Department</label>
                        <select name="dept_id" class="form-select" required>
                            <option value="">Select Dept</option>
                            <?php
$departments->data_seek(0);
while ($dept = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $dept['id']; ?>" <?php echo $c_dept == $dept['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($dept['dept_code']); ?>
                                </option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                </div>
                <?php if ($c_edit_mode): ?>
                    <button type="submit" name="update_course" class="btn btn-success">Update Course</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                <?php
else: ?>
                    <button type="submit" name="save_course" class="btn btn-primary">Add Course</button>
                <?php
endif; ?>
            </form>
        </div>
    </div>

    <!-- Faculty Management -->
    <div class="card shadow-sm mb-5 border-start border-danger border-4">
        <div class="card-body">
            <h4><i class="bi bi-person-badge"></i> <?php echo $f_edit_mode ? 'Edit Faculty Member' : 'Add New Faculty Member'; ?></h4>
            <form action="process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $f_id; ?>">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($f_name); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Designation</label>
                        <input type="text" name="designation" class="form-control" value="<?php echo htmlspecialchars($f_designation); ?>" required placeholder="e.g. Assistant Professor">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Department</label>
                        <select name="dept_id" class="form-select" required>
                            <option value="">Select Dept</option>
                            <?php $departments->data_seek(0);
while ($d = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>" <?php echo $f_dept == $d['id'] ? 'selected' : ''; ?>><?php echo $d['dept_code']; ?></option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($f_email); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($f_phone); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Image URL</label>
                        <input type="url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($f_img); ?>" placeholder="https://...">
                    </div>
                </div>
                <?php if ($f_edit_mode): ?>
                    <button type="submit" name="update_faculty" class="btn btn-success">Update Faculty</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                <?php
else: ?>
                    <button type="submit" name="save_faculty" class="btn btn-danger">Register Faculty</button>
                <?php
endif; ?>
            </form>
        </div>
    </div>

    <!-- Student Management -->
    <div class="card shadow-sm mb-5 border-start border-info border-4">
        <div class="card-body">
            <h4><i class="bi bi-people"></i> Student Management</h4>
            <form action="process.php" method="POST">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="student_name" class="form-control" required placeholder="John Doe">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Roll No</label>
                        <input type="text" name="student_roll" class="form-control" required placeholder="011191145">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="student_email" class="form-control" required placeholder="john@uiu.ac.bd">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Department</label>
                        <select name="student_dept" class="form-select" required>
                            <?php $departments->data_seek(0);
while ($d = $departments->fetch_assoc()): ?>
                                <option value="<?php echo $d['id']; ?>"><?php echo $d['dept_code']; ?></option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="save_student" class="btn btn-info text-white">Register Student</button>
            </form>
        </div>
    </div>

    <!-- Enrollment Form -->
    <div class="card shadow-sm mb-5 border-start border-warning border-4">
        <div class="card-body">
            <h4><i class="bi bi-person-plus"></i> Assign Course to Student</h4>
            <form action="process.php" method="POST">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Student Roll No</label>
                        <input type="text" name="roll_no" class="form-control" required placeholder="Enter Roll Number (e.g. 011...)">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Select Course</option>
                            <?php
$c_list = $conn->query("SELECT id, course_name, course_code FROM courses ORDER BY course_code ASC");
while ($c = $c_list->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_code'] . " - " . $c['course_name']); ?></option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Academic Session</label>
                        <select name="session_id" class="form-select" required>
                            <option value="">Select Session</option>
                            <?php
$sessions->data_seek(0);
while ($sess = $sessions->fetch_assoc()): ?>
                                <option value="<?php echo $sess['id']; ?>"><?php echo htmlspecialchars($sess['session_name']); ?></option>
                            <?php
endwhile; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" name="save_enrollment" class="btn btn-warning">Assign Course</button>
            </form>
        </div>
    </div>

    <!-- Academic Session Management -->
    <div class="card shadow-sm mb-5 border-start border-success border-4">
        <div class="card-body">
            <h4><i class="bi bi-calendar-event"></i> Academic Session Management</h4>
            <form action="process.php" method="POST" class="mb-4">
                <div class="row align-items-end">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Session Name</label>
                        <input type="text" name="session_name" class="form-control" required placeholder="e.g. Fall 2026">
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="sessionActive">
                            <label class="form-check-label" for="sessionActive">Set as Active</label>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" name="save_session" class="btn btn-success w-100">Add Session</button>
                    </div>
                </div>
            </form>

            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Session Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$sessions->data_seek(0);
while ($sess = $sessions->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sess['session_name']); ?></td>
                            <td>
                                <span class="badge <?php echo $sess['is_active'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $sess['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="process.php?delete_session=<?php echo $sess['id']; ?>" class="text-danger" onclick="return confirm('Delete this session?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php
endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Faculty List Table -->
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h4 class="mb-4">Faculty Directory</h4>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Dept</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
try {
    $f_result = $conn->query("SELECT faculty.*, departments.dept_code FROM faculty LEFT JOIN departments ON faculty.dept_id = departments.id ORDER BY faculty.id DESC");
    while ($f_row = $f_result->fetch_assoc()):
?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($f_row['image_url']); ?>" width="40" height="40" class="rounded-circle shadow-sm" style="object-fit: cover;"></td>
                        <td><strong><?php echo htmlspecialchars($f_row['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($f_row['designation']); ?></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($f_row['dept_code']); ?></span></td>
                        <td><small><?php echo htmlspecialchars($f_row['email']); ?><br><?php echo htmlspecialchars($f_row['phone']); ?></small></td>
                        <td>
                            <a href="admin.php?edit_faculty=<?php echo $f_row['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            <a href="process.php?delete_faculty=<?php echo $f_row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Remove this faculty member?')">Delete</a>
                        </td>
                    </tr>
                    <?php
    endwhile; ?>
                    <?php if ($f_result->num_rows == 0): ?>
                        <tr><td colspan="6" class="text-center">No faculty members found.</td></tr>
                    <?php
    endif; ?>
                    <?php
}
catch (mysqli_sql_exception $e) {
    echo "<tr><td colspan='6' class='text-center text-danger'>Error: Could not retrieve faculty list.</td></tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CRUD Form -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4><?php echo $edit_mode ? 'Edit Notice' : 'Add New Notice'; ?></h4>
            <form action="process.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Priority</label>
                    <select name="priority" class="form-select">
                        <option value="Normal" <?php echo $priority == 'Normal' ? 'selected' : ''; ?>>Normal</option>
                        <option value="Important" <?php echo $priority == 'Important' ? 'selected' : ''; ?>>Important</option>
                        <option value="Urgent" <?php echo $priority == 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="noticeDesc" class="form-control" rows="4" maxlength="1000" required><?php echo htmlspecialchars($desc); ?></textarea>
                    <div class="form-text text-end"><span id="charCount">0</span>/1000 characters</div>
                </div>
                <?php if ($edit_mode): ?>
                    <button type="submit" name="update" class="btn btn-success">Update Notice</button>
                    <a href="admin.php" class="btn btn-secondary">Cancel</a>
                <?php
else: ?>
                    <button type="submit" name="save" class="btn btn-primary">Publish Notice</button>
                <?php
endif; ?>
            </form>
        </div>
    </div>

    <!-- List Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
try {
    $result = $conn->query("SELECT * FROM notices ORDER BY id DESC");
    while ($row = $result->fetch_assoc()):
?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td>
                            <?php
        $badgeClass = 'bg-secondary';
        if ($row['priority'] == 'Important')
            $badgeClass = 'bg-info';
        if ($row['priority'] == 'Urgent')
            $badgeClass = 'bg-danger';
?>
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo $row['priority']; ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                        <td>
                            <a href="admin.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            <a href="process.php?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this notice?')">Delete</a>
                        </td>
                    </tr>
                    <?php
    endwhile; ?>
                    <?php if ($result->num_rows == 0): ?>
                        <tr><td colspan="4" class="text-center">No notices found.</td></tr>
                    <?php
    endif;

}
catch (mysqli_sql_exception $e) {
    echo "<tr><td colspan='4' class='text-center text-danger'>Error: Could not retrieve notices. Ensure the database table exists.</td></tr>";
}?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Course List Table -->
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h4 class="mb-4">Course List</h4>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Credits</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
try {
    $c_result = $conn->query("SELECT courses.*, departments.dept_code FROM courses LEFT JOIN departments ON courses.dept_id = departments.id ORDER BY course_code ASC");
    while ($c_row = $c_result->fetch_assoc()):
?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($c_row['course_code']); ?></strong></td>
                        <td><?php echo htmlspecialchars($c_row['course_name']); ?></td>
                        <td><?php echo $c_row['credits']; ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($c_row['dept_code']); ?></span></td>
                        <td>
                            <a href="admin.php?edit_course=<?php echo $c_row['id']; ?>" class="btn btn-sm btn-info text-white">Edit</a>
                            <a href="process.php?delete_course=<?php echo $c_row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Delete this course?')">Delete</a>
                        </td>
                    </tr>
                    <?php
    endwhile; ?>
                    <?php if ($c_result->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center">No courses found.</td></tr>
                    <?php
    endif; ?>
                    <?php
}
catch (mysqli_sql_exception $e) {
    echo "<tr><td colspan='5' class='text-center text-danger'>Error: Could not retrieve courses.</td></tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Enrollment List Table -->
    <div class="card shadow-sm mt-5">
        <div class="card-body">
            <h4 class="mb-4">Student Enrollments</h4>
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Roll No</th>
                        <th>Course</th>
                        <th>Session</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
try {
    $e_query = "SELECT enrollments.id, students.name as s_name, students.roll_no, courses.course_code, academic_sessions.session_name 
                                    FROM enrollments 
                                    JOIN students ON enrollments.student_id = students.id 
                                    JOIN courses ON enrollments.course_id = courses.id 
                                    JOIN academic_sessions ON enrollments.session_id = academic_sessions.id 
                                    ORDER BY enrollments.id DESC";
    $e_result = $conn->query($e_query);
    while ($e_row = $e_result->fetch_assoc()):
?>
                    <tr>
                        <td><?php echo htmlspecialchars($e_row['s_name']); ?></td>
                        <td><?php echo htmlspecialchars($e_row['roll_no']); ?></td>
                        <td><strong><?php echo htmlspecialchars($e_row['course_code']); ?></strong></td>
                        <td><?php echo htmlspecialchars($e_row['session_name']); ?></td>
                        <td>
                            <a href="process.php?delete_enrollment=<?php echo $e_row['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Remove this enrollment?')">Delete</a>
                        </td>
                    </tr>
                    <?php
    endwhile; ?>
                    <?php if ($e_result->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center">No enrollments found.</td></tr>
                    <?php
    endif; ?>
                    <?php
}
catch (mysqli_sql_exception $e) {
    echo "<tr><td colspan='5' class='text-center text-danger'>Error: Could not retrieve enrollments.</td></tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Character Counter for Notice Description
    const textarea = document.getElementById('noticeDesc');
    const charCount = document.getElementById('charCount');

    const updateCount = () => {
        charCount.textContent = textarea.value.length;
    };

    textarea.addEventListener('input', updateCount);
    window.addEventListener('DOMContentLoaded', updateCount); // Initial count for edit mode

    // AJAX REST API Search
    const apiSearch = document.getElementById('apiSearch');
    const searchResults = document.getElementById('searchResults');

    apiSearch.addEventListener('input', async (e) => {
        const term = e.target.value;
        if (term.length < 2) { searchResults.innerHTML = ''; return; }
        
        const response = await fetch(`api.php?search=${term}`);
        const data = await response.json();
        
        searchResults.innerHTML = data.map(s => `
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${s.name}</h6>
                    <small class="text-muted">Roll: ${s.roll_no}</small>
                </div>
                <span class="badge bg-primary rounded-pill">${s.dept_code}</span>
            </div>
        `).join('');
    });
</script>
</body>
</html>