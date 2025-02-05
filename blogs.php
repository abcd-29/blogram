<?php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

// Fetch blogs and join with users to get the username and profile picture
$stmt = $conn->prepare("
    SELECT b.id, b.title, b.description, b.created_at, b.image_url, b.hashtags, 
           u.username, u.name, u.profile_picture, u.user_id 
    FROM blogs b 
    JOIN users u ON b.user_id = u.user_id 
    ORDER BY b.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

// Add this function at the top of the file
function getUserProfilePicture($conn, $userId) {
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['profile_picture']) {
        return 'uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']);
    }
    return 'assets/default-avatar.png';
}

// Get user's profile picture only if logged in
$profilePicture = isLoggedIn() ? getUserProfilePicture($conn, $_SESSION['user_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogram - Blogs</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            /* Modern Color Palette */
            --primary-dark: #1E1E2E;      /* Dark background */
            --primary-light: #2A2A3C;     /* Lighter background for nav */
            --accent: #7C3AED;            /* Purple accent for buttons */
            --accent-hover: #6D28D9;      /* Darker purple for hover */
            --text-primary: #F8FAFC;      /* Bright text */
            --text-secondary: #CBD5E1;    /* Muted text */
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #252538 100%);
            color: var(--text-primary);
        }

        .navbar {
            background: rgba(42, 42, 60, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Navigation Buttons */
        .create-blog-btn {
            background: var(--accent);
            color: var(--text-primary);
            padding: 10px 24px;
            border-radius: 25px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(124, 58, 237, 0.2);
        }

        .create-blog-btn:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }

        .nav-button {
            padding: 10px 24px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav-button:not(.signup) {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-button.signup {
            background: var(--accent);
            color: var(--text-primary);
            border: none;
            box-shadow: 0 2px 10px rgba(124, 58, 237, 0.2);
        }

        .nav-button:not(.signup):hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .nav-button.signup:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }

        /* Profile Dropdown */
        .profile-dropdown .profile-img {
            border: 2px solid rgba(124, 58, 237, 0.5);
            transition: all 0.3s ease;
        }

        .profile-dropdown .profile-img:hover {
            border-color: var(--accent);
            transform: scale(1.05);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--primary-dark);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--accent);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-hover);
        }

        /* Page Title */
        h1 {
            color: var(--text-primary);
            text-align: center;
            margin: 2rem 0;
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Container Spacing */
        .blogs-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .create-blog-btn span {
                display: none;
            }
            
            .create-blog-btn {
                padding: 10px;
                border-radius: 50%;
                aspect-ratio: 1;
            }
            
            .nav-button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }

        .blogs-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            padding: 20px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .blog-post {
            background: #ffffff;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .blog-post:hover {
            transform: translateY(-5px);
        }

        .blog-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .blog-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .blog-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .author-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .author-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .blog-date {
            color: #666;
            font-size: 12px;
        }

        .blog-image {
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .blog-post:hover .blog-image img {
            transform: scale(1.05);
        }

        .blog-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin: 15px 0;
            padding: 0 15px;
        }

        .blog-content {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            padding: 0 15px;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-actions {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 20px;
        }

        .action-button {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
            cursor: pointer;
            transition: all 0.2s ease;
            padding: 6px 12px;
            border-radius: 20px;
        }

        .action-button:hover {
            background: #f5f5f5;
            color: #333;
        }

        .like-active {
            color: #ff4d4d;
        }

        .like-active i {
            animation: likeEffect 0.4s ease;
        }

        @keyframes likeEffect {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .blogs-container {
                grid-template-columns: repeat(2, 1fr);
                max-width: 800px;
            }
        }

        @media (max-width: 768px) {
            .blogs-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .blog-image {
                height: 160px;
            }

            .blog-title {
                font-size: 16px;
            }

            .blog-content {
                font-size: 13px;
            }
        }

        .comment-modal {
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

        .comment-dialog {
            background: white;
            width: 90%;
            max-width: 500px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .comment-header h3 {
            margin: 0;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .comments-container {
            max-height: 300px;
            overflow-y: auto;
            padding: 20px;
        }

        .comment-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .comment-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .comment-user img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-user .name {
            font-weight: bold;
            color: #333;
        }

        .comment-text {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        .comment-date {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        .comment-form {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .comment-form textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            margin-bottom: 10px;
            font-family: inherit;
        }

        .comment-form button {
            background: #4a90e2;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .comment-form button:hover {
            background: #357abd;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            gap: 10px;
        }

        .logo-img {
            height: 40px;
            width: auto;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            letter-spacing: -0.5px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .auth-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-button {
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .nav-button:not(.signup) {
            color: #333;
            background: transparent;
            border: 1px solid #ddd;
        }

        .nav-button:not(.signup):hover {
            background: #f5f5f5;
            border-color: #999;
        }

        .nav-button.signup {
            background: #4a90e2;
            color: white;
            border: none;
        }

        .nav-button.signup:hover {
            background: #357abd;
            transform: translateY(-1px);
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        .profile-img:hover {
            border-color: #4a90e2;
            transform: scale(1.05);
        }

        /* Add error handling for broken images */
        .profile-img:error {
            content: url('assets/default-avatar.png');
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 120%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            padding: 10px 0;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .dropdown-menu a:hover {
            background: #f5f5f5;
        }

        .dropdown-divider {
            height: 1px;
            background: #eee;
            margin: 8px 0;
        }

        .dropdown-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
            }

            .logo-text {
                display: none;
            }

            .auth-buttons {
                gap: 10px;
            }

            .nav-button {
                padding: 6px 15px;
                font-size: 13px;
            }
        }

        .create-blog-btn {
            padding: 8px 20px;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .create-blog-btn:hover {
            background: #357abd;
            transform: translateY(-1px);
        }

        .create-blog-btn i {
            font-size: 14px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            position: relative;
            background: white;
            width: 90%;
            max-width: 600px;
            margin: 50px auto;
            border-radius: 15px;
            padding: 20px;
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close-modal {
            font-size: 28px;
            color: #666;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-modal:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        #imagePreview {
            margin-top: 10px;
            max-width: 100%;
            height: 200px;
            border-radius: 8px;
            overflow: hidden;
            display: none;
        }

        #imagePreview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .cancel-btn,
        .submit-btn {
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .cancel-btn {
            background: #f5f5f5;
            border: 1px solid #ddd;
            color: #666;
        }

        .submit-btn {
            background: #4a90e2;
            border: none;
            color: white;
        }

        .cancel-btn:hover {
            background: #eee;
        }

        .submit-btn:hover {
            background: #357abd;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .create-blog-btn span {
                display: none;
            }
            
            .create-blog-btn {
                padding: 8px;
            }
        }

        .upload-label {
            cursor: pointer;
            display: block;
        }

        .upload-button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border: 2px dashed #4a90e2;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .upload-button:hover {
            background: #e8f0fe;
            border-color: #357abd;
        }

        .upload-button i {
            font-size: 24px;
            color: #4a90e2;
        }

        #imagePreview {
            margin-top: 15px;
            position: relative;
        }

        #imagePreview .remove-image {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        #imagePreview .remove-image:hover {
            background: rgba(0, 0, 0, 0.7);
        }

        .loading-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .submit-btn.loading .btn-text {
            display: none;
        }

        .submit-btn.loading .loading-spinner {
            display: inline-block;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 2000;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: #4CAF50;
        }

        .notification.error {
            background: #f44336;
        }

        .blog-hashtags {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .hashtag {
            background: #e8f0fe;
            color: #4a90e2;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 14px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let currentBlogId = null;

    async function handleComment(element, blogId) {
        // Check if user is logged in
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        
        if (!isLoggedIn) {
            window.location.href = 'login.php';
            return;
        }
        
        currentBlogId = blogId;
        const modal = document.getElementById('commentModal');
        const commentsContainer = modal.querySelector('.comments-container');
        
        // Show modal
        modal.style.display = 'flex';
        
        // Load comments
        try {
            const response = await fetch(`get_comments.php?blog_id=${blogId}`);
            const comments = await response.json();
            
            commentsContainer.innerHTML = comments.map(comment => `
                <div class="comment-item">
                    <div class="comment-user">
                        <img src="${comment.profile_picture || 'assets/default-avatar.png'}" alt="User avatar">
                        <span class="name">${comment.name}</span>
                    </div>
                    <div class="comment-text">${comment.comment}</div>
                    <div class="comment-date">${formatDate(comment.created_at)}</div>
                </div>
            `).join('') || '<p>No comments yet. Be the first to comment!</p>';
        } catch (error) {
            console.error('Error loading comments:', error);
        }
    }

    async function handleLike(element, blogId) {
        // Check if user is logged in
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        
        if (!isLoggedIn) {
            window.location.href = 'login.php';
            return;
        }
        
        if (!element) return;
        
        const likeIcon = element.querySelector('i');
        const likeCount = element.querySelector('.like-count');
        const isLiked = likeIcon.classList.contains('fas');
        
        try {
            const response = await fetch('handle_like.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    blog_id: blogId,
                    action: isLiked ? 'unlike' : 'like'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Update like status and count without page refresh
                if (isLiked) {
                    likeIcon.classList.remove('fas', 'like-active');
                    likeIcon.classList.add('far');
                    likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    // Store unlike status in localStorage
                    localStorage.removeItem(`blog_${blogId}_liked`);
                } else {
                    likeIcon.classList.remove('far');
                    likeIcon.classList.add('fas', 'like-active');
                    likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    // Store like status in localStorage
                    localStorage.setItem(`blog_${blogId}_liked`, 'true');
                }
                
                // Store the current like count
                localStorage.setItem(`blog_${blogId}_count`, likeCount.textContent);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    // Close modal when clicking outside
    document.querySelector('.comment-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCommentModal();
        }
    });

    // Close button functionality
    document.querySelector('.close-btn').addEventListener('click', function() {
        closeCommentModal();
    });

    // Function to close comment modal
    function closeCommentModal() {
        const modal = document.getElementById('commentModal');
        const commentText = document.getElementById('commentText');
        
        // Clear any entered text
        commentText.value = '';
        
        // Hide modal
        modal.style.display = 'none';
        
        // Reset currentBlogId
        currentBlogId = null;
    }

    // Submit comment
    document.getElementById('submitComment').addEventListener('click', async function() {
        const commentText = document.getElementById('commentText').value.trim();
        if (!commentText) return;

        try {
            const response = await fetch('add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    blog_id: currentBlogId,
                    comment: commentText
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Reload comments
                handleComment(null, currentBlogId);
                
                // Update comment count
                const blogPost = document.querySelector(`[data-blog-id="${currentBlogId}"]`);
                const commentCount = blogPost.querySelector('.comment-count');
                commentCount.textContent = parseInt(commentCount.textContent) + 1;
                
                // Clear textarea
                document.getElementById('commentText').value = '';
            }
        } catch (error) {
            console.error('Error posting comment:', error);
        }
    });

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

    // Initialize like counts and states on page load
    document.addEventListener('DOMContentLoaded', async function() {
        const posts = document.querySelectorAll('.blog-post');
        
        for (const post of posts) {
            const blogId = post.dataset.blogId;
            const likeIcon = post.querySelector('.likes i');
            const likeCount = post.querySelector('.like-count');
            
            try {
                // First check localStorage for like status and count
                const isLiked = localStorage.getItem(`blog_${blogId}_liked`) === 'true';
                const storedCount = localStorage.getItem(`blog_${blogId}_count`);
                
                if (isLiked) {
                    likeIcon.classList.remove('far');
                    likeIcon.classList.add('fas', 'like-active');
                }
                
                if (storedCount) {
                    likeCount.textContent = storedCount;
                } else {
                    // If no stored count, fetch from server
                    const response = await fetch(`get_interaction_counts.php?blog_id=${blogId}`);
                    const data = await response.json();
                    
                    likeCount.textContent = data.likes;
                    localStorage.setItem(`blog_${blogId}_count`, data.likes);
                    
                    if (data.user_liked) {
                        likeIcon.classList.remove('far');
                        likeIcon.classList.add('fas', 'like-active');
                        localStorage.setItem(`blog_${blogId}_liked`, 'true');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    });

    // Clear like data on logout
    function handleLogout() {
        // Clear all blog-related data from localStorage
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (key.startsWith('blog_')) {
                localStorage.removeItem(key);
            }
        }
        window.location.href = 'logout.php';
    }

    // Add session check before any authenticated actions
    function checkAuth(action) {
        const isLoggedIn = <?php echo isLoggedIn() ? 'true' : 'false'; ?>;
        if (!isLoggedIn) {
            window.location.href = 'login.php';
            return false;
        }
        return true;
    }

    // Update dropdown toggle to check auth
    function toggleDropdown() {
        if (!checkAuth('dropdown')) return;
        
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('active');

        // Close dropdown when clicking outside
        document.addEventListener('click', function closeDropdown(e) {
            const profile = document.querySelector('.profile-dropdown');
            if (!profile.contains(e.target)) {
                dropdown.classList.remove('active');
                document.removeEventListener('click', closeDropdown);
            }
        });
    }

    // Close dropdown when pressing escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.remove('active');
        }
    });

    // Add error handling for broken images
    document.addEventListener('DOMContentLoaded', function() {
        const profileImages = document.querySelectorAll('.profile-img, .dropdown-profile-img');
        profileImages.forEach(img => {
            img.onerror = function() {
                this.src = 'assets/default-avatar.png';
            };
        });
    });

    function openCreateBlogModal() {
        if (!checkAuth('create_blog')) return;
        document.getElementById('createBlogModal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function closeCreateBlogModal() {
        document.getElementById('createBlogModal').style.display = 'none';
        document.body.style.overflow = 'auto';
        document.getElementById('createBlogForm').reset();
        document.getElementById('imagePreview').style.display = 'none';
    }

    // Update the image preview functionality
    document.getElementById('blogImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            const preview = document.getElementById('imagePreview');
            
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="preview-container">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });

    function removeImage() {
        document.getElementById('blogImage').value = '';
        document.getElementById('imagePreview').innerHTML = '';
        document.getElementById('imagePreview').style.display = 'none';
    }

    // Update form submission with page refresh
    document.getElementById('createBlogForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('.submit-btn');
        submitBtn.classList.add('loading');
        
        const formData = new FormData(this);
        
        try {
            console.log('Submitting form...'); // Debug log
            
            const response = await fetch('create_blog.php', {
                method: 'POST',
                body: formData
            });
            
            console.log('Response received'); // Debug log
            
            const data = await response.json();
            console.log('Response data:', data); // Debug log
            
            if (data.success) {
                showNotification('Blog posted successfully!', 'success');
                
                // Close modal and refresh page
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                console.error('Error data:', data); // Debug log
                showNotification(data.message || 'Error creating blog post', 'error');
            }
        } catch (error) {
            console.error('Submission error:', error);
            showNotification('Error creating blog post', 'error');
        } finally {
            submitBtn.classList.remove('loading');
        }
    });

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
    }

    // Make sure the blogs container exists in your HTML
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.querySelector('.blogs-container')) {
            const mainContent = document.querySelector('main') || document.body;
            const blogsContainer = document.createElement('div');
            blogsContainer.className = 'blogs-container';
            mainContent.appendChild(blogsContainer);
        }
    });
    </script>
</head>
<body>
    <nav class="navbar">
        <div class="nav-logo">
            <a href="index.php">
                <img src="images/Blogram-1-2-2025.png" alt="Blogram Logo" class="logo-img">
                <span class="logo-text"></span>
            </a>
        </div>
        
        <div class="nav-right">
            <?php if (isLoggedIn()): ?>
                <a href="create_blog_page.php" class="create-blog-btn">
                    <i class="fas fa-plus"></i>
                    <span>Create Blog</span>
                </a>


                <div class="profile-dropdown">
                    <img src="<?php echo $profilePicture; ?>" 
                        alt="Profile" 
                        class="profile-img"
                        onclick="toggleDropdown()">
                    <div class="dropdown-menu" id="profileDropdown">
                        <div class="dropdown-header">
                            <img src="<?php echo $profilePicture; ?>" 
                                alt="Profile" 
                                class="dropdown-profile-img">
                        </div>
                        <a href="view_profile.php?user_id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" onclick="handleLogout()">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="login.php" class="nav-button">Login</a>
                    <a href="register.php" class="nav-button signup">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

    <h1>Blogs</h1>
    <div class="blogs-container">
        <?php while ($blog = $result->fetch_assoc()): ?>
        <article class="blog-post" data-blog-id="<?php echo htmlspecialchars($blog['id']); ?>">
            <header class="blog-header">
                <div class="blog-meta">
                    <div class="blog-author">
                        <img class="author-avatar" 
                             src="<?php echo $blog['profile_picture'] ? 'uploads/profile_pictures/' . htmlspecialchars($blog['profile_picture']) : 'assets/default-avatar.png'; ?>" 
                             alt="Author avatar"/>
                        <div class="author-info">
                            <span class="author-name"><?php echo htmlspecialchars($blog['name']); ?></span>
                            <span class="blog-date"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <?php if (!empty($blog['image_url'])): ?>
            <div class="blog-image">
                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>"
                     alt="<?php echo htmlspecialchars($blog['title']); ?>"/>
            </div>
            <?php endif; ?>

            <div class="blog-title">
                <?php echo htmlspecialchars($blog['title']); ?>
            </div>

            <div class="blog-content">
                <?php echo htmlspecialchars($blog['description']); ?>
            </div>

            <?php if (!empty($blog['hashtags'])): ?>
                <div class="blog-hashtags">
                    <?php 
                    $hashtags = json_decode($blog['hashtags']);
                    if ($hashtags && is_array($hashtags)):
                        foreach ($hashtags as $hashtag): 
                    ?>
                        <span class="hashtag">#<?php echo htmlspecialchars($hashtag); ?></span>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            <?php endif; ?>

            <div class="blog-actions">
                <div class="action-button comments" onclick="handleComment(this, <?php echo htmlspecialchars($blog['id']); ?>)">
                    <i class="far fa-comments"></i>
                    <span class="comment-count">0</span>
                </div>
                <div class="action-button likes" onclick="handleLike(this, <?php echo htmlspecialchars($blog['id']); ?>)">
                    <i class="far fa-thumbs-up"></i>
                    <span class="like-count">0</span>
                </div>
            </div>
        </article>
        <?php endwhile; ?>
    </div>

    <div id="commentModal" class="comment-modal">
        <div class="comment-dialog">
            <div class="comment-header">
                <h3>Comments</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="comments-container">
                <!-- Comments will be loaded here -->
            </div>
            <div class="comment-form">
                <textarea id="commentText" placeholder="Write a comment..."></textarea>
                <button id="submitComment">Post</button>
            </div>
        </div>
    </div>
</body>
</html>