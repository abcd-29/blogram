<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['blog_id'])) {
    echo json_encode(['success' => false, 'message' => 'Blog ID required']);
    exit;
}

try {
    $blog_id = $_GET['blog_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Get like count
    $like_stmt = $conn->prepare("
        SELECT COUNT(*) as like_count 
        FROM blog_likes 
        WHERE blog_id = ?
    ");
    $like_stmt->bind_param("i", $blog_id);
    $like_stmt->execute();
    $like_result = $like_stmt->get_result();
    $like_count = $like_result->fetch_assoc()['like_count'];

    // Check if current user liked the blog
    $user_liked = false;
    if ($user_id) {
        $check_stmt = $conn->prepare("
            SELECT 1 
            FROM blog_likes 
            WHERE blog_id = ? AND user_id = ?
        ");
        $check_stmt->bind_param("ii", $blog_id, $user_id);
        $check_stmt->execute();
        $user_liked = $check_stmt->get_result()->num_rows > 0;
    }

    echo json_encode([
        'success' => true,
        'likes' => $like_count,
        'user_liked' => $user_liked
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 