<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$service_id = $_POST['service_id'];
$mechanic_id = $_POST['mechanic_id'] ?: null;

$stmt = $conn->prepare("UPDATE service_rq SET assigned_mechanic_id = ? WHERE service_id = ?");
$stmt->bind_param("ii", $mechanic_id, $service_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>