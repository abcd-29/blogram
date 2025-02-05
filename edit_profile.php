<?php
session_start();
include 'includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch existing user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, bio, interests, name, profile_picture FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);
    $interests = trim($_POST['interests']);
    $update_successful = false;
    $error_message = '';
    
    try {
        // Start transaction
        $conn->begin_transaction();

        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_picture']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (!in_array(strtolower($filetype), $allowed)) {
                throw new Exception("Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.");
            }

            $new_filename = uniqid() . '.' . $filetype;
            $upload_path = 'uploads/profile_pictures/' . $new_filename;
            
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
                throw new Exception("Failed to upload image.");
            }

            // Delete old profile picture if exists
            if ($user_data['profile_picture']) {
                $old_file = 'uploads/profile_pictures/' . $user_data['profile_picture'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }

            // Update with new profile picture
            $stmt = $conn->prepare("UPDATE users SET name = ?, bio = ?, interests = ?, profile_picture = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $name, $bio, $interests, $new_filename, $user_id);
        } else {
            // Update without changing profile picture
            $stmt = $conn->prepare("UPDATE users SET name = ?, bio = ?, interests = ? WHERE user_id = ?");
            $stmt->bind_param("sssi", $name, $bio, $interests, $user_id);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update profile.");
        }

        $stmt->close();
        
        // Commit transaction
        $conn->commit();
        $update_successful = true;

        // Redirect on success
        header('Location: view_profile.php');
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .profile-container {
            max-width: 500px;
            width: 85%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .profile-preview {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            margin-bottom: 15px;
        }

        .picture-upload {
            position: relative;
            display: inline-block;
        }

        .upload-button {
            position: absolute;
            bottom: 20px;
            right: 0;
            background: #2196F3;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .upload-button i {
            color: white;
            font-size: 16px;
        }

        h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }

        .username {
            color: white;
            font-size: 18px;
            margin-top: 10px;
            opacity: 0.9;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .submit-btn {
            background: linear-gradient(45deg, #2196F3 0%, #1976D2 50%, #1565C0 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background: linear-gradient(45deg, #1E88E5 0%, #1565C0 50%, #0D47A1 100%);
            transform: translateY(-2px);
        }

        .error {
            color: #ff4444;
            font-size: 14px;
            margin-top: 5px;
        }

        .word-count {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            text-align: right;
            margin-top: 5px;
        }

        .error-message {
            background: rgba(255, 0, 0, 0.1);
            color: #ff4444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .loader {
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .submit-btn.loading .btn-text {
            display: none;
        }

        .submit-btn.loading .loader {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="header-container">
            <h1>Edit Profile</h1>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" id="profileForm">
            <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>

            <div class="profile-preview">
                <div class="picture-upload">
                    <img src="<?php echo $user_data['profile_picture'] ? 'uploads/profile_pictures/' . htmlspecialchars($user_data['profile_picture']) : 'assets/default-avatar.png'; ?>" 
                         alt="Profile Picture" 
                         class="profile-picture" 
                         id="profilePicture">
                    <label for="profile_picture" class="upload-button">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" 
                           id="profile_picture" 
                           name="profile_picture" 
                           accept="image/*" 
                           style="display: none;"
                           onchange="previewImage(this)">
                </div>
                <div class="username"><?php echo htmlspecialchars($user_data['username']); ?></div>
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?php echo htmlspecialchars($user_data['name']); ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" 
                         name="bio" 
                         required><?php echo htmlspecialchars($user_data['bio']); ?></textarea>
                <div class="word-count" id="bioWordCount">0/100 words</div>
            </div>

            <div class="form-group">
                <label for="interests">Interests</label>
                <textarea id="interests" 
                         name="interests"><?php echo htmlspecialchars($user_data['interests']); ?></textarea>
                <div class="word-count" id="interestsWordCount">0/100 words</div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span class="btn-text">Save Changes</span>
                <span class="loader" id="submitLoader" style="display: none;"></span>
            </button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePicture').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function countWords(str) {
            return str.trim().split(/\s+/).filter(word => word.length > 0).length;
        }

        function updateWordCount(field, countElement) {
            const words = countWords(field.value);
            countElement.textContent = `${words}/100 words`;
        }

        // Add event listeners for word count
        document.getElementById('bio').addEventListener('input', function() {
            updateWordCount(this, document.getElementById('bioWordCount'));
        });

        document.getElementById('interests').addEventListener('input', function() {
            updateWordCount(this, document.getElementById('interestsWordCount'));
        });

        // Initial word count update
        updateWordCount(document.getElementById('bio'), document.getElementById('bioWordCount'));
        updateWordCount(document.getElementById('interests'), document.getElementById('interestsWordCount'));

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('submitLoader');
            
            // Show loader
            submitBtn.classList.add('loading');
            loader.style.display = 'block';
        });
    </script>
</body>
</html> 