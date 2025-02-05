<?php
session_start();
include 'includes/db.php'; // Include database connection

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

if (!isset($_FILES['profile_picture']) || $_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
    $response['message'] = 'No file uploaded or upload error';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];
$upload_dir = 'uploads/profile_pictures/';

// Create directory if it doesn't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique filename
$file_extension = 'png';
$file_name = $user_id . '_' . time() . '.' . $file_extension;
$target_file = $upload_dir . $file_name;

// Remove old profile picture if exists
$stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($old_picture);
$stmt->fetch();
$stmt->close();

if ($old_picture && file_exists($upload_dir . $old_picture)) {
    unlink($upload_dir . $old_picture);
}

// Upload new picture
if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
    // Update database
    $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
    $stmt->bind_param("si", $file_name, $user_id);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile picture updated successfully';
    } else {
        $response['message'] = 'Database update failed';
        // Remove uploaded file if database update fails
        if (file_exists($target_file)) {
            unlink($target_file);
        }
    }
    $stmt->close();
} else {
    $response['message'] = 'Failed to move uploaded file';
}

header('Content-Type: application/json');
echo json_encode($response);
?>
