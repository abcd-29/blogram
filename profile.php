<?php
session_start();
include 'includes/db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, name, profile_picture, bio FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Determine display name based on registration/login scenario
$display_name = $user['username']; // Default to username

// If name exists in database and user has logged in before
if (!empty($user['name'])) {
    $display_name = $user['name'];
}

// If this is first time after registration (you can set a session variable during registration)
if (isset($_SESSION['just_registered']) && $_SESSION['just_registered']) {
    $display_name = $user['username'];
    // Clear the registration flag
    unset($_SESSION['just_registered']);
}

// Fetch user's blogs
$stmt = $conn->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$blogs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Determine if profile is complete (all required fields filled except interests)
$has_profile_picture = !empty($user['profile_picture']);
$has_complete_profile = !empty($user['bio']) && !empty($user['name']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($display_name); ?>'s Profile - Blogram</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your CSS file -->
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
        .welcome-message {
            font-size: 50px; /* Increased font size */
            color: black; /* Changed color to white */
            text-align: center;
            margin: 20px 0;
            opacity: 0;
            animation: fadeInWords 0.1s forwards; /* Decreased starting time */
            font-family: 'Pacifico', cursive; /* Changed font style */
            font-weight: bold; /* Made it bold */

        }

        .welcome-message span {
            display: inline-block;
            opacity: 0;
            margin-right: 5px; /* Defined space between words */
        }

        @keyframes fadeInWords {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes fadeInLetter {
            0% { opacity: 0; transform: translateX(-20px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .arrow-button {
            background: none; /* No background */
            color: black;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;

            align-items: center;
            cursor: pointer;
            font-size: 20px;
            transition: transform 0.3s;
            opacity: 0; /* Initially hidden */
            margin-top: 20px; /* Space below the welcome message */
            margin-left: 10px; /* Shifted to the right */
        }
        .arrow-button.show {
            opacity: 1; /* Show after animation */
        }
        .arrow-button:hover {
            transform: scale(1.2); /* Extend arrow on hover */
        }
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            background: rgba(0, 0, 0, 0); /* Semi-transparent background */
            color: #fff;
            padding: 10px 0;
            font-size: 12px;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 90%; /* Base width */
            max-width: 1200px; /* Maximum width */
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin: 0 auto;
        }

        /* Media Queries for Container Size Only */
        @media screen and (max-width: 1400px) {
            .container {
                max-width: 1000px;
                padding: 35px;
            }
        }

        @media screen and (max-width: 1200px) {
            .container {
                max-width: 800px;
                padding: 30px;
            }
        }

        @media screen and (max-width: 992px) {
            .container {
                max-width: 700px;
                padding: 25px;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                max-width: 600px;
                width: 95%; /* Slightly wider on smaller screens */
                padding: 20px;
            }
        }

        @media screen and (max-width: 576px) {
            .container {
                max-width: 500px;
                padding: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                max-width: 400px;
                width: 98%; /* Even wider on mobile */
                padding: 15px;
            }
        }

        @media screen and (max-width: 320px) {
            .container {
                max-width: 300px;
                padding: 10px;
            }
        }

        /* Height-based adjustments */
        @media screen and (max-height: 800px) {
            .container {
                margin-top: 20px;
                margin-bottom: 20px;
            }
        }

        @media screen and (max-height: 600px) {
            .container {
                margin-top: 15px;
                margin-bottom: 15px;
            }
        }

        /* Base container and form styles */
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            margin: auto;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn {
            transition: all 0.3s ease;
        }

        /* Extra large devices */
        @media screen and (min-width: 1400px) {
            .form-container {
                width: 550px;
                padding: 40px;
            }

            .form-control {
                padding: 15px 20px;
                font-size: 16px;
            }

            .btn {
                padding: 15px 30px;
                font-size: 16px;
            }
        }

        /* Large devices */
        @media screen and (min-width: 1200px) and (max-width: 1399px) {
            .form-container {
                width: 500px;
                padding: 35px;
            }

            .form-control {
                padding: 14px 18px;
                font-size: 15px;
            }

            .btn {
                padding: 14px 28px;
                font-size: 15px;
            }
        }

        /* Medium devices */
        @media screen and (min-width: 992px) and (max-width: 1199px) {
            .form-container {
                width: 450px;
                padding: 30px;
            }

            .form-control {
                padding: 13px 16px;
                font-size: 14px;
            }

            .btn {
                padding: 13px 26px;
                font-size: 14px;
            }
        }

        /* Small devices */
        @media screen and (min-width: 768px) and (max-width: 991px) {
            .form-container {
                width: 400px;
                padding: 25px;
            }

            .form-control {
                padding: 12px 15px;
                font-size: 14px;
            }

            .btn {
                padding: 12px 24px;
                font-size: 14px;
            }
        }

        /* Extra small devices */
        @media screen and (min-width: 576px) and (max-width: 767px) {
            .form-container {
                width: 350px;
                padding: 20px;
            }

            .form-control {
                padding: 11px 14px;
                font-size: 13px;
            }

            .btn {
                padding: 11px 22px;
                font-size: 13px;
            }
        }

        /* Mobile devices */
        @media screen and (max-width: 575px) {
            .form-container {
                width: 90%;
                padding: 15px;
            }

            .form-control {
                padding: 10px 12px;
                font-size: 13px;
            }

            .btn {
                padding: 10px 20px;
                font-size: 13px;
            }
        }

        /* Very small mobile devices */
        @media screen and (max-width: 320px) {
            .form-container {
                width: 95%;
                padding: 12px;
            }

            .form-control {
                padding: 9px 10px;
                font-size: 12px;
            }

            .btn {
                padding: 9px 18px;
                font-size: 12px;
            }
        }

        /* Maintain fixed structure */
        .form-structure {
            display: flex;
            flex-direction: column;
            gap: 20px;
            position: relative;
        }

        /* Keep labels consistent */
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        /* Maintain button position */
        .button-container {
            margin-top: 20px;
            text-align: center;
        }

        /* Keep input fields aligned */
        .input-group {
            position: relative;
            width: 100%;
        }

        /* Maintain glass effect */
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Ensure smooth transitions */
        * {
            transition: all 0.3s ease;
        }
    </style>
    <script>
        // Prevent going back to previous pages
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function(event) {
            window.history.pushState(null, null, window.location.href);
            window.location.href = 'index.php';
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Animate welcome message
            const welcomeMessage = document.querySelector('.welcome-message');
            const arrowButton = document.querySelector('.arrow-button');
            if (welcomeMessage) {
                const letters = welcomeMessage.textContent.split('');
                welcomeMessage.innerHTML = letters.map((letter, index) => `<span style="animation: fadeInLetter 0.1s ${index * 0.1}s forwards;">${letter}</span>`).join('');
                setTimeout(() => {
                    arrowButton.classList.add('show');
                }, letters.length * 100); // Adjust timing based on the number of letters
            }

            // Handle arrow button click
            arrowButton.addEventListener('click', function () {
                const hasProfilePicture = <?php echo json_encode($has_profile_picture); ?>;
                const hasCompleteProfile = <?php echo json_encode($has_complete_profile); ?>;

                if (!hasProfilePicture) {
                    window.location.href = 'upload_profile_picture.php';
                } else if (!hasCompleteProfile) {
                    window.location.href = 'complete_profile.php';
                } else {
                    window.location.href = 'blogs.php';
                }
            });
        });
    </script>
</head>
<body>
    <video class="video-background" autoplay muted loop>
        <source src="images/8733062-uhd_3840_2160_30fps.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <div class="welcome-message">
        Hi <?php echo htmlspecialchars($display_name); ?>, Blogram Welcomes You!
    </div>
    <button class="arrow-button">
        <i class="fas fa-arrow-right"></i>

    </button>

    <footer>
        <p>&copy; 2023 Blogram. All rights reserved.</p>
    </footer>
</body>
</html>