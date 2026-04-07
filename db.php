<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password
$dbname = 'university';

// Set error reporting before connection
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $conn = new mysqli($host, $user, $pass, $dbname);

  if ($conn->connect_error) {
    throw new Exception("Connection failed: " . $conn->connect_error);
  }

  // Verify that the required administrative tables exist
  $required_tables = ['notices', 'admins'];
  foreach ($required_tables as $table) {
    $check = $conn->query("SHOW TABLES LIKE '$table'");
    if ($check->num_rows == 0) {
      throw new Exception("System core table '$table' is missing.");
    }
  }

}
catch (Exception $e) {
  // Log the error internally (omitted for brevity) and show user-friendly guidance
  http_response_code(503); // Service Unavailable
  die("<html><body style='font-family:sans-serif; text-align:center; padding-top:50px;'>
            <h2 style='color:#d9534f;'>System Initialization Required</h2>
            <p class='text-muted'>{$e->getMessage()}</p>
            <div style='margin-top: 20px;'>
                <a href='setup.php' style='background:#0275d8; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;'>Run Database Setup</a>
            </div>
         </body></html>");
}
?>
