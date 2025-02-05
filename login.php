<?php
session_start();
include 'includes/db.php'; // Include database connection
include 'includes/auth.php';

// Destroy any existing session to force login
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
    session_start(); // Start a new session
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            header("Location: profile.php");
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blogram</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your CSS file -->
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: url('images/luca-micheli-ruWkmt3nU58-unsplash.jpg') no-repeat center center/cover;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.2); /* Semi-transparent background */
            animation: fadeIn 1s ease-in-out; /* Animation effect */
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .container h2 {
            margin-bottom: 20px;
            font-family: 'Pacifico', cursive;
            font-size: 36px;
            color: #ff416c;
            transform: scale(1.2); /* Increase the size of the word "Login" */
        }
        .input-group {
            margin-bottom: 20px;
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-group input {
            width: calc(100% - 40px);
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: transparent;
            color: #333;
        }
        .input-group input:focus {
            outline: none;
        }
        .input-group input.valid {
            border-color: green;
        }
        .input-group .valid-icon {
            position: absolute;
            right: 15px;
            color: green;
            display: none;
        }
        .input-group input.valid + .valid-icon {
            display: block;
        }
        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 30px;
            background: #ff2d2d;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover {
            background: #e60000;
        }
        .loader {
            display: none;
            width: 30px;
            height: 30px;
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #ff2d2d;
            animation: spin 2s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
            color: red;
        }
        .signup-link {
            margin-top: 20px;
            font-size: 14px;
            color: white;
            font-weight: bold;
        }
        .signup-link a {
            color: #ff416c;
            text-decoration: none;
            font-weight: bold;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
        .forgot-password {
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
            color: white;
        }
        .forgot-password a {
            color: #ff416c;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        footer {
            padding: 10px 0;
            text-align: center;
            font-size: 14px;
            color: #333;
            background: none; /* Remove background */
        }
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }
            .container h2 {
                font-size: 28px;
            }
            .input-group input {
                padding: 10px 15px;
                font-size: 14px;
            }
            .submit-btn {
                padding: 10px;
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            .container {
                padding: 15px;
                max-width: 95%;
            }
            .container h2 {
                font-size: 24px;
            }
            .input-group input {
                padding: 8px 10px;
                font-size: 12px;
            }
            .submit-btn {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
    <script>
        function showLoader() {
            document.querySelector('.submit-btn').style.display = 'none';
            document.querySelector('.loader').style.display = 'block';
        }

        window.onload = function() {
            document.getElementById('error_message').style.display = 'none';
        };
    </script>
</head>
<body>
    <div class="main-content">
        <div class="container">
            <h2>Login</h2>
            <?php if (!empty($error_message)) { echo '<p id="error_message" class="message">' . $error_message . '</p>'; } ?>
            <form method="post" onsubmit="showLoader();">
                <div class="input-group">
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="submit-btn">Login</button>
                <div class="loader"></div>
            </form>
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <div class="signup-link">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>
        </div>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> Blogram. All rights reserved.
    </footer>
</body>
</html>