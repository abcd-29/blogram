<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['blog_id'])) {
    echo json_encode(['success' => false, 'message' => 'Blog ID is required']);
    exit;
}

try {
    $blog_id = intval($_GET['blog_id']);
    
    $stmt = $conn->prepare("
        SELECT 
            c.comment,
            c.created_at,
            u.username,
            u.profile_picture as user_image
        FROM blog_comments c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.blog_id = ?
        ORDER BY c.created_at DESC
    ");
    
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = [
            'comment' => htmlspecialchars($row['comment']),
            'created_at' => $row['created_at'],
            'username' => htmlspecialchars($row['username']),
            'user_image' => $row['user_image'] ? htmlspecialchars($row['user_image']) : null
        ];
    }
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load comments'
    ]);
}

$stmt->close();
$conn->close();
?> 