<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}
$data = json_decode(file_get_contents('php://input'), true);

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'add_vehicle':
        $vehicleListId = $_POST['vehicleListId'] ?? '';
        $vehicleNumber = $_POST['vehicleNumber'] ?? '';
        $userId = $_POST['userId'] ?? '';

        if (!$vehicleListId || !$vehicleNumber || !$userId) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit();
        }

        $query = "SELECT * FROM vehicle WHERE vehicle_number = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $vehicleNumber, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Vehicle already exists']);
            exit();
        }

        $query = "INSERT INTO vehicle (user_id, vehicle_list_id, vehicle_number) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iis', $userId, $vehicleListId, $vehicleNumber);
        $success = $stmt->execute();

        echo json_encode(['success' => $success, 'error' => $success ? null : $conn->error]);
        $stmt->close();
        break;

    case 'delete_vehicle':
        $vehicleId = $_POST['vehicleId'] ?? '';
        $userId = $_POST['userId'] ?? '';

        if (!$vehicleId || !$userId) {
            echo json_encode(['success' => false, 'error' => 'Missing vehicle ID or user ID']);
            exit();
        }

        $query = "SELECT * FROM service_rq where vehicle_id = ? ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $vehicleId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Vehicle is associated with a service request']);
            $stmt->close();
            exit();
        }

        $checkQuery = "SELECT id FROM vehicle WHERE id = ? AND user_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('ii', $vehicleId, $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'error' => 'Vehicle not found or not owned by user']);
            $checkStmt->close();
            exit();
        }
        $checkStmt->close();

        $query = "DELETE FROM vehicle WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $vehicleId, $userId);
        $success = $stmt->execute();

        echo json_encode(['success' => $success, 'error' => $success ? null : $conn->error]);
        $stmt->close();
        break;

    case 'delete_servicerq':
        $serviceId = $_POST['serviceId'] ?? '';

        if (!$serviceId) {
            echo json_encode(['success' => false, 'error' => 'Missing service ID or user ID']);
            exit();
        }
        $query = "SELECT slot_id FROM service_rq WHERE service_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $serviceId);
        $stmt->execute();
        $result = $stmt->get_result();

        $query = "UPDATE service_slots SET current_bookings = current_bookings - 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $result->fetch_assoc()['slot_id']);
        $stmt->execute();

        $query = "DELETE FROM service_rq WHERE service_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $serviceId);    
        $success = $stmt->execute();

        echo json_encode(['success' => $success, 'error' => $success ? null : $conn->error]);
        $stmt->close();
        break;

    case 'reschedule_service':
        $service_id = $_POST['serviceId'] ?? '';
        $slot_id = $_POST['slotId'] ?? '';
        $date = $_POST['date'] ?? '';

        if (!$service_id || !$slot_id || !$date) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit();
        }

        // Verify the service request belongs to the user and is cancelled
        $check_query = "SELECT service_status, request_status FROM service_rq WHERE service_id = ? AND user_id = ?";
        if (!$stmt = $conn->prepare($check_query)) {
            error_log('Prepare failed: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit();
        }
        $stmt->bind_param('ii', $service_id, $user_id);
        if (!$stmt->execute()) {
            error_log('Execute failed: ' . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit();
        }
        $result = $stmt->get_result();
        $service = $result->fetch_assoc();

        if (!$service || $service['service_status'] !== 'Cancelled' || $service['request_status'] !== 'Cancelled') {
            echo json_encode(['success' => false, 'error' => 'Service request not found or not eligible for rescheduling']);
            exit();
        }

        // Verify the slot is available
        $slot_query = "SELECT current_bookings, max_capacity FROM service_slots WHERE id = ? AND slot_date = ?";
        if (!$stmt = $conn->prepare($slot_query)) {
            error_log('Prepare failed: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit();
        }
        $stmt->bind_param('is', $slot_id, $date);
        if (!$stmt->execute()) {
            error_log('Execute failed: ' . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit();
        }
        $result = $stmt->get_result();
        $slot = $result->fetch_assoc();

        if (!$slot || $slot['current_bookings'] >= $slot['max_capacity']) {
            echo json_encode(['success' => false, 'error' => 'Selected slot is not available']);
            exit();
        }

        // Update the service request with new slot and status
        $update_query = "UPDATE service_rq SET slot_id = ?, service_status = 'Requested', request_status = 'Requested', cancelled_at = NULL WHERE service_id = ?";
        if (!$stmt = $conn->prepare($update_query)) {
            error_log('Prepare failed: ' . $conn->error);
            echo json_encode(['success' => false, 'error' => 'Database error']);
            exit();
        }
        $stmt->bind_param('ii', $slot_id, $service_id);
        if ($stmt->execute()) {
            // Increment current_bookings in service_slots
            $update_slot_query = "UPDATE service_slots SET current_bookings = current_bookings + 1 WHERE id = ?";
            if (!$stmt = $conn->prepare($update_slot_query)) {
                error_log('Prepare failed: ' . $conn->error);
                echo json_encode(['success' => false, 'error' => 'Database error']);
                exit();
            }
            $stmt->bind_param('i', $slot_id);
            if (!$stmt->execute()) {
                error_log('Execute failed: ' . $stmt->error);
                echo json_encode(['success' => false, 'error' => 'Database error']);
                exit();
            }

            echo json_encode(['success' => true]);
        } else {
            error_log('Update failed: ' . $stmt->error);
            echo json_encode(['success' => false, 'error' => 'Failed to update service request']);
        }
        $stmt->close();
        break;

    default :
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $_SESSION['user_id'];
        $vehicle_id = $data['vehicleId'] ?? '';
        $service_ids = $data['serviceIds'] ?? [];
        $plan_id = $data['planId'] ?? '';
        $slot_id = $data['slotId'] ?? '';
        $date = $data['date'] ?? '';
        $notes = $data['notes'] ?? '';

        
        if (!$vehicle_id || empty($service_ids) || !$plan_id || !$slot_id) {
            echo json_encode(['success' => false, 'error' => 'Missing required fields', 
                            'details' => [
                                'vehicle_id' => $vehicle_id,
                                'service_ids' => $service_ids,
                                'plan_id' => $plan_id,
                                'slot_id' => $slot_id
                            ]]);
            exit();
        }
        // Check slot availability
        $query = "SELECT current_bookings, max_capacity FROM service_slots WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $slot_id);
        $stmt->execute();
        $slot = $stmt->get_result()->fetch_assoc();
        if (!$slot || $slot['current_bookings'] >= $slot['max_capacity']) {
            echo json_encode(['success' => false, 'error' => 'Slot is fully booked or invalid']);
            exit();
        }

        // Insert service request
        $query = "INSERT INTO service_rq (user_id, vehicle_id, request_date, slot_id, service_date, request_status, service_status, plan_id, notes) 
                VALUES (?, ?, CURDATE(), ?, ?, 'Pending', 'Requested', ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iiisis', $user_id, $vehicle_id, $slot_id, $date, $plan_id, $notes);
        $stmt->execute();
        $service_rq_id = $conn->insert_id;

        // Link multiple services to request
        $query = "INSERT INTO service_rq_services (service_rq_id, service_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        foreach ($service_ids as $service_id) {
            $stmt->bind_param('ii', $service_rq_id, $service_id);
            $stmt->execute();
        }

        // Update slot bookings
        $query = "UPDATE service_slots SET current_bookings = current_bookings + 1 WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $slot_id);
        $stmt->execute();

        echo json_encode(['success' => true]);
        break;
}

$conn->close();
?>