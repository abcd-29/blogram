<?php
session_start();
include 'includes/db.php'; // Include database connection

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $name);
$stmt->fetch();
$stmt->close();

// Determine the display name
$display_name = !empty($name) ? $name : $username;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Profile Picture - Blogram</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden; /* Lock scroll bar */
        }
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1; /* Ensure the video is behind other content */
        }
        .upload-container {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 500px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: 'Roboto', sans-serif; /* Changed font style */
        }
        .upload-container h2 {
            color: #fff; /* Changed color to white */
            font-family: 'Pacifico', cursive; /* Changed font style */
        }
        .user-icon {
            width: 200px; /* Increased size */
            height: 200px; /* Increased size */
            border-radius: 50%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
            overflow: hidden;
            position: relative;
            border: 2px solid #fff;
        }
        
        .user-icon i {
            font-size: 120px; /* Increased icon size */
            color: #333;
        }
        
        .user-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        
        .image-controls {
            margin-top: 10px;
            display: none; /* Initially hidden */
        }
        
        .image-controls button {
            margin: 0 5px;
            padding: 5px 10px;
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid #fff;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .image-controls button:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .upload-container .button-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .upload-container .custom-file-upload {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-family: Arial, sans-serif;
            font-size: 16px;
            transition: background-color 0.3s ease;
            background-color: #fff;
            color: #333;
        }
        .upload-container .custom-file-upload:hover {
            background-color: #f0f0f0;
        }
        .upload-container .custom-file-upload i {
            margin-right: 10px;
        }
        .upload-container #cropButton {
            display: none;
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            color: #fff;
            padding: 10px 0;
            font-size: 12px;
        }
        /* Cropper styling */
        .cropper-view-box,
        .cropper-face {
            border-radius: 50%;
        }
        
        .cropper-container {
            border-radius: 50%;
            overflow: hidden;
        }
        
        .cropper-view-box {
            box-shadow: 0 0 0 1px #39f;
            outline: 0;
        }
        
        .cropper-face {
            background-color: transparent;
        }
    </style>
</head>
<body>
    <video class="video-background" autoplay muted loop>
        <source src="images/8733062-uhd_3840_2160_30fps.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="upload-container">
        <h2>Add Profile Picture</h2>
        <div class="user-icon">
            <i class="fas fa-user" id="userIcon"></i>
            <img id="imagePreview">
        </div>
        <div class="image-controls" id="imageControls">
            <button type="button" id="zoomIn"><i class="fas fa-search-plus"></i></button>
            <button type="button" id="zoomOut"><i class="fas fa-search-minus"></i></button>
            <button type="button" id="rotateLeft"><i class="fas fa-undo"></i></button>
            <button type="button" id="rotateRight"><i class="fas fa-redo"></i></button>
        </div>
        <form id="uploadForm" action="update_profile_picture.php" method="post" enctype="multipart/form-data">
            <div class="button-group">
                <label class="custom-file-upload">
                    <i class="fas fa-upload"></i>
                    <span>Choose File</span>
                    <input type="file" name="profile_picture" accept="image/*" style="display: none;" id="fileInput">
                </label>
                <button type="submit" class="custom-file-upload">
                    <i class="fas fa-arrow-right"></i>
                    <span>Upload</span>
                </button>
            </div>
        </form>
    </div>

    <footer>
        <p>&copy; 2023 Blogram. All rights reserved.</p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        const fileInput = document.getElementById('fileInput');
        const imagePreview = document.getElementById('imagePreview');
        const userIcon = document.getElementById('userIcon');
        const uploadForm = document.getElementById('uploadForm');
        const imageControls = document.getElementById('imageControls');
        
        let scale = 1;
        let rotation = 0;
        
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    userIcon.style.display = 'none';
                    imageControls.style.display = 'block';
                    
                    // Reset transformations
                    scale = 1;
                    rotation = 0;
                    updateImageTransform();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Zoom and rotation controls
        document.getElementById('zoomIn').addEventListener('click', () => {
            scale += 0.1;
            updateImageTransform();
        });
        
        document.getElementById('zoomOut').addEventListener('click', () => {
            if (scale > 0.5) scale -= 0.1;
            updateImageTransform();
        });
        
        document.getElementById('rotateLeft').addEventListener('click', () => {
            rotation -= 90;
            updateImageTransform();
        });
        
        document.getElementById('rotateRight').addEventListener('click', () => {
            rotation += 90;
            updateImageTransform();
        });
        
        function updateImageTransform() {
            imagePreview.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
        }

        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Create a canvas to apply the transformations
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const img = new Image();
            
            img.onload = function() {
                // Set canvas size
                canvas.width = 200;
                canvas.height = 200;
                
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Save context state
                ctx.save();
                
                // Move to center of canvas
                ctx.translate(canvas.width/2, canvas.height/2);
                
                // Apply transformations
                ctx.rotate(rotation * Math.PI / 180);
                ctx.scale(scale, scale);
                
                // Draw image centered
                ctx.drawImage(img, 
                    -canvas.width/2, 
                    -canvas.height/2, 
                    canvas.width, 
                    canvas.height
                );
                
                // Restore context state
                ctx.restore();
                
                // Convert canvas to blob
                canvas.toBlob(function(blob) {
                    const formData = new FormData();
                    formData.append('profile_picture', blob, 'profile_picture.png');
                    
                    // Show loading state
                    const submitButton = uploadForm.querySelector('button[type="submit"]');
                    const originalText = submitButton.innerHTML;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                    submitButton.disabled = true;
                    
                    fetch('update_profile_picture.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Redirect on success
                            window.location.href = 'complete_profile.php';
                        } else {
                            // Show error and reset button
                            alert('Error uploading profile picture: ' + data.message);
                            submitButton.innerHTML = originalText;
                            submitButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while uploading the profile picture');
                        submitButton.innerHTML = originalText;
                        submitButton.disabled = false;
                    });
                }, 'image/png', 0.9);
            };
            
            img.src = imagePreview.src;
        });
    </script>
</body>
</html>