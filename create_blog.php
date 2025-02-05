<?php
session_start();
require_once 'includes/db.php';

// Set JSON response header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to create a blog']);
    exit;
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

try {
    // Basic validation
    if (!isset($_POST['title'], $_POST['description'])) {
        throw new Exception('Missing required fields');
    }

    // Get and sanitize input
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];
    $hashtags = isset($_POST['hashtags']) ? json_decode($_POST['hashtags']) : [];

    // Validate input lengths
    if (empty($title)) {
        throw new Exception('Title is required');
    }
    if (empty($description)) {
        throw new Exception('Description is required');
    }

    // Image handling
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Please select an image');
    }

    $file = $_FILES['image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Validate image
    if (!in_array($file['type'], $allowed_types)) {
        throw new Exception('Invalid image type. Only JPG, PNG and GIF are allowed');
    }

    if ($file['size'] > $max_size) {
        throw new Exception('Image size should be less than 5MB');
    }

    // Create upload directory if it doesn't exist
    $upload_dir = 'uploads/blog_images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        throw new Exception('Failed to upload image');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert blog post
        $stmt = $conn->prepare("
            INSERT INTO blogs (user_id, title, description, image_url, hashtags, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");

        $hashtags_json = !empty($hashtags) ? json_encode($hashtags) : null;
        
        $stmt->bind_param("issss", 
            $user_id, 
            $title, 
            $description, 
            $upload_path, 
            $hashtags_json
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to create blog post');
        }

        $blog_id = $stmt->insert_id;

        // Get the created blog with user details
        $select_stmt = $conn->prepare("
            SELECT b.*, u.name, u.username, u.profile_picture 
            FROM blogs b 
            JOIN users u ON b.user_id = u.user_id 
            WHERE b.id = ?
        ");

        $select_stmt->bind_param("i", $blog_id);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        $blog = $result->fetch_assoc();

        if (!$blog) {
            throw new Exception('Failed to fetch blog details');
        }

        // Commit transaction
        $conn->commit();

        // Prepare success response
        $response = [
            'success' => true,
            'message' => 'Blog created successfully',
            'blog' => [
                'id' => $blog_id,
                'title' => htmlspecialchars($blog['title']),
                'description' => htmlspecialchars($blog['description']),
                'image_url' => $blog['image_url'],
                'hashtags' => $hashtags,
                'created_at' => date('M d, Y', strtotime($blog['created_at'])),
                'author' => [
                    'name' => htmlspecialchars($blog['name'] ?: $blog['username']),
                    'profile_picture' => $blog['profile_picture'] ? 
                        'uploads/profile_pictures/' . htmlspecialchars($blog['profile_picture']) : 
                        'assets/default-avatar.png'
                ]
            ]
        ];

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    // Delete uploaded image if exists
    if (isset($upload_path) && file_exists($upload_path)) {
        unlink($upload_path);
    }

    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Close database connection
if (isset($stmt)) $stmt->close();
if (isset($select_stmt)) $select_stmt->close();
$conn->close();

// Send response
echo json_encode($response);
?>