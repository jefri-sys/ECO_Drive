<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$service_id = $_POST['service_id'];
$service_status = $_POST['service_status'];
$spare_part_ids = $_POST['spare_part_ids'] ?? [];
$quantities = $_POST['quantities'] ?? [];

$conn->begin_transaction();
try {
    // Update service status (fixed syntax: use comma instead of 'and')
    $stmt = $conn->prepare("UPDATE service_rq SET service_status = ?, completion_date = CURDATE() WHERE service_id = ?");
    $stmt->bind_param("si", $service_status, $service_id);
    $stmt->execute();

    // Record spare parts usage
    if (!empty($spare_part_ids)) {
        $stmt = $conn->prepare("INSERT INTO service_spare_parts (service_id, spare_part_id, quantity_used) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($spare_part_ids); $i++) {
            if ($spare_part_ids[$i]) { // Only process if a part is selected
                $quantity = (int)$quantities[$i];
                $part_id = (int)$spare_part_ids[$i];
                $stmt->bind_param("iii", $service_id, $part_id, $quantity);
                $stmt->execute();

                // Update inventory
                $conn->query("UPDATE inventory SET quantity = quantity - $quantity WHERE id = $part_id");
            }
        }
    }

    // Get the user_id for this service request
    $stmt = $conn->prepare("SELECT user_id FROM service_rq WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_id = $result->fetch_assoc()['user_id'];

    // Delete previous service_request notifications for this service_id and user_id
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'service_request' AND related_id = ?");
    $stmt->bind_param("ii", $user_id, $service_id);
    $stmt->execute();

    // Insert new notification with updated status
    $message = "Service request #$service_id status updated to: $service_status";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, 'service_request', ?)");
    $stmt->bind_param("isi", $user_id, $message, $service_id);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>