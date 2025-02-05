<?php
session_start();
include 'includes/db.php'; // Include database connection

$response = ['status' => 'error'];

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $response['status'] = 'success';
    }
    $stmt->close();
}

echo json_encode($response);
?>