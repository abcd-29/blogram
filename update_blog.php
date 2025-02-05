<?php
session_start();
require_once 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

try {
    $blog_id = $_POST['blog_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $hashtags = isset($_POST['hashtags']) ? trim($_POST['hashtags']) : '';
    
    // Validate inputs
    if (empty($title) || empty($description)) {
        throw new Exception('Title and description are required');
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    // Update blog text content
    $stmt = $conn->prepare("
        UPDATE blogs 
        SET title = ?, description = ?, hashtags = ?, updated_at = NOW()
        WHERE id = ? AND user_id = ?
    ");
    
    $stmt->bind_param("sssii", $title, $description, $hashtags, $blog_id, $_SESSION['user_id']);
    $stmt->execute();
    
    $image_url = null;
    
    // Handle image upload if present
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!in_array($file['type'], $allowed_types)) {
            throw new Exception('Invalid image format. Only JPG, PNG and GIF are allowed.');
        }
        
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $max_size) {
            throw new Exception('Image size should be less than 5MB');
        }
        
        $upload_dir = 'uploads/blog_images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Update image path in database
            $stmt = $conn->prepare("UPDATE blogs SET image_url = ? WHERE id = ?");
            $image_url = $filepath;
            $stmt->bind_param("si", $image_url, $blog_id);
            $stmt->execute();
        } else {
            throw new Exception('Failed to upload image');
        }
    }
    
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Blog updated successfully',
        'image_url' => $image_url
    ]);

} catch (Exception $e) {
    if ($conn->connect_error) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 