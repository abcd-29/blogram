<?php
session_start();
include 'includes/db.php'; // Include database connection

// Redirect to forgot_password.php if OTP is not verified
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch the user's name from the database
$stmt = $conn->prepare("SELECT username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Server-side validation for password
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#_])(?=.*\d).{8,}$/', $new_password)) {
        $error_message = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one special character (!@#_), and one number.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        // Clear the session variables
        unset($_SESSION['otp']);
        unset($_SESSION['otp_expires']);
        unset($_SESSION['otp_verified']);
        unset($_SESSION['email']);

        // Redirect to login page
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Blogram</title>
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
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .container h2 {
            margin-bottom: 20px;
            font-family: 'Pacifico', cursive;
            font-size: 32px;
            color: #ff416c;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #333;
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
        .message {
            margin-top: 10px;
            font-size: 14px;
        }
        .password-conditions {
            display: none;
            position: absolute;
            top: 0;
            right: -270px; /* Adjusted to move the box to the right */
            width: 240px;
            padding: 10px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: left;
            font-size: 12px;
            margin-left: 10px;
        }
        .password-conditions p {
            margin: 5px 0;
            color: red;
        }
        .password-conditions p.valid {
            color: green;
        }
        .password-conditions::before {
            content: '';
            position: absolute;
            top: 10px;
            left: -20px;
            border-width: 10px;
            border-style: solid;
            border-color: transparent transparent transparent white;
            transform: rotate(45deg);
            box-shadow: -3px 3px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
    <script>
        function validatePassword() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const errorMessage = document.getElementById('error_message');

            const passwordPattern = /^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#_])(?=.*\d).{8,}$/;

            if (!passwordPattern.test(password)) {
                errorMessage.textContent = "Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one special character (!@#_), and one number.";
                return false;
            }

            if (password !== confirmPassword) {
                errorMessage.textContent = "Passwords do not match.";
                return false;
            }

            errorMessage.textContent = "";
            return true;
        }

        function checkPasswordConditions() {
            const password = document.getElementById('new_password').value;

            const lengthCondition = document.getElementById('length-condition');
            const uppercaseCondition = document.getElementById('uppercase-condition');
            const lowercaseCondition = document.getElementById('lowercase-condition');
            const specialCharCondition = document.getElementById('special-char-condition');
            const numberCondition = document.getElementById('number-condition');
            const passwordInput = document.getElementById('new_password');

            // Check length condition
            if (password.length >= 8) {
                lengthCondition.classList.add('valid');
                passwordInput.classList.add('valid');
            } else {
                lengthCondition.classList.remove('valid');
                passwordInput.classList.remove('valid');
            }

            // Check uppercase condition
            if (/[A-Z]/.test(password)) {
                uppercaseCondition.classList.add('valid');
            } else {
                uppercaseCondition.classList.remove('valid');
            }

            // Check lowercase condition
            if (/[a-z]/.test(password)) {
                lowercaseCondition.classList.add('valid');
            } else {
                lowercaseCondition.classList.remove('valid');
            }

            // Check special character condition
            if (/[!@#_]/.test(password)) {
                specialCharCondition.classList.add('valid');
            } else {
                specialCharCondition.classList.remove('valid');
            }

            // Check number condition
            if (/\d/.test(password)) {
                numberCondition.classList.add('valid');
            } else {
                numberCondition.classList.remove('valid');
            }

            checkConfirmPassword();
        }

        function checkConfirmPassword() {
            const password = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password');
            const confirmPasswordValue = confirmPassword.value;
            const validIcon = document.getElementById('confirm-password-valid-icon');

            if (password === confirmPasswordValue && confirmPasswordValue.length > 0) {
                confirmPassword.classList.add('valid');
                validIcon.style.display = 'block';
            } else {
                confirmPassword.classList.remove('valid');
                validIcon.style.display = 'none';
            }
        }

        function showPasswordConditions() {
            document.getElementById('password-conditions').style.display = 'block';
        }

        function hidePasswordConditions() {
            document.getElementById('password-conditions').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        <form method="post" onsubmit="return validatePassword();">
            <div class="input-group">
                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" onfocus="showPasswordConditions()" onblur="hidePasswordConditions()" oninput="checkPasswordConditions()" required>
                <div id="password-conditions" class="password-conditions">
                    <p id="length-condition">At least 8 characters long</p>
                    <p id="uppercase-condition">At least one uppercase letter</p>
                    <p id="lowercase-condition">At least one lowercase letter</p>
                    <p id="special-char-condition">At least one special character (!@#_)</p>
                    <p id="number-condition">At least one number</p>
                </div>
            </div>
            <div class="input-group">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" oninput="checkConfirmPassword()" required>
                <i id="confirm-password-valid-icon" class="fas fa-check valid-icon"></i>
            </div>
            <p id="error_message" class="message" style="color: red;"></p>
            <?php if (isset($error_message)) { echo '<p class="message" style="color: red;">' . $error_message . '</p>'; } ?>
            <button type="submit" class="submit-btn">Reset Password</button>
        </form>
    </div>
</body>
</html>