<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'] ?? '';
$bio = $_POST['bio'] ?? '';
$interests = $_POST['interests'] ?? '';

if (empty($name) || empty($bio)) {
    echo json_encode(['status' => 'error', 'message' => 'Name and bio are required']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE users SET name = ?, bio = ?, interests = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $name, $bio, $interests, $user_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Failed to update profile');
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
} 