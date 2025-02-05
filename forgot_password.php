<?php
session_start();
include 'includes/db.php'; // Include database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php'; // Include Composer's autoload file
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function send_email($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth = true;
        $mail->Username = 'sc22cs301124@medicaps.ac.in'; // SMTP username
        $mail->Password = 'xezk uphx jllv vseu'; // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('sc22cs301124@medicaps.ac.in', 'Blogram');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for password reset';
        $mail->Body = "Your OTP for password reset is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['email']) && !isset($_POST['otp'])) {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        // Generate a unique OTP
        $otp = rand(1000, 9999);

        // Store the OTP in the session with an expiration time
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expires'] = time() + 300; // OTP expires in 5 minutes
        $_SESSION['email'] = $email;

        // Send the OTP to the user's email address
        if (send_email($email, $otp)) {
            $success_message = "An OTP has been sent to your email address.";
        } else {
            $error_message = "Failed to send OTP. Please try again.";
        }
    } else {
        $error_message = "No account found with that email address.";
    }
} elseif (isset($_POST['otp'])) {
    $otp = $_POST['otp'];

    // Verify the OTP
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $otp && time() < $_SESSION['otp_expires']) {
        // OTP is valid, set otp_verified session variable and proceed with password reset
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $error_message = "Invalid or expired OTP.";
    }
} elseif (isset($_POST['resend_otp'])) {
    $email = $_SESSION['email'];

    // Generate a new OTP
    $otp = rand(1000, 9999);

    // Store the new OTP in the session with an expiration time
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expires'] = time() + 300; // OTP expires in 5 minutes

    // Send the new OTP to the user's email address
    if (send_email($email, $otp)) {
        $success_message = "A new OTP has been sent to your email address.";
    } else {
        $error_message = "Failed to send OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Blogram</title>
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
        .input-group {
            margin-bottom: 20px;
            position: relative;
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
        .input-group .prefix {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
            font-size: 16px;
        }
        .input-group .valid, .input-group .invalid {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
        }
        .input-group .valid {
            color: green;
        }
        .input-group .invalid {
            color: red;
        }
        .submit-btn, .resend-btn {
            width: 48%;
            padding: 15px;
            border: none;
            border-radius: 30px;
            background: #ff2d2d;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover, .resend-btn:hover {
            background: #e60000;
        }
        .resend-btn:disabled {
            cursor: not-allowed;
            background: #ff9999;
        }
        .message {
            margin-top: 10px;
            font-size: 14px;
        }
        .countdown {
            margin-top: 10px;
            font-size: 14px;
            color: #ff416c;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        function validateEmail(emailInput) {
            const email = emailInput.value;
            const validIcon = document.querySelector('.valid');
            const invalidIcon = document.querySelector('.invalid');

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailPattern.test(email)) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'validate_fp.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function () {
                    const response = JSON.parse(this.responseText);
                    if (response.status === 'success') {
                        emailInput.style.borderColor = 'green';
                        validIcon.style.display = 'block';
                        invalidIcon.style.display = 'none';
                    } else {
                        emailInput.style.borderColor = 'red';
                        validIcon.style.display = 'none';
                        invalidIcon.style.display = 'block';
                    }
                };
                xhr.send('email=' + email);
            } else {
                emailInput.style.borderColor = 'red';
                validIcon.style.display = 'none';
                invalidIcon.style.display = 'block';
            }
        }

        function startCountdown() {
            const resendBtn = document.getElementById('resend-btn');
            const countdown = document.getElementById('countdown');
            let timeLeft = 15;

            resendBtn.disabled = true;
            resendBtn.style.cursor = 'not-allowed';

            const timer = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    countdown.textContent = '';
                    resendBtn.disabled = false;
                    resendBtn.style.cursor = 'pointer';
                } else {
                    countdown.textContent = `Resend OTP in ${timeLeft} seconds`;
                    timeLeft--;
                }
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function () {
                validateEmail(emailInput);
            });

            if (document.getElementById('otp')) {
                startCountdown();
            }

            const otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.addEventListener('input', function () {
                    otpInput.value = otpInput.value.replace(/\D/g, ''); // Remove non-numeric characters
                });
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="post">
            <div class="input-group">
                <input type="email" id="email" name="email" placeholder="Enter your email address" <?php if (isset($success_message)) { echo 'value="' . htmlspecialchars($_SESSION['email']) . '" readonly'; } ?> required>
                <i class="fas fa-check valid" <?php if (isset($success_message)) { echo 'style="display: block;"'; } ?>></i>
                <i class="fas fa-times invalid"></i>
            </div>
            <?php if (isset($success_message)) { echo '<p class="message" style="color: green;">' . $success_message . '</p>'; } ?>
            <?php if (isset($error_message)) { echo '<p class="message" style="color: red;">' . $error_message . '</p>'; } ?>
            <?php if (isset($success_message)) { ?>
                <div class="input-group">
                    <input type="text" id="otp" name="otp" placeholder="Enter the OTP" maxlength="4" pattern="\d{4}" required>
                </div>
                <p id="countdown" class="countdown"></p>
                <div class="button-group">
                    <button type="submit" class="submit-btn">Verify</button>
                    <button type="submit" name="resend_otp" id="resend-btn" class="resend-btn" onclick="startCountdown()">Resend OTP</button>
                </div>
            <?php } else { ?>
                <button type="submit" class="submit-btn">Submit</button>
            <?php } ?>
        </form>
    </div>
</body>
</html>