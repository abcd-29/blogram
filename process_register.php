<?php
session_start();
require 'includes/db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php'; // Include Composer's autoload file
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Generate OTP
    $otp = rand(1000, 9999);
    
    // Store user data in session
    $_SESSION['temp_user'] = [
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
        'otp' => $otp
    ];
    
    // Send OTP via email
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'sc22cs301124@medicaps.ac.in'; // Your Gmail
    $mail->Password = 'xezk uphx jllv vseu'; // Your Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    

    $mail->setFrom('sc22cs301124@medicaps.ac.in', 'Blogram');
    $mail->addAddress($email);
    $mail->Subject = 'Email Verification OTP';
    $mail->Body = "Your OTP for registration is: $otp";
    

    if($mail->send()) {
        header("Location: verify_otp.php");
    } else {
        echo "Error sending email: " . $mail->ErrorInfo;
    }
}
?> 