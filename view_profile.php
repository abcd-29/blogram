<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user profile data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT user_id, username, email, profile_picture, bio, created_at, interests, name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch user's blogs
$stmt = $conn->prepare("
    SELECT 
        b.*,
        (SELECT COUNT(*) FROM blog_comments WHERE blog_id = b.id) as comment_count,
        (SELECT COUNT(*) FROM blog_likes WHERE blog_id = b.id) as like_count
    FROM blogs b 
    WHERE b.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$blogs = [];
while ($row = $result->fetch_assoc()) {
    $blogs[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'image_url' => $row['image_url'],
        'comment_count' => $row['comment_count'],
        'like_count' => $row['like_count'],
        'created_at' => $row['created_at']
    ];
}
$totalBlogs = count($blogs);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #1E1E2E 0%, #2D2D44 100%);
            /* Alternative gradients you might like (uncomment to try):
            background: linear-gradient(135deg, #1A1A2E 0%, #16213E 100%);
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            background: linear-gradient(135deg, #1F1F3A 0%, #2E2E5A 100%);
            */
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .profile-container {
            max-width: 800px;
            width: 85%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            margin: 20px auto;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .profile {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            margin-bottom: 30px;
        }

        .profile-left {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .profile-right {
            flex-grow: 1;
        }

        .profile img {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .profile img:hover {
            transform: scale(1.05);
        }

        .profile-info {
            text-align: center;
            color: white;
            width: 100%;
        }

        .username {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .name {
            font-size: 24px;
            font-weight: 600;
            color: white;
            text-align: center;
            margin-top: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .bio-section {
            margin-bottom: 20px;
        }

        .bio-label {
            font-size: 16px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }

        .bio-content {
            font-size: 18px;
            line-height: 1.6;
            color: white;
        }

        .interests-section {
            margin-top: 20px;
        }

        .interests-label {
            font-size: 16px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }

        .interests-content {
            font-size: 18px;
            line-height: 1.6;
            color: white;
        }

        .blogs {
            margin-top: 30px;
        }

        .total-blogs {
            color: white;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .blog-container {
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        .post {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 200px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow: hidden;
        }

        .post-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .blog-actions {
            margin-top: auto;
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 15px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .action-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.8);
        }

        .action-button:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .action-button i {
            font-size: 1.1rem;
        }

        /* Ensure content doesn't push buttons out */
        .post-description {
            margin-bottom: 20px;
        }

        .blog-hashtags {
            margin-bottom: 20px;
        }

        .post-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .post-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .username {
            color: white;
            font-weight: 500;
            font-size: 1rem;
            line-height: 1.2;
        }

        .post-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            margin-top: 2px;
        }

        .post-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .post-header .title {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        .post-image {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
        }

        .post-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .post:hover .post-image img {
            transform: scale(1.05);
        }

        .post-footer {
            margin-top: auto; /* Push footer to bottom */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
        }

        .comments, .likes {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 20px;
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .comments {
            cursor: pointer;
        }

        .comments:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .likes {
            cursor: default;
        }

        .comments i, .likes i {
            font-size: 1.1rem;
        }

        .nav-actions {
            display: flex;
            align-items: center;
        }

        .edit-profile-btn {
            background: linear-gradient(45deg, #2196F3 0%, #1976D2 50%, #1565C0 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-profile-btn:hover {
            background: linear-gradient(45deg, #1E88E5 0%, #1565C0 50%, #0D47A1 100%);
            transform: translateY(-2px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .blog-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .blog-grid {
                grid-template-columns: 1fr;
                padding: 10px;
            }
            .post-user .username {
                font-size: 0.9rem;
            }
            
            .post-user img {
                width: 35px;
                height: 35px;
            }
            
            .blog-options-toggle {
                width: 25px;
                height: 25px;
            }
            .post-date {
                font-size: 0.7rem;
            }
        }

        .section-divider {
            width: 100%;
            margin: 30px 0;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .divider-line {
            height: 1px;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0),
                rgba(255, 255, 255, 0.5),
                rgba(255, 255, 255, 0)
            );
            flex-grow: 1;
        }

        .section-title {
            color: white;
            font-size: 20px;
            font-weight: 600;
            margin: 0 20px;
            padding: 0 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            padding: 8px 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .section-title:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .blog-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
        }

        .stat-item {
            text-align: center;
            color: white;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            padding: 10px 25px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.8;
        }

        .dropdown-menu {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .dropdown-toggle {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .dropdown-toggle span {
            font-size: 16px;
            font-weight: 500;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            min-width: 200px;
            display: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dropdown-content.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .dropdown-item {
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .dropdown-item i {
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 5px 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .comment-dialog-content {
            background: #1a1a1a;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            padding: 20px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .comment-header h3 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }

        .close-btn {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: white;
        }

        .comments-container {
            overflow-y: auto;
            max-height: calc(80vh - 80px);
            padding-right: 10px;
        }

        .comment-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            color: white;
        }

        .comment-user {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .comment-user img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .comment-user-name {
            font-weight: 500;
            color: #2196F3;
        }

        .comment-date {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.5);
            margin-left: auto;
        }

        .comment-text {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
        }

        /* Custom scrollbar for comments container */
        .comments-container::-webkit-scrollbar {
            width: 6px;
        }

        .comments-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }

        .comments-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .blog-options {
            position: relative;
        }

        .blog-options-toggle {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .blog-options-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .blog-options-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(30, 30, 30, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            min-width: 150px;
            display: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 100;
        }

        .blog-options-menu.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        .blog-option-item {
            padding: 10px 15px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .blog-option-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .blog-option-item.delete-option {
            color: #ff4444;
        }

        .blog-option-item.delete-option:hover {
            background: rgba(255, 68, 68, 0.1);
        }

        .blog-option-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 5px 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dialog {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .dialog-content {
            background: #1a1a1a;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            padding: 20px;
            position: relative;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-height: 90vh;
            overflow-y: auto;
        }

        .dialog-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .dialog-header h3 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            font-size: 14px;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .current-image {
            margin-top: 10px;
            max-width: 200px;
        }

        .current-image img {
            width: 100%;
            border-radius: 6px;
        }

        .dialog-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .save-btn {
            background: #2196F3;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .save-btn:hover {
            background: #1976D2;
        }

        .warning-text {
            color: #ff4444;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .success-message {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s;
            z-index: 1000;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }

        /* Update post content styles */
        .post-content {
            padding: 15px;
        }

        .post-image {
            margin-bottom: 15px;
        }

        .post-image img {
            width: 100%;
            border-radius: 8px;
            object-fit: cover;
        }

        .post-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .post-title, .post-description {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .post-title label, .post-description label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-right: 8px;
        }

        .post-hashtags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .hashtag {
            background: rgba(33, 150, 243, 0.1);
            color: #2196F3;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        /* Form styles */
        .form-group label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        .form-group input[type="text"],
        .form-group textarea {
            font-size: 0.9rem;
        }

        .close-btn {
            padding: 8px;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }

        .close-btn:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 480px) {
            .post-title, .post-description {
                font-size: 0.85rem;
            }
            
            .hashtag {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="header">
            <div class="username"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="nav-actions">
                <a href="edit_profile.php" class="edit-profile-btn">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
            </div>
        </div>
        
        <div class="profile">
            <div class="profile-left">
                <img src="<?php echo $user['profile_picture'] ? 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']) : 'assets/default-avatar.png'; ?>" 
                     alt="Profile picture of <?php echo htmlspecialchars($user['name']); ?>"/>
                <div class="name"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            
            <div class="profile-right">
                <div class="bio-section">
                    <div class="bio-label">Bio</div>
                    <div class="bio-content">
                        <?php echo htmlspecialchars($user['bio']); ?>
                    </div>
                </div>
                
                <?php if (!empty($user['interests'])): ?>
                <div class="interests-section">
                    <div class="interests-label">Interests</div>
                    <div class="interests-content">
                        <?php echo htmlspecialchars($user['interests']); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- New section divider -->
        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="section-title">My Blogs</div>
            <div class="divider-line"></div>
        </div>

        <!-- Blog statistics -->
        <div class="blog-stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo count($blogs); ?></div>
                <div class="stat-label">Posts</div>
            </div>
            <div class="stat-item">
                <?php
                $total_likes = 0;
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM blog_likes WHERE blog_id IN (SELECT id FROM blogs WHERE user_id = ?)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $total_likes = $row['total'];
                }
                ?>
                <div class="stat-number"><?php echo $total_likes; ?></div>
                <div class="stat-label">Total Likes</div>
            </div>
            <div class="stat-item">
                <?php
                $total_comments = 0;
                $stmt = $conn->prepare("SELECT COUNT(*) as total FROM blog_comments WHERE blog_id IN (SELECT id FROM blogs WHERE user_id = ?)");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $total_comments = $row['total'];
                }
                ?>
                <div class="stat-number"><?php echo $total_comments; ?></div>
                <div class="stat-label">Total Comments</div>
            </div>
        </div>

        <div class="blog-container">
            <div class="blog-grid">
                <?php 
                shuffle($blogs);
                foreach ($blogs as $blog): 
                ?>
                <div class="post" data-blog-id="<?php echo htmlspecialchars($blog['id']); ?>">
                    <div class="post-header">
                        <div class="post-user">
                            <img src="<?php echo isset($user['profile_picture']) ? 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']) : 'assets/default-avatar.png'; ?>" 
                                 alt="Profile Picture">
                            <div class="user-info">
                                <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                                <span class="post-date"><?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                            </div>
                        </div>
                        <div class="blog-options">
                            <div class="blog-options-toggle" onclick="toggleBlogOptions(<?php echo htmlspecialchars($blog['id']); ?>)">
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                            <div class="blog-options-menu" id="blogOptions<?php echo htmlspecialchars($blog['id']); ?>">
                                <a href="#" onclick="editBlog(<?php echo htmlspecialchars($blog['id']); ?>); return false;" class="blog-option-item">
                                    <i class="fas fa-edit"></i> Edit Blog
                                </a>
                                <div class="blog-option-divider"></div>
                                <a href="#" onclick="deleteBlog(<?php echo htmlspecialchars($blog['id']); ?>); return false;" 
                                   class="blog-option-item delete-option">
                                    <i class="fas fa-trash-alt"></i> Delete Blog
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="post-content">
                        <?php if ($blog['image_url']): ?>
                            <div class="post-image">
                                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Blog image">
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-details">
                            <div class="post-title">
                                <label>Title:</label>
                                <span><?php echo htmlspecialchars($blog['title']); ?></span>
                            </div>
                            
                            <div class="post-description">
                                <label>Description:</label>
                                <span><?php echo htmlspecialchars($blog['description']); ?></span>
                            </div>
                            
                            <?php if (!empty($blog['hashtags'])): ?>
                                <div class="post-hashtags">
                                    <?php
                                    $hashtags = explode(',', $blog['hashtags']);
                                    foreach ($hashtags as $hashtag) {
                                        echo '<span class="hashtag">' . htmlspecialchars(trim($hashtag)) . '</span>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="post-footer">
                        <div class="comments" onclick="viewComments(<?php echo htmlspecialchars($blog['id']); ?>)">
                            <i class="far fa-comments"></i>
                            <span class="comment-count"><?php echo isset($blog['comment_count']) ? (int)$blog['comment_count'] : 0; ?></span>
                            <span>Comments</span>
                        </div>
                        <div class="likes">
                            <i class="far fa-thumbs-up"></i>
                            <span class="like-count"><?php echo isset($blog['like_count']) ? (int)$blog['like_count'] : 0; ?></span>
                            <span>Likes</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="dropdown-menu">
        <div class="dropdown-toggle" onclick="toggleDropdown()">
            <?php
            // Check if session exists and username is set
            $displayText = 'Menu';
            if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
                $displayText = htmlspecialchars($_SESSION['username']);
            }
            ?>
            <span><?php echo $displayText; ?></span>
            <i class="fas fa-chevron-down"></i>
        </div>
        
        <div class="dropdown-content" id="dropdownContent">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
                <a href="blogs.php" class="dropdown-item">
                    <i class="fas fa-blog"></i>
                    Blogs
                </a>
                <a href="create_blog_page.php" class="dropdown-item">
                    <i class="fas fa-plus-circle"></i>
                    Create Blog
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="dropdown-item">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </a>
                <a href="register.php" class="dropdown-item">
                    <i class="fas fa-user-plus"></i>
                    Register
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add this dialog box HTML after your main content -->
    <div id="commentDialog" class="comment-dialog">
        <div class="comment-dialog-content">
            <div class="comment-header">
                <h3>Comments</h3>
                <button class="close-btn" onclick="closeCommentDialog()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="comments-container" id="commentsContainer">
                <!-- Comments will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Edit Blog Dialog -->
    <div id="editBlogDialog" class="dialog">
        <div class="dialog-content">
            <div class="dialog-header">
                <h3>Edit Blog</h3>
                <button class="close-btn" onclick="closeEditDialog()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editBlogForm" class="edit-blog-form">
                <input type="hidden" id="editBlogId" name="blog_id">
                
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" id="editTitle" name="title" required>
                </div>
                
                <div class="form-group">
                    <label>Description:</label>
                    <textarea id="editDescription" name="description" required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Hashtags:</label>
                    <input type="text" id="editHashtags" name="hashtags" placeholder="e.g., #nature, #photography">
                </div>
                
                <div class="form-group">
                    <label>Change Image:</label>
                    <input type="file" id="editImage" name="image" accept="image/*">
                    <div id="currentImage" class="current-image">
                        <img src="" alt="Current blog image">
                    </div>
                </div>
                
                <div class="dialog-buttons">
                    <button type="submit" class="save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <div id="deleteConfirmDialog" class="dialog">
        <div class="dialog-content">
            <div class="dialog-header">
                <h3>Delete Blog</h3>
                <button class="close-btn" onclick="closeDeleteDialog()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="dialog-body">
                <p>Are you sure you want to permanently delete this blog?</p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
            <div class="dialog-buttons">
                <button class="delete-btn" onclick="confirmDelete()">Delete</button>
                <button class="cancel-btn" onclick="closeDeleteDialog()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
    function toggleDropdown() {
        const dropdownContent = document.getElementById('dropdownContent');
        dropdownContent.classList.toggle('show');

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = event.target.closest('.dropdown-menu');
            if (!isClickInside && dropdownContent.classList.contains('show')) {
                dropdownContent.classList.remove('show');
            }
        });
    }

    // Close dropdown when pressing escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdownContent = document.getElementById('dropdownContent');
            if (dropdownContent.classList.contains('show')) {
                dropdownContent.classList.remove('show');
            }
        }
    });

    async function viewComments(blogId) {
        const dialog = document.getElementById('commentDialog');
        const container = document.getElementById('commentsContainer');
        
        try {
            // Show dialog and loading state
            dialog.style.display = 'flex';
            container.innerHTML = '<div class="loading">Loading comments...</div>';
            
            // Fetch comments
            const response = await fetch(`get_comments.php?blog_id=${blogId}`);
            const data = await response.json();
            
            if (data.success) {
                if (data.comments.length > 0) {
                    container.innerHTML = data.comments.map(comment => `
                        <div class="comment-item">
                            <div class="comment-user">
                                <img src="${comment.user_image || 'assets/default-avatar.png'}" 
                                     alt="User avatar">
                                <span class="comment-user-name">${comment.username}</span>
                                <span class="comment-date">${formatDate(comment.created_at)}</span>
                            </div>
                            <div class="comment-text">${comment.comment}</div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="no-comments">No comments yet</div>';
                }
            } else {
                container.innerHTML = '<div class="error">Failed to load comments</div>';
            }
        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = '<div class="error">Failed to load comments</div>';
        }
    }

    function closeCommentDialog() {
        const dialog = document.getElementById('commentDialog');
        dialog.style.display = 'none';
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Close dialog when clicking outside
    document.getElementById('commentDialog').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommentDialog();
        }
    });

    // Close dialog with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCommentDialog();
        }
    });

    function toggleBlogOptions(blogId) {
        const menu = document.getElementById(`blogOptions${blogId}`);
        const allMenus = document.querySelectorAll('.blog-options-menu');
        
        // Close all other open menus
        allMenus.forEach(m => {
            if (m.id !== `blogOptions${blogId}`) {
                m.classList.remove('show');
            }
        });
        
        menu.classList.toggle('show');
    }

    let blogToDelete = null;

    function deleteBlog(blogId) {
        blogToDelete = blogId;
        document.getElementById('deleteConfirmDialog').style.display = 'flex';
    }

    function closeDeleteDialog() {
        document.getElementById('deleteConfirmDialog').style.display = 'none';
        blogToDelete = null;
    }

    function confirmDelete() {
        if (blogToDelete) {
            fetch('delete_blog.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    blog_id: blogToDelete
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const blogPost = document.querySelector(`.post[data-blog-id="${blogToDelete}"]`);
                    blogPost.style.animation = 'fadeOut 0.3s ease';
                    setTimeout(() => {
                        blogPost.remove();
                        updateBlogCount();
                    }, 300);
                    closeDeleteDialog();
                } else {
                    alert('Failed to delete blog: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the blog');
            });
        }
    }

    // Handle edit form submission
    document.getElementById('editBlogForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const blogId = document.getElementById('editBlogId').value;
        
        fetch('update_blog.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the blog post content in the DOM
                const blogPost = document.querySelector(`.post[data-blog-id="${blogId}"]`);
                if (blogPost) {
                    blogPost.querySelector('.post-title').textContent = formData.get('title');
                    blogPost.querySelector('.post-description').textContent = formData.get('description');
                    
                    // Update image if a new one was uploaded
                    const imageFile = formData.get('image');
                    if (imageFile && imageFile.size > 0 && data.image_url) {
                        blogPost.querySelector('.post-image img').src = data.image_url;
                    }
                    
                    // Show success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'success-message';
                    successMessage.textContent = 'Blog updated successfully!';
                    blogPost.appendChild(successMessage);
                    
                    // Remove success message after 3 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                }
                
                closeEditDialog();
            } else {
                alert('Failed to update blog: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the blog');
        });
    });

    // Close dialogs when clicking outside
    document.querySelectorAll('.dialog').forEach(dialog => {
        dialog.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });

    // Close dialogs with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.dialog').forEach(dialog => {
                dialog.style.display = 'none';
            });
            blogToDelete = null;
        }
    });

    function updateBlogCount() {
        const totalBlogs = document.querySelectorAll('.post').length;
        const blogCountElement = document.querySelector('.total-blogs');
        if (blogCountElement) {
            blogCountElement.textContent = `Total Blogs: ${totalBlogs}`;
        }
    }

    // Add fadeOut animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    `;
    document.head.appendChild(style);

    function editBlog(blogId) {
        const dialog = document.getElementById('editBlogDialog');
        dialog.style.display = 'flex';
        
        // Close the options menu
        document.getElementById(`blogOptions${blogId}`).classList.remove('show');
        
        // Fetch blog details
        fetch(`get_blog_details.php?id=${blogId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editBlogId').value = data.blog.id;
                    document.getElementById('editTitle').value = data.blog.title;
                    document.getElementById('editDescription').value = data.blog.description;
                    document.getElementById('editHashtags').value = data.blog.hashtags || '';
                    
                    const currentImageDiv = document.getElementById('currentImage');
                    if (data.blog.image_url) {
                        currentImageDiv.querySelector('img').src = data.blog.image_url;
                        currentImageDiv.style.display = 'block';
                    } else {
                        currentImageDiv.style.display = 'none';
                    }
                } else {
                    alert('Failed to load blog details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading blog details');
            });
    }
    </script>
</body>
</html>
