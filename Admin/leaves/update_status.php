<?php
header('Content-Type: application/json'); // Add header for JSON response
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

$leave_id = $_POST['leave_id'];
$status = $_POST['status'];

$conn->begin_transaction();
try {
    if ($status === 'Approved') {
        // Update leave status and mechanic availability
        $stmt = $conn->prepare("
            UPDATE mechanic_leave l
            JOIN mechanic m ON m.id = l.mechanic_id
            SET l.status = ?, m.availability_status = 'Not Available'
            WHERE l.id = ?
        ");
        $stmt->bind_param("si", $status, $leave_id);
        $stmt->execute();
    }
    
    // Get user_id and leave details
    $stmt = $conn->prepare("
        SELECT m.user_id, ml.start_date, ml.end_date 
        FROM mechanic m 
        INNER JOIN mechanic_leave ml ON m.id = ml.mechanic_id 
        WHERE ml.id = ?
    ");
    $stmt->bind_param("i", $leave_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        throw new Exception('Leave request or mechanic not found');
    }
    $user_id = $row['user_id'];
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    // Delete previous mechanic_update notifications for this leave
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ? AND type = 'mechanic_update' AND related_id = ?");
    $stmt->bind_param("ii", $user_id, $leave_id);
    $stmt->execute();

    // Insert new notification
    $message = "Your leave request from $start_date to $end_date has been $status";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, 'mechanic_update', ?)");
    $stmt->bind_param("isi", $user_id, $message, $leave_id);
    $stmt->execute();

    if($status === 'Rejected') {
        // Update leave status
        $stmt = $conn->prepare("DELETE FROM mechanic_leave WHERE id = ?");
        $stmt->bind_param("i", $leave_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>