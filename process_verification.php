<?php
session_start();
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['temp_user'])) {
        header("Location: register.php");
        exit();
    }

    $entered_otp = $_POST['otp'];
    $stored_otp = $_SESSION['temp_user']['otp'];

    if ($entered_otp == $stored_otp) {
        // OTP is correct, proceed with registration
        $username = $_SESSION['temp_user']['username'];
        $email = $_SESSION['temp_user']['email'];
        $password = $_SESSION['temp_user']['password'];

        try {
            // Begin transaction
            $conn->begin_transaction();

            // Insert user data
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            
            if ($stmt->execute()) {
                // Get the new user's ID
                $user_id = $conn->insert_id;
                
                // Commit transaction
                $conn->commit();
                
                // Clear temporary session data
                unset($_SESSION['temp_user']);
                
                // Set user session
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;
                
                // Redirect to profile page
                header("Location: profile.php");
                exit();
            } else {
                throw new Exception("Error executing query");
            }
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            $_SESSION['error'] = "Registration failed: " . $e->getMessage();
            header("Location: verify_otp.php");
            exit();
        }
    } else {
        // Invalid OTP
        $_SESSION['error'] = "Invalid OTP. Please try again.";
        header("Location: verify_otp.php");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?> 