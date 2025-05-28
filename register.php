<?php
// Include database and user model
require_once 'config/database.php';
require_once 'models/User.php';
require_once 'includes/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$first_name = $last_name = $email = $phone = $password = $confirm_password = "";
$first_name_err = $last_name_err = $email_err = $phone_err = $password_err = $confirm_password_err = $terms_err = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate first name
    if (empty(trim($_POST["firstName"]))) {
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["firstName"]);
    }
    
    // Validate last name
    if (empty(trim($_POST["lastName"]))) {
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["lastName"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
        
        // Check if email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        } else {
            // Get database connection
            $database = new Database();
            $db = $database->getConnection();
            
            // Create user object
            $user = new User($db);
            
            // Check if email already exists
            if ($user->checkEmailExists($email)) {
                $email_err = "This email is already registered.";
            }
        }
    }
    
    // Validate phone
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirmPassword"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirmPassword"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }
    
    // Validate terms
    if (!isset($_POST["terms"])) {
        $terms_err = "You must agree to the terms and conditions.";
    }
    
    // Check input errors before inserting in database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($phone_err) && empty($password_err) && empty($confirm_password_err) && empty($terms_err)) {
        // Get database connection
        $database = new Database();
        $db = $database->getConnection();
        
        // Create user object
        $user = new User($db);
        
        // Set user values
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->email = $email;
        $user->phone = $phone;
        $user->password = $password;
        $user->role = 'user';
        $user->status = 'active';
        
        // Register user
        $result = $user->register();
        if ($result) {
            // Set success message
            $_SESSION['success_msg'] = "Registration successful! You can now log in.";
            
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            // Get the error message from the error log
            $error = error_get_last();
            if ($error) {
                $register_err = "Error: " . $error['message'];
            } else {
                $register_err = "Something went wrong. Please try again later.";
            }
            
            // Log the error for debugging
            error_log("Registration failed for email: $email - " . json_encode($user));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Sign Up - DREAMS RENT</title>
    <?php include('assets/includes/header_link.php') ?>
</head>
<body>
    <div class="main-wrapper">
        <!-- Header -->
        <?php include('assets/includes/header.php') ?>
        <!-- /Header -->

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Sign Up</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sign Up</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Register Section -->
        <section class="register-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 col-md-10">
                        <div class="register-wrapper" data-aos="fade-up">
                            <div class="register-header">
                                <h2>Create an Account</h2>
                                <p>Join us to access exclusive offers and manage your bookings</p>
                            </div>
                            
                            <?php if (isset($register_err)) : ?>
                                <div class="alert alert-danger"><?php echo $register_err; ?></div>
                            <?php endif; ?>
                            
                            <form id="registerForm" class="register-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" id="firstName" name="firstName" placeholder="Enter your first name" value="<?php echo $first_name; ?>" required>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $first_name_err; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" id="lastName" name="lastName" placeholder="Enter your last name" value="<?php echo $last_name; ?>" required>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $last_name_err; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                                    </div>
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                        <input type="tel" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo $phone; ?>" required>
                                    </div>
                                    <div class="invalid-feedback"><?php echo $phone_err; ?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Create a password" required>
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                            <div class="password-strength mt-2" id="passwordStrength">
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <small class="text-muted">Password strength: <span id="strengthText">Poor</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback"><?php echo $confirm_password_err; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input <?php echo (!empty($terms_err)) ? 'is-invalid' : ''; ?>" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                                        </label>
                                        <div class="invalid-feedback"><?php echo $terms_err; ?></div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                                </div>
                                
                                <div class="register-or">
                                    <span class="or-line"></span>
                                    <span class="or-text">Or</span>
                                </div>
                                
                                <div class="social-register mb-4">
                                    <a href="#" class="btn btn-google w-100 mb-2">
                                        <i class="fab fa-google"></i> Sign up with Google
                                    </a>
                                    <a href="#" class="btn btn-facebook w-100">
                                        <i class="fab fa-facebook-f"></i> Sign up with Facebook
                                    </a>
                                </div>
                                
                                <div class="text-center already-have">
                                    Already have an account? <a href="login.php">Sign In</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Register Section -->

        <!-- Footer -->
        <?php include('assets/includes/footer.php') ?>
        <!-- /Footer -->
    </div>

    <?php include('assets/includes/footer_link.php') ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        
        if (toggleButtons) {
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Toggle eye icon
                    const eyeIcon = this.querySelector('i');
                    eyeIcon.classList.toggle('fa-eye');
                    eyeIcon.classList.toggle('fa-eye-slash');
                });
            });
        }
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthBar = document.querySelector('#passwordStrength .progress-bar');
        const strengthText = document.getElementById('strengthText');
        
        if (passwordInput && strengthBar && strengthText) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                
                // Check password length
                if (password.length >= 8) {
                    strength += 25;
                }
                
                // Check for mixed case
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
                    strength += 25;
                }
                
                // Check for numbers
                if (password.match(/\d/)) {
                    strength += 25;
                }
                
                // Check for special characters
                if (password.match(/[^a-zA-Z\d]/)) {
                    strength += 25;
                }
                
                // Update the strength bar
                strengthBar.style.width = strength + '%';
                
                // Update the text
                if (strength < 25) {
                    strengthText.textContent = 'Poor';
                    strengthBar.className = 'progress-bar bg-danger';
                } else if (strength < 50) {
                    strengthText.textContent = 'Weak';
                    strengthBar.className = 'progress-bar bg-warning';
                } else if (strength < 75) {
                    strengthText.textContent = 'Good';
                    strengthBar.className = 'progress-bar bg-info';
                } else {
                    strengthText.textContent = 'Strong';
                    strengthBar.className = 'progress-bar bg-success';
                }
            });
        }
    });
    </script>
</body>
</html>
