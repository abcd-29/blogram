<!DOCTYPE html>
<html>
<head>
    <title>Create Account | Blogram</title>
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
            margin-bottom: 30px;
        }

        .logo h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: white;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #667eea;
            outline: none;
        }

        .error-message {
            color: #ff4444;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .password-requirements {
            position: absolute;
            right: -230px;
            top: 0;
            width: 200px;
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            border-radius: 10px;
            display: none;
        }

        .requirement {
            color: white;
            font-size: 12px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }

        .requirement i {
            margin-right: 5px;
        }

        .valid {
            color: #4CAF50;
        }

        .invalid {
            color: #ff4444;
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
            opacity: 0.7;
            pointer-events: none;
        }

        button.active {
            opacity: 1;
            pointer-events: all;
        }

        button.active:hover {
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 14px;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .validation-icon {
            position: absolute;
            right: 10px;
            top: 40px;
            color: #4CAF50;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>Create Account</h1>
        </div>
        
        <form id="registerForm" action="process_register.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" required>
                <i class="fas fa-check validation-icon" id="usernameValid"></i>
                <div class="error-message" id="usernameError"></div>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" required>
                <i class="fas fa-check validation-icon" id="emailValid"></i>
                <div class="error-message" id="emailError"></div>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
                <i class="fas fa-check validation-icon" id="passwordValid"></i>
                <div class="password-requirements" id="passwordRequirements">
                    <div class="requirement" id="length"><i class="fas fa-circle"></i> At least 8 characters</div>
                    <div class="requirement" id="uppercase"><i class="fas fa-circle"></i> One uppercase letter</div>
                    <div class="requirement" id="lowercase"><i class="fas fa-circle"></i> One lowercase letter</div>
                    <div class="requirement" id="number"><i class="fas fa-circle"></i> One number</div>
                    <div class="requirement" id="special"><i class="fas fa-circle"></i> One special character</div>
                </div>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" id="confirmPassword" required>
                <i class="fas fa-check validation-icon" id="confirmPasswordValid"></i>
                <div class="error-message" id="confirmPasswordError"></div>
            </div>
            
            <button type="submit" id="submitBtn">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        let validUsername = false;
        let validEmail = false;
        let validPassword = false;
        let validConfirmPassword = false;

        // Username validation
        document.getElementById('username').addEventListener('input', async function() {
            const username = this.value;
            if(username.length < 3) {
                showError('username', 'Username must be at least 3 characters');
                validUsername = false;
            } else {
                const response = await fetch('check_username.php', {
                    method: 'POST',
                    body: JSON.stringify({ username: username }),
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await response.json();
                
                if(data.exists) {
                    showError('username', 'Username already taken');
                    validUsername = false;
                } else {
                    hideError('username');
                    document.getElementById('usernameValid').style.display = 'block';
                    validUsername = true;
                }
            }
            updateSubmitButton();
        });

        // Email validation
        document.getElementById('email').addEventListener('input', async function() {
            const email = this.value;
            if(!isValidEmail(email)) {
                showError('email', 'Please enter a valid email');
                validEmail = false;
            } else {
                const response = await fetch('check_email.php', {
                    method: 'POST',
                    body: JSON.stringify({ email: email }),
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await response.json();
                
                if(data.exists) {
                    showError('email', 'Email already registered');
                    validEmail = false;
                } else {
                    hideError('email');
                    document.getElementById('emailValid').style.display = 'block';
                    validEmail = true;
                }
            }
            updateSubmitButton();
        });

        // Password validation
        const passwordInput = document.getElementById('password');
        const requirements = document.getElementById('passwordRequirements');

        passwordInput.addEventListener('focus', () => {
            requirements.style.display = 'block';
        });

        passwordInput.addEventListener('blur', () => {
            if(validPassword) {
                requirements.style.display = 'none';
            }
        });

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            const conditions = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*]/.test(password)
            };

            for(let condition in conditions) {
                const element = document.getElementById(condition);
                if(conditions[condition]) {
                    element.classList.add('valid');
                    element.classList.remove('invalid');
                    element.querySelector('i').className = 'fas fa-check';
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    element.querySelector('i').className = 'fas fa-circle';
                }
            }

            validPassword = Object.values(conditions).every(condition => condition);
            if(validPassword) {
                document.getElementById('passwordValid').style.display = 'block';
            } else {
                document.getElementById('passwordValid').style.display = 'none';
            }
            
            validateConfirmPassword();
            updateSubmitButton();
        });

        // Confirm password validation
        document.getElementById('confirmPassword').addEventListener('input', validateConfirmPassword);

        function validateConfirmPassword() {
            const confirmPassword = document.getElementById('confirmPassword');
            const password = document.getElementById('password').value;
            
            if(confirmPassword.value === password && validPassword) {
                hideError('confirmPassword');
                document.getElementById('confirmPasswordValid').style.display = 'block';
                validConfirmPassword = true;
            } else {
                showError('confirmPassword', 'Passwords do not match');
                document.getElementById('confirmPasswordValid').style.display = 'none';
                validConfirmPassword = false;
            }
            updateSubmitButton();
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            if(validUsername && validEmail && validPassword && validConfirmPassword) {
                submitBtn.classList.add('active');
            } else {
                submitBtn.classList.remove('active');
            }
        }

        function showError(field, message) {
            document.getElementById(`${field}Error`).textContent = message;
            document.getElementById(`${field}Error`).style.display = 'block';
            document.getElementById(field).style.borderColor = '#ff4444';
            document.getElementById(`${field}Valid`).style.display = 'none';
        }

        function hideError(field) {
            document.getElementById(`${field}Error`).style.display = 'none';
            document.getElementById(field).style.borderColor = 'rgba(255, 255, 255, 0.2)';
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Form submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if(validUsername && validEmail && validPassword && validConfirmPassword) {
                this.submit();
            }
        });
    </script>
</body>
</html> 