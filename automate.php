<?php
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Set timezone (adjust as needed)
date_default_timezone_set('Asia/Kolkata');

// Current date (today)
$current_date = date('Y-m-d');

// Define slot times (adjust as per your needs)
$slots = [
    '09:00:00', // Morning
    '12:00:00', // Afternoon
    '15:00:00'  // Evening
];

for ($i = 1; $i <= 5; $i++) {
    // Calculate the date for day $i ahead
    $tomorrow = date('Y-m-d', strtotime("+$i day", strtotime($current_date)));

    // Check if the slot already exists for tomorrow
    $query = "SELECT id FROM service_slots WHERE slot_date = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $tomorrow);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        foreach ($slots as $slot_time) {
            // Insert new slot if it doesn’t exist
            $insert_query = "
                INSERT INTO service_slots (slot_date, slot_time, max_capacity, current_bookings)
                VALUES (?, ?, 3, 0)
            ";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('ss', $tomorrow, $slot_time);
            if ($stmt->execute()) {
            } else {
                echo "Error creating slot: " . $conn->error . "\n";
            }
        }
    }
    $stmt->close();
}

// 2. Delete slots older than 4 days from today and handle related service requests
$delete_date = date('Y-m-d', strtotime('-4 days', strtotime($current_date)));

// First, find and update service requests before deleting slots
$find_query = "SELECT sr.service_id, sr.user_id, sr.slot_id FROM service_rq sr INNER JOIN service_slots ss ON sr.slot_id = ss.id WHERE ss.slot_date < ? AND sr.service_status != 'Completed'";
$stmt = $conn->prepare($find_query);
$stmt->bind_param('s', $delete_date);
$stmt->execute();
$result = $stmt->get_result();

$current_timestamp = date('Y-m-d H:i:s');

while ($row = $result->fetch_assoc()) {
    $service_id = $row['service_id'];
    $user_id = $row['user_id'];
    
    // Update service request status and cancelled_at
    $update_query = "UPDATE service_rq SET service_status = 'Cancelled',request_status = 'Cancelled',cancelled_at = ? WHERE service_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $current_timestamp, $service_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Insert notification
    $message = "Your service request has been cancelled due to slot unavailability. Please click here to reschedule.";
    $notify_query = "INSERT INTO notifications (user_id, message, type, related_id, is_read, created_at) VALUES (?, ?, 'rescheduling', ?, 0, ?)";
    $notify_stmt = $conn->prepare($notify_query);
    $notify_stmt->bind_param('isis', $user_id, $message, $service_id, $current_timestamp);
    $notify_stmt->execute();
    $notify_stmt->close();
}

// Now delete the old slots
$delete_query = "DELETE FROM service_slots WHERE slot_date < ?";
$stmt = $conn->prepare($delete_query);
$stmt->bind_param('s', $delete_date);
if ($stmt->execute()) {
    $deleted_rows = $stmt->affected_rows;
} else {
    echo "Error deleting slots: " . $conn->error . "\n";
}
$stmt->close();

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Update mechanic availability status
$query = "SELECT mechanic_id, start_date, end_date FROM mechanic_leave WHERE status = 'Approved'";
$result = $conn->query($query);
if (!$result) {
    die("Query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $mechanic_id = $row['mechanic_id'];
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    // Check if current date falls after the leave period
    if ($current_date > $end_date) {
        // Update mechanic availability status to 'Available'
        $update_query = "UPDATE mechanic SET availability_status = 'Available' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('i', $mechanic_id);
        $stmt->execute();
        $stmt->close();

        $query = "DELETE FROM mechanic_leave WHERE mechanic_id = ? AND start_date = ? AND end_date = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iss', $mechanic_id, $start_date, $end_date);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
?>