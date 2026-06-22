<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$service_id = $_POST['service_id'];
$status = $_POST['status'];

// Start transaction for consistency
$conn->begin_transaction();

try {
    // Fetch current mechanic assignment and slot_id
    $result = $conn->query("SELECT assigned_mechanic_id, slot_id FROM service_rq WHERE service_id = $service_id");
    if (!$result || $result->num_rows === 0) {
        throw new Exception('Service request not found');
    }
    $row = $result->fetch_assoc();
    $mechanic_id = $row['assigned_mechanic_id'];
    $slot_id = $row['slot_id'];

    // Enforce constraint: Cannot approve without mechanic
    if ($status === 'Approved' && !$mechanic_id) {
        echo json_encode(['success' => false, 'message' => 'Cannot approve without assigning a mechanic', 'previous_status' => 'Pending']);
        exit;
    }

    // Update service request status
    $stmt = $conn->prepare("UPDATE service_rq SET request_status = ?, service_status = 'Assigned' WHERE service_id = ?");
    $stmt->bind_param("si", $status, $service_id);
    $stmt->execute();

    // Adjust slot bookings if rejected
    if ($status === 'Rejected' && $slot_id) {
        $slot_stmt = $conn->prepare("UPDATE service_slots SET current_bookings = current_bookings - 1 WHERE id = ?");
        $slot_stmt->bind_param("i", $slot_id);
        $slot_stmt->execute();
        $slot_stmt->close();
    }

    // Get user_id for customer notification
    $stmt = $conn->prepare("SELECT user_id FROM service_rq WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_id = $result->fetch_assoc()['user_id'];

    // Delete previous service_request notifications for customer
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'service_request' AND related_id = ? ");
    $stmt->bind_param("ii", $user_id, $service_id);
    $stmt->execute();

    // Insert customer notification
    $customer_message = "Service request #$service_id has been " . $status;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, 'service_request', ?)");
    $stmt->bind_param("isi", $user_id, $customer_message, $service_id);
    $stmt->execute();

    // Insert mechanic notification if approved
    if ($status === 'Approved' && $mechanic_id) {
        $stmt = $conn->prepare("select user_id from mechanic where id = ?");
        $stmt->bind_param("i", $mechanic_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $mechanic_id = $result->fetch_assoc()['user_id'];

        // Delete previous mechanic notifications for this service
        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'mechanic_update' AND related_id = ? AND is_read = 1");
        $stmt->bind_param("ii", $mechanic_id, $service_id);
        $stmt->execute();

        // Insert new mechanic notification
        $mechanic_message = "You have been assigned to service request #$service_id";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, 'mechanic_update', ?)");
        $stmt->bind_param("isi", $mechanic_id, $mechanic_message, $service_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'previous_status' => 'Pending']);
}

$stmt->close();
$conn->close();
?>