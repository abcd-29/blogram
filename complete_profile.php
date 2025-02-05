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
$stmt = $conn->prepare("SELECT username, bio, interests FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #00416A 0%, #E4E5E6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* Lock scroll */
        }

        .profile-container {
            max-width: 500px;
            width: 85%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            display: flex;
            flex-direction: column;
            gap: 15px;
            max-height: 90vh;
            animation: fadeIn 0.8s ease-out;
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .profile-preview {
            margin: 0;
        }

        .profile-preview img {
            width: 120px; /* Increased size */
            height: 120px; /* Increased size */
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-preview img:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        h1 {
            font-size: 2.2em;
            font-weight: 700;
            margin: 0;
            text-align: right; /* Right aligned */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-family: 'Poppins', sans-serif;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #ffffff 0%, #e6e6e6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .name-container {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .name-container .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(to right, #e3e8ff 0%, #f0f2ff 100%);
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        input[type="text"]:hover:not(.readonly-field),
        textarea:hover {
            background: linear-gradient(to right, #d1d8ff 0%, #e3e8ff 100%);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        input[type="text"]:focus:not(.readonly-field),
        textarea:focus {
            background: linear-gradient(to right, #c4cdff 0%, #d1d8ff 100%);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.2);
            outline: none;
            transform: translateY(-2px);
        }

        textarea {
            height: 70px; /* Reduced height */
            resize: none;
        }

        .readonly-field {
            background: linear-gradient(to right, #f0f0f0, #e6e6e6) !important;
            color: #666 !important;
            cursor: not-allowed;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.2);
            opacity: 0.8;
        }

        .submit-btn {
            background: linear-gradient(45deg, #2196F3 0%, #1976D2 50%, #1565C0 100%);
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            background: linear-gradient(45deg, #1E88E5 0%, #1565C0 50%, #0D47A1 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 150, 243, 0.4);
        }

        .submit-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(33, 150, 243, 0.3);
            background: linear-gradient(45deg, #1976D2 0%, #1565C0 50%, #0D47A1 100%);
        }

        .submit-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(45deg);
            transition: all 0.5s ease;
            opacity: 0;
        }

        .submit-btn:hover::after {
            opacity: 1;
            transform: rotate(45deg) translate(50%, -50%);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
            }
            50% {
                box-shadow: 0 4px 25px rgba(33, 150, 243, 0.5);
            }
            100% {
                box-shadow: 0 4px 15px rgba(33, 150, 243, 0.3);
            }
        }

        .submit-btn:hover {
            animation: pulse 2s infinite;
        }

        .username-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .error-message {
            color: #ff4444;
            font-size: 14px;
            margin-top: 5px;
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 6px;
            display: none;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.9);
            color: white;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-weight: 500;
        }

        /* Required field indicator */
        .required-field::after {
            content: '*';
            color: #ff4444;
            margin-left: 4px;
        }

        /* Custom scrollbar for textareas */
        textarea::-webkit-scrollbar {
            width: 8px;
        }

        textarea::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        textarea::-webkit-scrollbar-thumb {
            background: rgba(26, 115, 232, 0.5);
            border-radius: 4px;
        }

        /* Video Background */
        .video-background {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -1;
            object-fit: cover;
            transform: scale(1.1); /* Slight scale to prevent white edges */
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Adjust form spacing */
        .form-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Enhanced Responsive Design */
        @media screen and (max-width: 1200px) {
            .profile-container {
                width: 70%;
            }
        }

        @media screen and (max-width: 992px) {
            .profile-container {
                width: 80%;
            }

            h1 {
                font-size: 2.2em;
            }
        }

        @media screen and (max-width: 768px) {
            .profile-container {
                width: 90%;
                padding: 20px;
            }

            .header-container {
                margin-bottom: 15px;
            }

            h1 {
                font-size: 1.8em;
            }

            .profile-preview img {
                width: 100px;
                height: 100px;
            }

            .name-container {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media screen and (max-width: 480px) {
            .profile-container {
                width: 92%;
                padding: 15px;
            }

            h1 {
                font-size: 1.6em;
            }

            .profile-preview img {
                width: 90px;
                height: 90px;
            }

            .header-container {
                margin-bottom: 12px;
            }
        }

        /* Portrait phones */
        @media screen and (max-width: 360px) {
            .profile-container {
                padding: 15px;
                gap: 12px;
            }

            h1 {
                font-size: 1.6em;
            }

            .profile-preview img {
                width: 80px;
                height: 80px;
            }
        }

        /* Landscape mode */
        @media screen and (max-height: 600px) and (orientation: landscape) {
            body {
                padding: 10px;
            }

            .profile-container {
                gap: 12px;
            }

            .profile-preview img {
                width: 80px;
                height: 80px;
                margin: 10px 0;
            }

            h1 {
                margin-bottom: 10px;
            }

            .form-group {
                margin-bottom: 10px;
            }

            textarea {
                height: 60px;
            }
        }

        /* Ensure video covers screen on all devices */
        @supports (-webkit-touch-callout: none) {
            .video-background {
                position: fixed;
                top: 50%;
                left: 50%;
                min-width: 100%;
                min-height: 100%;
                width: auto;
                height: auto;
                transform: translateX(-50%) translateY(-50%) scale(1.1);
                z-index: -1;
            }
        }

        /* Input validation styles */
        input[type="text"].valid,
        textarea.valid {
            border: 2px solid #4CAF50;
            background: linear-gradient(to right, #e8f5e9 0%, #f1f8e9 100%);
        }

        .submit-btn.all-valid {
            background: linear-gradient(45deg, #43A047 0%, #2E7D32 50%, #1B5E20 100%);
        }

        .submit-btn.all-valid:hover {
            background: linear-gradient(45deg, #388E3C 0%, #2E7D32 50%, #1B5E20 100%);
        }

        .word-count {
            font-size: 12px;
            color: #fff;
            text-align: right;
            margin-top: 4px;
        }

        .word-count.limit-reached {
            color: #ff4444;
        }

        /* Form box fade-in animation */
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

        /* Loader styles */
        .loader {
            display: none;
            width: 100%;
            height: 45px;
            position: relative;
            background: linear-gradient(45deg, #2196F3 0%, #1976D2 50%, #1565C0 100%);
            border-radius: 12px;
            overflow: hidden;
        }

        .loader::after {
            content: '';
            position: absolute;
            width: 40%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(300%);
            }
        }

        /* Button loading state */
        .submit-btn.loading {
            display: none;
        }

        .submit-btn span {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Video Background with proper path -->
    <video autoplay muted loop class="video-background">
        <source src="images/8733062-uhd_3840_2160_30fps.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <div class="overlay"></div>

    <div class="profile-container">
        <div class="header-container">
            <div class="profile-preview">
                <?php
                $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $profile_pic = $user['profile_picture'] ? 'uploads/profile_pictures/' . $user['profile_picture'] : 'assets/default-avatar.png';
                ?>
                <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture">
            </div>
            <h1>Complete Your Profile</h1>
        </div>

        <div class="success-message" id="successMessage"></div>
        
        <form id="profileForm" class="form-content">
            <div class="name-container">
                <div class="form-group">
                    <label class="username-label">Username</label>
                    <input type="text" 
                           value="<?php echo htmlspecialchars($user_data['username'] ?? ''); ?>" 
                           class="readonly-field" 
                           readonly>
                </div>

                <div class="form-group">
                    <label for="name" class="required-field">Full Name</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" 
                           required 
                           placeholder="Enter your full name"
                           oninput="capitalizeWords(this)">
                    <div class="error-message" id="nameError"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="bio" class="required-field">Bio</label>
                <textarea id="bio" 
                         name="bio" 
                         required
                         placeholder="Tell us about yourself... (max 100 words)"><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                <div class="word-count" id="bioWordCount">0/100 words</div>
                <div class="error-message" id="bioError"></div>
            </div>

            <div class="form-group">
                <label for="interests">Interests</label>
                <textarea id="interests" 
                         name="interests" 
                         placeholder="Share your interests... (max 100 words)"><?php echo htmlspecialchars($user_data['interests'] ?? ''); ?></textarea>
                <div class="word-count" id="interestsWordCount">0/100 words</div>
                <div class="error-message" id="interestsError"></div>
            </div>

            <button type="submit" class="submit-btn">
                <span>
                    Save Profile
                </span>
            </button>
            <div class="loader" id="submitLoader"></div>
        </form>
    </div>

    <script>
        function capitalizeWords(input) {
            let value = input.value;
            value = value.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
            input.value = value;
        }

        function countWords(str) {
            return str.trim().split(/\s+/).filter(word => word.length > 0).length;
        }

        function validateField(field) {
            const value = field.value.trim();
            if (field.required && !value) {
                return false;
            }
            if (value && countWords(value) > 100) {
                return false;
            }
            return true;
        }

        function updateWordCount(field, countElement) {
            const words = countWords(field.value);
            countElement.textContent = `${words}/100 words`;
            countElement.classList.toggle('limit-reached', words > 100);
        }

        function validateForm() {
            const name = document.getElementById('name');
            const bio = document.getElementById('bio');
            const interests = document.getElementById('interests');
            const submitBtn = document.querySelector('.submit-btn');

            let isValid = true;

            // Validate name
            if (!name.value.trim()) {
                document.getElementById('nameError').textContent = 'Name is required';
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            } else {
                name.classList.add('valid');
            }

            // Validate bio
            if (!bio.value.trim()) {
                document.getElementById('bioError').textContent = 'Bio is required';
                document.getElementById('bioError').style.display = 'block';
                isValid = false;
            } else if (countWords(bio.value) > 100) {
                document.getElementById('bioError').textContent = 'Bio cannot exceed 100 words';
                document.getElementById('bioError').style.display = 'block';
                isValid = false;
            } else {
                bio.classList.add('valid');
            }

            // Validate interests (optional but check word limit)
            if (interests.value.trim() && countWords(interests.value) > 100) {
                document.getElementById('interestsError').textContent = 'Interests cannot exceed 100 words';
                document.getElementById('interestsError').style.display = 'block';
                isValid = false;
            } else if (interests.value.trim()) {
                interests.classList.add('valid');
            }

            // Update button state
            submitBtn.classList.toggle('all-valid', isValid);
            return isValid;
        }

        // Add event listeners
        document.getElementById('bio').addEventListener('input', function() {
            updateWordCount(this, document.getElementById('bioWordCount'));
        });

        document.getElementById('interests').addEventListener('input', function() {
            updateWordCount(this, document.getElementById('interestsWordCount'));
        });

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                const submitBtn = document.querySelector('.submit-btn');
                const loader = document.getElementById('submitLoader');
                
                // Show loader, hide button
                submitBtn.classList.add('loading');
                loader.style.display = 'block';

                const formData = new FormData();
                formData.append('name', document.getElementById('name').value.trim());
                formData.append('bio', document.getElementById('bio').value.trim());
                formData.append('interests', document.getElementById('interests').value.trim());

                fetch('update_profile.php', {
                    method: 'POST',
                    body: formData // Changed from JSON to FormData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        loader.style.background = 'linear-gradient(45deg, #43A047 0%, #2E7D32 50%, #1B5E20 100%)';
                        setTimeout(() => {
                            window.location.href = 'view_profile.php';
                        }, 500);
                    } else {
                        throw new Error(data.message || 'An error occurred');
                    }
                })
                .catch(error => {
                    submitBtn.classList.remove('loading');
                    loader.style.display = 'none';
                    alert('Error: ' + error.message);
                });
            }
        });

        // Auto-capitalize full name on paste
        document.getElementById('name').addEventListener('paste', function(e) {
            setTimeout(() => {
                capitalizeWords(this);
            }, 0);
        });

        // Initial word count update
        updateWordCount(document.getElementById('bio'), document.getElementById('bioWordCount'));
        updateWordCount(document.getElementById('interests'), document.getElementById('interestsWordCount'));

        // Add entrance animation when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.profile-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>