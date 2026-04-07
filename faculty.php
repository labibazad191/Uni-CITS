<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Directory | Uni-CITS</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/img/uiu.png">
    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/img/uiu.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
            <span class="fw-bold">Uni-CITS</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php#about">About</a></li>
                <li class="nav-item"><a class="nav-link active" href="faculty.php">Faculty</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php#notices">Notices</a></li>
                <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="admin.php">Admin Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Our Distinguished Faculty</h1>
        <p class="lead">Meet the experts leading our academic excellence.</p>
    </div>
</header>

<!-- Faculty List Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <?php
try {
    // Fetch faculty with their department code
    $faculty_res = $conn->query("SELECT faculty.*, departments.dept_code 
                                           FROM faculty 
                                           LEFT JOIN departments ON faculty.dept_id = departments.id 
                                           ORDER BY id DESC");
    if ($faculty_res && $faculty_res->num_rows > 0):
        while ($faculty = $faculty_res->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card faculty-card shadow-sm h-100">
                            <img src="<?php echo htmlspecialchars($faculty['image_url']); ?>" class="card-img-top" alt="Faculty">
                            <div class="card-body text-center d-flex flex-column">
                                <h5 class="card-title fw-bold"><?php echo htmlspecialchars($faculty['name']); ?></h5>
                                <p class="card-text text-primary fw-semibold mb-1"><?php echo htmlspecialchars($faculty['designation']); ?></p>
                                <p class="text-muted small mb-3"><?php echo htmlspecialchars($faculty['dept_code'] ?? 'Faculty'); ?></p>
                                <div class="border-top pt-3 mt-auto">
                                    <a href="mailto:<?php echo htmlspecialchars($faculty['email']); ?>" class="btn btn-sm btn-outline-primary">
                                        Contact Faculty
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
        endwhile;
    else: ?>
                    <div class="col-12 text-center py-5"><h3 class="text-muted">Faculty profiles coming soon.</h3></div>
                <?php
    endif;
}
catch (mysqli_sql_exception $e) {
    echo '<div class="col-12 text-center py-5"><p class="text-danger">Error loading faculty profiles. Please check database connection.</p></div>';
}?>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> University Computer Science Department. All Rights Reserved.</p>
        <div>
            <a href="#" class="text-white me-3 text-decoration-none">Privacy Policy</a>
            <a href="#" class="text-white text-decoration-none">Contact Us</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>