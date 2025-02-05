<?php
require 'includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['count'] > 0]);
?> 