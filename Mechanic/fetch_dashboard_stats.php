<?php
session_start();
header('Content-Type: application/json');

// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Get mechanic ID from the mechanic table based on user_id
$user_id = $_SESSION['user_id'];
$query = "SELECT id FROM mechanic WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Mechanic not found']);
    exit();
}
$mechanic_id = $result->fetch_assoc()['id'];

// Initialize response array
$response = [];

// 1. Assigned Services (specific to this mechanic)
$assigned_query = "SELECT COUNT(*) as assigned_services 
                   FROM service_rq 
                   WHERE assigned_mechanic_id = ? AND service_status IN ('Assigned', 'Servicing')";
$stmt = $conn->prepare($assigned_query);
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
$response['a_requests'] = $result->fetch_assoc()['assigned_services'] ?? 0;

// 3. servicing status
$servicing_query = "SELECT COUNT(*) as servicing_status 
                  FROM service_rq 
                  WHERE assigned_mechanic_id = ? AND service_status = 'servicing'";
$stmt = $conn->prepare($servicing_query);
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
$response['s_status'] = $result->fetch_assoc()['servicing_status'] ?? 0;

// 2. Completed Services (specific to this mechanic)
$completed_query = "SELECT COUNT(*) as completed_services 
                    FROM service_rq 
                    WHERE assigned_mechanic_id = ? AND service_status = 'Completed'";
$stmt = $conn->prepare($completed_query);
$stmt->bind_param("i", $mechanic_id);
$stmt->execute();
$result = $stmt->get_result();
$response['c_requests'] = $result->fetch_assoc()['completed_services'] ?? 0;

// Output JSON response
echo json_encode($response);

// Close database connection
$stmt->close();
$conn->close();
?>