<?php
session_start();
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 1 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

include "db.php";

// Redirect if already logged in
if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admindash.php");
        exit();
    } else if ($_SESSION['role'] === 'user') {
        header("Location: home.php");
        exit();
    }
}

// Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["loginEmail"];
    $password = $_POST["loginPassword"];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            
            $_SESSION["email"] = $row["email"];
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["user_name"] = $row["fname"];
            $_SESSION["role"] = $row["role"];
            if ($row["role"] === "admin") {
                header("Location: admindash.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $login_error = "Invalid email or password.";
        }
    } else {
        $login_error = "Invalid email or password.";
    }
}

// Signup handler (no AJAX, just regular POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["signupEmail"];
    $phoneNumber = $_POST["phoneNumber"];
    $password = $_POST["signupPassword"];
    $userType = "user";

    // Only check for duplicate email, then insert
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        $signup_error = "Email already exists. Please use a different email.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fname, lname, email, phone_no, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $firstName, $lastName, $email, $phoneNumber, $hashedPassword, $userType);
        
        if ($stmt->execute()) {
            $signup_success = "Account created successfully. Please sign in.";
        } else {
            $signup_error = "Error creating account. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseCompass - Sign In / Sign Up</title>
    <link rel="stylesheet" href="login.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <div class="logo">🧭</div>
                <span class="brand-text">CourseCompass</span>
            </div>
        </div>
    </nav>

    <!-- Auth Section -->
    <section class="auth-section">
        <div class="auth-container">
            <div class="form-container">
                <!-- Toggle Buttons -->
                <div class="auth-toggle">
                    <button class="toggle-btn active" id="loginToggle" type="button">Sign In</button>
                    <button class="toggle-btn" id="signupToggle" type="button">Sign Up</button>
                </div>

                <!-- Login Form -->
                <form class="auth-form" id="loginForm" method="POST" action="login.php">
                    <h2 class="form-title">Welcome Back!</h2>
                    <p class="form-subtitle">Sign in to continue your learning journey</p>
                    
                    <div class="form-group">
                        <label for="loginEmail" class="form-label">Email Address</label>
                        <input type="email" id="loginEmail" name="loginEmail" class="form-input" placeholder="Enter your email" required>
                    </div>
                    
                    <!-- Login Password Field -->
                    <div class="form-group" style="position:relative;">
                        <label for="loginPassword" class="form-label">Password</label>
                        <input type="password" id="loginPassword" name="loginPassword" class="form-input" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    
                    <?php if (isset($login_error)): ?>
                        <div class="form-error"><?php echo $login_error; ?></div>
                    <?php endif; ?>
                    
                    <button type="submit" name="login" class="btn btn-primary btn-full">Sign In</button>
                </form>

                <!-- Signup Form -->
                <form class="auth-form hidden" id="signupForm" method="POST" action="login.php">
                    <h2 class="form-title">Create Account</h2>
                    <p class="form-subtitle">Join us and discover your perfect courses</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" id="firstName" name="firstName" class="form-input" placeholder="First name" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" id="lastName" name="lastName" class="form-input" placeholder="Last name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="signupEmail" class="form-label">Email Address</label>
                        <input type="email" id="signupEmail" name="signupEmail" class="form-input" placeholder="Enter your email" required>
                    </div>
                    
                    <!-- Signup Password Field -->
                    <div class="form-group" style="position:relative;">
                        <label for="signupPassword" class="form-label">Password</label>
                        <input type="password" id="signupPassword" name="signupPassword" class="form-input" placeholder="Create a password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('signupPassword')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Signup Confirm Password Field -->
                    <div class="form-group" style="position:relative;">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" placeholder="Confirm your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Phone Number Field -->
                    <div class="form-group">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" class="form-input" placeholder="Enter your phone number" required>
                    </div>
                 
                    <?php if (isset($signup_error)): ?>
                        <div class="form-error"><?php echo $signup_error; ?></div>
                    <?php endif; ?>
                    <?php if (isset($signup_success)): ?>
                        <div class="form-success"><?php echo $signup_success; ?></div>
                    <?php endif; ?>

                    <button type="submit" name="signup" class="btn btn-primary btn-full">Create Account</button>
                </form>
       
    <script>
        // Toggle between login and signup forms
        const loginToggle = document.getElementById('loginToggle');
        const signupToggle = document.getElementById('signupToggle');
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');

        loginToggle.addEventListener('click', () => {
            loginToggle.classList.add('active');
            signupToggle.classList.remove('active');
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
        });

        signupToggle.addEventListener('click', () => {
            signupToggle.classList.add('active');
            loginToggle.classList.remove('active');
            signupForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });

        // Utility functions
        function showError(input, message) {
            let errorDiv = input.parentElement.querySelector('.input-error');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'input-error';
                errorDiv.style.color = '#e53e3e';
                errorDiv.style.fontSize = '0.9em';
                errorDiv.style.marginTop = '4px';
                input.parentElement.appendChild(errorDiv);
            }
            errorDiv.textContent = message;
            input.classList.add('error-border');
        }

        function clearError(input) {
            let errorDiv = input.parentElement.querySelector('.input-error');
            if (errorDiv) errorDiv.textContent = '';
            input.classList.remove('error-border');
        }

        // Validation regex
        const nameRegex = /^[A-Za-z]+$/;
        const emailRegex = /^\S+@\S+\.\S+$/;
        const phoneRegex = /^[6-9]\d{9}$/;

        // Live validation for Login
        document.getElementById('loginEmail').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Email is required.");
            } else if (!emailRegex.test(this.value)) {
                showError(this, "Please enter a valid email address.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('loginPassword').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Password is required.");
            } else {
                clearError(this);
            }
        });

        loginForm.addEventListener('submit', function(e) {
            let valid = true;
            const emailInput = document.getElementById('loginEmail');
            const passwordInput = document.getElementById('loginPassword');

            if (!emailInput.value) {
                showError(emailInput, "Email is required.");
                valid = false;
            } else if (!emailRegex.test(emailInput.value)) {
                showError(emailInput, "Please enter a valid email address.");
                valid = false;
            }
            if (!passwordInput.value) {
                showError(passwordInput, "Password is required.");
                valid = false;
            }
            if (!valid) e.preventDefault();
        });

        // Live validation for Signup
        document.getElementById('firstName').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "First name is required.");
            } else if (!nameRegex.test(this.value)) {
                showError(this, "First name should contain only letters.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('lastName').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Last name is required.");
            } else if (!nameRegex.test(this.value)) {
                showError(this, "Last name should contain only letters.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('signupEmail').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Email is required.");
            } else if (!emailRegex.test(this.value)) {
                showError(this, "Please enter a valid email address.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('signupPassword').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Password is required.");
            } else if (this.value.length < 8) {
                showError(this, "Password must be at least 8 characters.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('confirmPassword').addEventListener('input', function() {
            const passwordInput = document.getElementById('signupPassword');
            if (!this.value) {
                showError(this, "Please confirm your password.");
            } else if (this.value !== passwordInput.value) {
                showError(this, "Passwords do not match.");
            } else {
                clearError(this);
            }
        });

        document.getElementById('phoneNumber').addEventListener('input', function() {
            if (!this.value) {
                showError(this, "Phone number is required.");
            } else if (!phoneRegex.test(this.value)) {
                showError(this, "Please enter a valid 10-digit phone number starting with 6-9.");
            } else {
                clearError(this);
            }
        });

        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        // Force reload if loaded from back/forward cache
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

    </script>
<?php 
?>
</body>
</html>