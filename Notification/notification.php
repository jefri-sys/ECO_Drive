<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? 'get'; // Default to 'get' if no action specified

try {
    switch ($action) {
        case 'get':
            $limit = max(0, intval($_GET['limit'] ?? 10));
            $offset = max(0, intval($_GET['offset'] ?? 0));
            $unread_only = isset($_GET['unread_only']) && $_GET['unread_only'] === 'true';

            // Unread count
            $count_stmt = $conn->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
            $count_stmt->bind_param("i", $user_id);
            $count_stmt->execute();
            $unread_count = $count_stmt->get_result()->fetch_assoc()['unread_count'];

            if ($limit === 0) {
                echo json_encode(['success' => true, 'unread_count' => $unread_count, 'notifications' => []]);
                break;
            }

            // Fetch notifications
            $query = "SELECT id, message, type, related_id, is_read, created_at FROM notifications WHERE user_id = ?" . 
                     ($unread_only ? " AND is_read = 0" : "") . 
                     " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $user_id, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            $notifications = [];
            while ($row = $result->fetch_assoc()) {
                $row['formatted_date'] = (new DateTime($row['created_at']))->format('M j, g:i a');
                $notifications[] = $row;
            }

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unread_count,
                'total' => count($notifications)
            ]);
            break;

        case 'mark':
            if (isset($_POST['notification_id'])) {
                $notification_id = intval($_POST['notification_id']);
                $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $notification_id, $user_id);
                $stmt->execute();
                echo json_encode(['success' => $stmt->affected_rows > 0, 'message' => 'Notification marked as read']);
            } elseif (isset($_POST['mark_all']) && $_POST['mark_all'] === 'true') {
                $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'All notifications marked as read', 'affected_rows' => $stmt->affected_rows]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No action specified']);
            }
            break;

        case 'add':
            if (isset($_POST['message'], $_POST['type'])) {
                $message = $_POST['message'];
                $type = $_POST['type'];
                $related_id = $_POST['related_id'] ?? null;
                $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, type, related_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issi", $user_id, $message, $type, $related_id);
                $stmt->execute();
                echo json_encode(['success' => true, 'notification_id' => $stmt->insert_id]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Error in notifications.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
}

$conn->close();
?>