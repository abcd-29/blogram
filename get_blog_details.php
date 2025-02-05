<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $blog_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("
        SELECT id, title, description, image_url, hashtags 
        FROM blogs 
        WHERE id = ? AND user_id = ?
    ");
    
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($blog = $result->fetch_assoc()) {
        echo json_encode([
            'success' => true,
            'blog' => [
                'id' => $blog['id'],
                'title' => $blog['title'],
                'description' => $blog['description'],
                'image_url' => $blog['image_url'],
                'hashtags' => $blog['hashtags']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Blog not found or access denied']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error']);
}

$stmt->close();
$conn->close();
?> 