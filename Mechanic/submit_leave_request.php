<?php
session_start();
header('Content-Type: application/json');

include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to submit a leave request.']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get mechanic ID from the mechanic table
$query = "SELECT id FROM mechanic WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $mechanic_id = $row['id'];
} else {
    echo json_encode(['success' => false, 'message' => 'Mechanic profile not found.']);
    exit;
}

// Check for existing active leave requests
$query = "SELECT COUNT(*) FROM mechanic_leave WHERE mechanic_id = ? AND status NOT IN ('Rejected', 'Cancelled')";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->fetch_row()[0] > 0) {
    echo json_encode(['success' => false, 'message' => 'You already have an active leave request. Please wait until it is processed or cancelled.']);
    exit;
}

// Get form data
$start_date = $_POST['startDate'] ?? '';
$end_date = $_POST['endDate'] ?? '';
$reason = $_POST['reason'] ?? '';

// Validate inputs
if (empty($start_date) || empty($end_date) || empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (strtotime($end_date) < strtotime($start_date)) {
    echo json_encode(['success' => false, 'message' => 'End date must be after start date.']);
    exit;
}

// Insert leave request into the database
$query = "INSERT INTO mechanic_leave (mechanic_id, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, 'Pending')";
$stmt = $conn->prepare($query);
$stmt->bind_param("isss", $mechanic_id, $start_date, $end_date, $reason);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit leave request. Please try again.']);
}

$stmt->close();
$conn->close();
?>