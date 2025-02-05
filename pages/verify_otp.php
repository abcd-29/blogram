<?php
session_start();

if (!isset($_SESSION['otp']) || !isset($_SESSION['temp_user'])) {
    header("Location: register.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_otp = $_POST['otp'];

    if ($user_otp == $_SESSION['otp']) {
        include '../includes/db.php';
        $temp_user = $_SESSION['temp_user'];

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $temp_user['username'], $temp_user['email'], $temp_user['password']);

        if ($stmt->execute()) {
            session_destroy();
            header("Location: ../login.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Invalid OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogram - Verify OTP</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../login.php">Login</a>
        <a href="../register.php">Register</a>
    </nav>
    <h1>Verify OTP</h1>
    <form method="POST" action="verify_otp.php">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>