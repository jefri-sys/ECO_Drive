<?php
$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die("Environment file not found.");
}

$env = parse_ini_file($envFile);
$host = isset($env['DB_HOST']) ? $env['DB_HOST'] : '';
$user = isset($env['DB_USER']) ? $env['DB_USER'] : '';
$pass = isset($env['DB_PASS']) ? $env['DB_PASS'] : '';
$dbname = isset($env['DB_NAME']) ? $env['DB_NAME'] : '';

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    // Detect if the request expects a JSON response (e.g., AJAX)
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        die(json_encode(['success' => false, 'message' => 'Connection failed']));
    } else {
        die("Connection failed: " . $conn->connect_error);
    }
}
?>
