<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$blog_id = $data['blog_id'];
$user_id = $_SESSION['user_id'];
$comment = trim($data['comment']);

try {
    // Insert comment
    $stmt = $conn->prepare("INSERT INTO blog_comments (blog_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $blog_id, $user_id, $comment);
    $stmt->execute();
    
    // Notify blog owner
    $notify_stmt = $conn->prepare("
        INSERT INTO notifications (user_id, type, related_id, from_user_id)
        SELECT user_id, 'comment', ?, ?
        FROM blogs
        WHERE id = ?
    ");
    $notify_stmt->bind_param("iii", $blog_id, $user_id, $blog_id);
    $notify_stmt->execute();
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 