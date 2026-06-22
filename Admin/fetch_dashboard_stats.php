<?php
session_start();
header('Content-Type: application/json');

// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Check if the user is an admin (optional security)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Initialize response array
$response = [];

// 1. Total Mechanics
$mechanic_query = "SELECT COUNT(*) as total_mechanics FROM mechanic";
$mechanic_result = $conn->query($mechanic_query);
if ($mechanic_result) {
    $response['mechanics'] = $mechanic_result->fetch_assoc()['total_mechanics'];
} else {
    $response['mechanics'] = 0;
}

// 2. Total Services (active service requests)
$services_query = "SELECT COUNT(*) as total_services FROM service_rq WHERE service_status != 'Cancelled'";
$services_result = $conn->query($services_query);
if ($services_result) {
    $response['services'] = $services_result->fetch_assoc()['total_services'];
} else {
    $response['services'] = 0;
}

// 3. Total Finished Requests
$finished_query = "SELECT COUNT(*) as finished_requests FROM service_rq WHERE service_status = 'Completed'";
$finished_result = $conn->query($finished_query);
if ($finished_result) {
    $response['finished'] = $finished_result->fetch_assoc()['finished_requests'];
} else {
    $response['finished'] = 0;
}

// Output JSON response
echo json_encode($response);

// Close database connection
$conn->close();
?>