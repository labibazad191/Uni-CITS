<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uni-CITS | Computer Science Department</title>
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
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="assets/img/uiu.png" alt="Logo" width="30" height="30" class="d-inline-block align-text-top me-2">
            <span class="fw-bold">Uni-CITS</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="faculty.php">Faculty</a></li>
                <li class="nav-item"><a class="nav-link" href="#notices">Notices</a></li>
                <li class="nav-item"><a class="btn btn-primary ms-lg-3" href="admin.php">Admin Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<header class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Department of Computer Science</h1>
        <p class="lead">Innovating the future through excellence in technology and research.</p>
    </div>
</header>

<!-- About Section -->
<section id="about" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>About the Department</h2>
                <p>We provide a world-class environment for learning and research in computing. Our curriculum is designed to meet the demands of the modern tech industry.</p>
            </div>
            <div class="col-md-6">
                <img src="assets/img/uiu_logo.png" class="img-fluid rounded shadow" alt="About Image">
            </div>
        </div>
    </div>
</section>

<!-- Faculty Section -->
<section id="faculty" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Our Distinguished Faculty</h2>
        <div class="row">
            <?php
try {
  $faculty_res = $conn->query("SELECT * FROM faculty ORDER BY id DESC");
  if ($faculty_res && $faculty_res->num_rows > 0):
    while ($faculty = $faculty_res->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card faculty-card shadow-sm">
                            <img src="<?php echo htmlspecialchars($faculty['image_url']); ?>" class="card-img-top" alt="Faculty">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?php echo htmlspecialchars($faculty['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($faculty['designation']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
    endwhile;
  else: ?>
                    <div class="col-12 text-center"><p class="text-muted">Faculty profiles coming soon.</p></div>
                <?php
  endif;
}
catch (mysqli_sql_exception $e) {
  echo '<div class="col-12 text-center"><p class="text-danger">Error loading faculty profiles.</p></div>';
}?>
        </div>
    </div>
</section>

<!-- Notice Board Section -->
<section id="notices" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">Recent Notices</h2>
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <input type="text" id="noticeSearch" class="form-control shadow-sm" placeholder="Search notices by title or content...">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="list-group notice-board">
<?php
try {
  $result = $conn->query("SELECT * FROM notices ORDER BY created_at DESC");
  if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()): ?>
                            <div class="list-group-item notice-item p-4 shadow-sm">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 text-primary"><?php echo htmlspecialchars($row['title']); ?></h5>
                                    <small class="text-muted"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                                </div>
                                <p class="mb-1 text-dark"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                            </div>
                        <?php
    endwhile;
  else: ?>
                        <p class="text-center">No notices available at this time.</p>
                    <?php
  endif;
}
catch (mysqli_sql_exception $e) {
  echo '<p class="text-center text-danger">Notice board is currently unavailable.</p>';
}?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container text-center">
        <p>&copy; 2023 University Computer Science Department. All Rights Reserved.</p>
        <div>
            <a href="#" class="text-white me-3">Privacy Policy</a>
            <a href="#" class="text-white">Contact Us</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Real-time Notice Filter
    document.getElementById('noticeSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const notices = document.querySelectorAll('.notice-item');
        
        notices.forEach(notice => {
            const title = notice.querySelector('h5').textContent.toLowerCase();
            const desc = notice.querySelector('p').textContent.toLowerCase();
            
            if(title.includes(term) || desc.includes(term)) {
                notice.style.display = 'block';
            } else {
                notice.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>