<?php
session_start();
require_once 'config/database.php';

$page_title = 'Register - User Management System';
$message = '';
$error = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if username or email already exists
        $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Username or email already exists.';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->bindParam(':username', $username);
            $insert_stmt->bindParam(':email', $email);
            $insert_stmt->bindParam(':password', $hashed_password);
            
            if ($insert_stmt->execute()) {
                $message = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card fade-in">
            <div class="auth-header">
                <i class="fas fa-user-plus"></i>
                <h2>Create Account</h2>
                <p class="mb-0">Join our community today</p>
            </div>
            
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($message): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                        <div class="invalid-feedback">
                            Please choose a username.
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Email Address" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                        <div class="invalid-feedback">
                            Please provide a valid email address.
                        </div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required minlength="6">
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                        <div class="invalid-feedback">
                            Password must be at least 6 characters long.
                        </div>
                        <div id="passwordStrength"></div>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm Password" required minlength="6">
                        <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm Password</label>
                        <div class="invalid-feedback">
                            Please confirm your password.
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-0">Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <script>
        // Initialize password strength checker
        document.addEventListener('DOMContentLoaded', function() {
            showPasswordStrength('password', 'passwordStrength');
            
            // Confirm password validation
            const confirmPassword = document.getElementById('confirm_password');
            const password = document.getElementById('password');
            
            confirmPassword.addEventListener('input', function() {
                if (this.value !== password.value) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>