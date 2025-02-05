<?php
require 'includes/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];

$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(['exists' => $row['count'] > 0]);
?> 