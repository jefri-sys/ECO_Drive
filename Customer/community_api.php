<?php
// Enable error reporting during debugging (set to 0 for production)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Use output buffering for clean JSON output
ob_start();

session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/S6 PROJECT(TEAM 6)/db_connection.php';

// Check database connection
if ($conn->connect_error) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Verify user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'User not logged in or session expired']);
    exit();
}

// Clean output buffer and set content type
ob_end_clean();
header('Content-Type: application/json');

// Handle JSON request body
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON: ' . json_last_error_msg()]);
    exit();
}
$action = $data['action'] ?? '';

$user_id = (int)$_SESSION['user_id'];
$username = $_SESSION['username'];

try {
    switch ($action) {
        case 'get_posts':
            $query = "
                SELECT p.*, 
                       COUNT(pl.id) as likes_count,
                       EXISTS(SELECT 1 FROM post_likes WHERE post_id = p.id AND user_id = ?) as user_liked,
                       CONCAT(u.fname, ' ', u.lname) as username,
                       p.user_id = ? as is_owner
                FROM community_posts p
                LEFT JOIN user_tbl u ON p.user_id = u.id
                LEFT JOIN post_likes pl ON p.id = pl.post_id
                GROUP BY p.id
                ORDER BY p.created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $posts = [];
            while ($row = $result->fetch_assoc()) {
                $row['created_at'] = date('M d, Y \a\t h:i A', strtotime($row['created_at']));
                $row['user_liked'] = (bool)$row['user_liked'];
                $row['is_owner'] = (bool)$row['is_owner'];
                $posts[] = $row;
            }
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        case 'get_reviews':
            $query = "
                SELECT r.*, 
                       CONCAT(u.fname, ' ', u.lname) as username,
                       r.user_id = ? as is_owner
                FROM community_reviews r
                LEFT JOIN user_tbl u ON r.user_id = u.id
                ORDER BY r.created_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $row['created_at'] = date('M d, Y \a\t h:i A', strtotime($row['created_at']));
                $row['is_owner'] = (bool)$row['is_owner'];
                $reviews[] = $row;
            }
            echo json_encode(['success' => true, 'reviews' => $reviews]);
            break;

        case 'add_post':
            $title = trim($data['title'] ?? '');
            $content = trim($data['content'] ?? '');
            if (empty($title) || empty($content)) {
                echo json_encode(['success' => false, 'error' => 'Title and content are required']);
                exit();
            }
            $query = "INSERT INTO community_posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $user_id, $title, $content);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'post_id' => $conn->insert_id]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to add post: ' . $stmt->error]);
            }
            break;

        case 'add_review':
            $service_type = trim($data['service_type'] ?? '');
            $rating = intval($data['rating'] ?? 0);
            $content = trim($data['content'] ?? ''); // Ensure content is captured
            
            // Log incoming data for debugging
            error_log("add_review data: " . json_encode($data));
            
            if (empty($service_type) || $rating < 1 || $rating > 5 || empty($content)) {
                echo json_encode(['success' => false, 'error' => 'All fields (service_type, rating 1-5, content) are required']);
                exit();
            }
            
            $query = "INSERT INTO community_reviews (user_id, service_type, rating, content, created_at) 
                     VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
                exit();
            }
            $stmt->bind_param('isis', $user_id, $service_type, $rating, $content); // Changed to 'isis' for correct order
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'review_id' => $conn->insert_id]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to add review: ' . $stmt->error]);
            }
            $stmt->close();
            break;

        case 'like_post':
            $post_id = intval($data['post_id'] ?? 0);
            if ($post_id === 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
                exit();
            }
            $query = "SELECT id FROM community_posts WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $post_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['success' => false, 'error' => 'Post not found']);
                exit();
            }
            $query = "SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $post_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $query = "DELETE FROM post_likes WHERE post_id = ? AND user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $post_id, $user_id);
                $action = 'unliked';
            } else {
                $query = "INSERT INTO post_likes (post_id, user_id, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $post_id, $user_id);
                $action = 'liked';
            }
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'action' => $action]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update like: ' . $stmt->error]);
            }
            break;

        case 'delete_post':
            $post_id = intval($data['post_id'] ?? 0);
            if ($post_id === 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
                exit();
            }
            $query = "SELECT id FROM community_posts WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $post_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['success' => false, 'error' => 'Post not found or no permission']);
                exit();
            }
            $conn->begin_transaction();
            try {
                $query = "DELETE FROM post_likes WHERE post_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('i', $post_id);
                $stmt->execute();
                $query = "DELETE FROM community_posts WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ii', $post_id, $user_id);
                if ($stmt->execute()) {
                    $conn->commit();
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception('Failed to delete post: ' . $stmt->error);
                }
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'delete_review':
            $review_id = intval($data['review_id'] ?? 0);
            if ($review_id === 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid review ID']);
                exit();
            }
            $query = "SELECT id FROM community_reviews WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $review_id, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                echo json_encode(['success' => false, 'error' => 'Review not found or no permission']);
                exit();
            }
            $query = "DELETE FROM community_reviews WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $review_id, $user_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete review: ' . $stmt->error]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Community API exception: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}

$conn->close();
?>