<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$blog_id = $data['blog_id'];
$user_id = $_SESSION['user_id'];
$action = $data['action'];

if ($action === 'like') {
    // Check if already liked
    $stmt = $conn->prepare("SELECT id FROM blog_likes WHERE blog_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Add new like
        $stmt = $conn->prepare("INSERT INTO blog_likes (blog_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $blog_id, $user_id);
        $stmt->execute();
    }
} else {
    // Remove like
    $stmt = $conn->prepare("DELETE FROM blog_likes WHERE blog_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $blog_id, $user_id);
    $stmt->execute();
}

$stmt->close();
?>