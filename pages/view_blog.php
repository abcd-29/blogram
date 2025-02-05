<?php
include '../includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: ../blogs.php");
    exit();
}

$blog_id = $_GET['id'];
$stmt = $conn->prepare("SELECT b.id, b.title, b.content, b.created_at, u.username FROM blogs b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();

if (!$blog) {
    header("Location: ../blogs.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogram - View Blog</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../blogs.php">Blogs</a>
        <a href="../logout.php">Logout</a>
    </nav>
    <h1><?php echo $blog['title']; ?></h1>
    <p><?php echo $blog['content']; ?></p>
    <p>Posted by: <?php echo $blog['username']; ?> on <?php echo $blog['created_at']; ?></p>
    
    <h2>Comments</h2>
    <form method="POST" action="add_comment.php">
        <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
        <textarea name="comment" placeholder="Add a comment..." required></textarea>
        <button type="submit">Submit</button>
    </form>
    
    <div class="comments-section">
        <?php
        $stmt = $conn->prepare("SELECT c.comment, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.blog_id = ?");
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $comments = $stmt->get_result();
        
        while ($comment = $comments->fetch_assoc()) {
            echo "<div class='comment'><strong>" . $comment['username'] . ":</strong> " . $comment['comment'] . "</div>";
        }
        ?>
    </div>
</body>
</html>