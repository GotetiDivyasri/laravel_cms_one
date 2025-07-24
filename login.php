<?php
session_start();
require_once 'config/database.php';

$page_title = 'Login - User Management System';
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
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Check user credentials
        $query = "SELECT id, username, email, password FROM users WHERE username = :username OR email = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Update last login
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = :id";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->bindParam(':id', $user['id']);
                $update_stmt->execute();
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Invalid username or password.';
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
                <i class="fas fa-sign-in-alt"></i>
                <h2>Welcome Back</h2>
                <p class="mb-0">Sign in to your account</p>
            </div>
            
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="needs-validation" novalidate>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username or Email" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">
                        <label for="username"><i class="fas fa-user me-2"></i>Username or Email</label>
                        <div class="invalid-feedback">
                            Please enter your username or email.
                        </div>
                    </div>
                    
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>
                    </div>
                    
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-auth">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p class="mb-2">Don't have an account? <a href="register.php">Create one here</a></p>
                <p class="mb-0"><a href="forgot-password.php">Forgot your password?</a></p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>