<?php
include '../includes/auth.php';
include '../includes/db.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
$notifications = getNotifications($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogram - Notifications</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
    </nav>
    <h1>Notifications</h1>
    <?php while ($notification = $notifications->fetch_assoc()): ?>
        <div class="notification">
            <p><?php echo $notification['message']; ?></p>
        </div>
    <?php endwhile; ?>
</body>
</html>