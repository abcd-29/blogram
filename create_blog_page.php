<?php
session_start();
include 'includes/db.php';
include 'includes/auth.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Blog - Blogram</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }

        .create-blog-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        textarea {
            height: 200px;
            resize: vertical;
        }

        input[type="text"]:focus,
        textarea:focus {
            border-color: #4a90e2;
            outline: none;
        }

        .upload-container {
            border: 2px dashed #4a90e2;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-container:hover {
            background: #e8f0fe;
            border-color: #357abd;
        }

        .upload-container i {
            font-size: 24px;
            color: #4a90e2;
            margin-bottom: 10px;
        }

        .image-preview-container {
            margin-top: 15px;
            position: relative;
            display: none;
        }

        .image-preview-container img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .remove-image {
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
        }

        .hashtags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .hashtag {
            background: #e8f0fe;
            color: #4a90e2;
            padding: 5px 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .hashtag i {
            cursor: pointer;
            color: #4a90e2;
        }

        .hashtag i:hover {
            color: #dc3545;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .submit-btn, .cancel-btn {
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            flex: 1;
        }

        .submit-btn {
            background: #4a90e2;
            color: white;
            border: none;
        }

        .cancel-btn {
            background: #f5f5f5;
            color: #666;
            border: 1px solid #ddd;
        }

        .submit-btn:hover {
            background: #357abd;
        }

        .cancel-btn:hover {
            background: #eee;
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
            z-index: 1000;
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

        .required::after {
            content: ' *';
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <form class="create-blog-form" id="createBlogForm" enctype="multipart/form-data">
            <h1>Create New Blog</h1>
            
            <div class="form-group">
                <label for="blogTitle" class="required">Title</label>
                <input type="text" id="blogTitle" name="title" required>
            </div>

            <div class="form-group">
                <label for="blogDescription" class="required">Description</label>
                <textarea id="blogDescription" name="description" required></textarea>
            </div>

            <div class="form-group">
                <label for="blogImage" class="required">Blog Image</label>
                <div class="upload-container" onclick="document.getElementById('blogImage').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click to upload image</p>
                </div>
                <input type="file" id="blogImage" name="image" accept="image/*" required hidden>
                <div class="image-preview-container" id="imagePreview"></div>
            </div>

            <div class="form-group">
                <label for="hashtagInput">Hashtags (optional)</label>
                <input type="text" id="hashtagInput" placeholder="Type hashtag and press Enter">
                <div class="hashtags-container" id="hashtagsContainer"></div>
            </div>

            <div class="button-group">
                <button type="button" class="cancel-btn" onclick="window.location.href='blogs.php'">Cancel</button>
                <button type="submit" class="submit-btn">Post Blog</button>
            </div>
        </form>
    </div>

    <script>
        // Hashtags handling
        const hashtagInput = document.getElementById('hashtagInput');
        const hashtagsContainer = document.getElementById('hashtagsContainer');
        let hashtags = [];

        hashtagInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const hashtag = this.value.trim().replace(/[^a-zA-Z0-9]/g, '');
                if (hashtag && !hashtags.includes(hashtag)) {
                    addHashtag(hashtag);
                    this.value = '';
                }
            }
        });

        function addHashtag(hashtag) {
            hashtags.push(hashtag);
            updateHashtags();
        }

        function removeHashtag(hashtag) {
            hashtags = hashtags.filter(h => h !== hashtag);
            updateHashtags();
        }

        function updateHashtags() {
            hashtagsContainer.innerHTML = hashtags.map(hashtag => `
                <span class="hashtag">
                    #${hashtag}
                    <i class="fas fa-times" onclick="removeHashtag('${hashtag}')"></i>
                </span>
            `).join('');
        }

        // Image preview handling
        document.getElementById('blogImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                const preview = document.getElementById('imagePreview');
                
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" onclick="removeImage()">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        });

        function removeImage() {
            document.getElementById('blogImage').value = '';
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            preview.style.display = 'none';
        }

        // Form submission
        document.getElementById('createBlogForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const title = document.getElementById('blogTitle').value.trim();
            const description = document.getElementById('blogDescription').value.trim();
            const image = document.getElementById('blogImage').files[0];
            
            if (!title || !description || !image) {
                showNotification('Please fill in all required fields', 'error');
                return;
            }
            
            const submitBtn = this.querySelector('.submit-btn');
            submitBtn.disabled = true;
            
            const formData = new FormData();
            formData.append('title', title);
            formData.append('description', description);
            formData.append('image', image);
            
            if (hashtags.length > 0) {
                formData.append('hashtags', JSON.stringify(hashtags));
            }
            
            try {
                const response = await fetch('create_blog.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showNotification('Blog posted successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = 'blogs.php';
                    }, 1000);
                } else {
                    showNotification(data.message || 'Error creating blog post', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error creating blog post', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        });

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('show');
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }, 100);
        }
    </script>
</body>
</html> 