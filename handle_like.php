<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['blog_id']) || !isset($data['action'])) {
        throw new Exception('Invalid request');
    }

    $blog_id = $data['blog_id'];
    $user_id = $_SESSION['user_id'];
    $action = $data['action'];

    // Start transaction
    $conn->begin_transaction();

    if ($action === 'like') {
        // Add like
        $stmt = $conn->prepare("
            INSERT IGNORE INTO blog_likes (blog_id, user_id)
            VALUES (?, ?)
        ");
    } else {
        // Remove like
        $stmt = $conn->prepare("
            DELETE FROM blog_likes 
            WHERE blog_id = ? AND user_id = ?
        ");
    }

    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();

    // Get updated like count
    $count_stmt = $conn->prepare("
        SELECT COUNT(*) as like_count 
        FROM blog_likes 
        WHERE blog_id = ?
    ");
    $count_stmt->bind_param("i", $blog_id);
    $count_stmt->execute();
    $result = $count_stmt->get_result();
    $like_count = $result->fetch_assoc()['like_count'];

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'likes' => $like_count
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 