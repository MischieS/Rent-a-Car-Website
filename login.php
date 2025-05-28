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
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Check for success message from registration
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is empty
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
        try {
            // Get database connection
            $database = new Database();
            $db = $database->getConnection();
            
            // Create user object
            $user = new User($db);
            $user->email = $email;
            $user->password = $password;
            
            // Attempt to login
            if ($user->login()) {
                // Set session variables
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->first_name . ' ' . $user->last_name;
                $_SESSION['user_email'] = $user->email;
                $_SESSION['user_role'] = $user->role;
                
                // Remember me functionality
                if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + 30 * 24 * 60 * 60, '/');
                    
                    // Store token in database
                    $user->storeRememberToken($token);
                }
                
                // Redirect to home page
                header("Location: index.php");
                exit();
            } else {
                // Login failed
                $login_err = "Invalid email or password.";
            }
        } catch (Exception $e) {
            $login_err = "An error occurred. Please try again later.";
            // Log the error
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <title>Sign In - DREAMS RENT</title>
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
                        <h1>Sign In</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sign In</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        <!-- Login Section -->
        <section class="login-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-8">
                        <div class="login-wrapper" data-aos="fade-up">
                            <div class="login-header">
                                <h2>Welcome Back</h2>
                                <p>Sign in to your account to continue</p>
                            </div>
                            
                            <?php if (!empty($login_err)) : ?>
                                <div class="alert alert-danger"><?php echo $login_err; ?></div>
                            <?php endif; ?>
                            
                            <?php if (isset($success_msg)) : ?>
                                <div class="alert alert-success"><?php echo $success_msg; ?></div>
                            <?php endif; ?>
                            
                            <form id="loginForm" class="login-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Enter your email" value="<?php echo $email; ?>" required>
                                    </div>
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                        <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="password" name="password" placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback"><?php echo $password_err; ?></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="#" class="forgot-link">Forgot Password?</a>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                </div>
                                
                                <div class="login-or">
                                    <span class="or-line"></span>
                                    <span class="or-text">Or</span>
                                </div>
                                
                                <div class="social-login mb-4">
                                    <a href="#" class="btn btn-google w-100 mb-2">
                                        <i class="fab fa-google"></i> Sign in with Google
                                    </a>
                                    <a href="#" class="btn btn-facebook w-100">
                                        <i class="fab fa-facebook-f"></i> Sign in with Facebook
                                    </a>
                                </div>
                                
                                <div class="text-center dont-have">
                                    Don't have an account? <a href="register.php">Sign Up</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Login Section -->

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
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');
        
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle eye icon
                const eyeIcon = this.querySelector('i');
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }
    });
    </script>
</body>
</html>
