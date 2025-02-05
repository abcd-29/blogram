<?php
session_start();
if (!isset($_SESSION['temp_user'])) {
    header("Location: register.php");
    exit();
}
$email = $_SESSION['temp_user']['email'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP | Blogram</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url('images/jonny-gios-49U_31wsJxU-unsplash.jpg');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
        }

        .message {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            font-size: 14px;
            line-height: 1.6;
        }

        .email-sent {
            color:rgb(255, 0, 0);
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .otp-input {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .otp-input input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .otp-input input:focus {
            border-color:rgb(0, 47, 255);
            outline: none;
            font-weight: 700;
        }

        button {
            width: 100%;
            padding: 12px;
            background: rgba(102, 126, 234, 0.8);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        button:hover {
            transform: translateY(-2px);
        }

        .resend-link {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 14px;
        }

        .resend-link a {
            color:rgb(0, 47, 255);
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
        }


        .resend-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #ff4444;
            font-size: 14px;
            text-align: center;
            margin-top: 10px;
            display: none;
        }

        .resend-link a.disabled {
            opacity: 0.5;
            pointer-events: none;
            cursor: default;
        }

        /* Loader styles */
        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            backdrop-filter: blur(5px);
        }

        .loader {
            width: 48px;
            height: 48px;
            border: 5px solid #FFF;
            border-bottom-color: transparent;
            border-radius: 50%;
            display: inline-block;
            box-sizing: border-box;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="loader-container" id="loaderContainer">
        <span class="loader"></span>
    </div>
    <div class="container">
        <div class="logo">
            <h1>Verify OTP</h1>
        </div>
        
        <div class="message">
            Please enter the 4-digit verification code sent to<br>
            <span class="email-sent"><?php echo htmlspecialchars($email); ?></span>
        </div>
        
        <form id="otpForm" action="process_verification.php" method="POST">
            <div class="otp-input">
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric" required>
                <input type="hidden" name="otp" id="otpValue">
            </div>
            
            <button type="submit">Verify OTP</button>
        </form>

        <div class="resend-link">
            Didn't receive the code? <a href="#" id="resendOtp" class="disabled">Resend OTP (<span id="countdown">15</span>s)</a>
        </div>
        <div class="error-message" id="errorMessage"></div>
    </div>

    <script>
        // Auto-focus next input
        const inputs = document.querySelectorAll('.otp-input input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (this.value.length === 1) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && !this.value) {
                    if (index > 0) {
                        inputs[index - 1].focus();
                    }
                }
            });
        });

        // Form submission
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            let otp = '';
            inputs.forEach(input => {
                otp += input.value;
            });
            document.getElementById('otpValue').value = otp;
            
            if (otp.length === 4) {
                // Show loader and disable button
                document.getElementById('loaderContainer').style.display = 'flex';
                document.querySelector('button[type="submit"]').disabled = true;
                
                // Submit form after a small delay to show the loader
                setTimeout(() => {
                    this.submit();
                }, 500);
            }
        });

        // Countdown Timer
        let timeLeft = 15;
        const countdownDisplay = document.getElementById('countdown');
        const resendLink = document.getElementById('resendOtp');
        resendLink.classList.add('disabled');
        
        function startCountdown() {
            resendLink.classList.add('disabled');
            timeLeft = 15;
            
            const timer = setInterval(() => {
                timeLeft--;
                countdownDisplay.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    resendLink.classList.remove('disabled');
                    countdownDisplay.textContent = '15';
                }
            }, 1000);
        }

        // Start countdown on page load
        startCountdown();

        // Resend OTP with countdown
        document.getElementById('resendOtp').addEventListener('click', async function(e) {
            e.preventDefault();
            if (this.classList.contains('disabled')) return;
            
            try {
                const response = await fetch('resend_otp.php');
                const data = await response.json();
                if (data.success) {
                    document.getElementById('errorMessage').style.display = 'block';
                    document.getElementById('errorMessage').style.color = '#4CAF50';
                    document.getElementById('errorMessage').textContent = 'OTP has been resent to your email';
                    startCountdown();
                } else {
                    throw new Error('Failed to resend OTP');
                }
            } catch (error) {
                document.getElementById('errorMessage').style.display = 'block';
                document.getElementById('errorMessage').style.color = '#ff4444';
                document.getElementById('errorMessage').textContent = 'Failed to resend OTP. Please try again.';
            }
        });
    </script>
</body>
</html> 