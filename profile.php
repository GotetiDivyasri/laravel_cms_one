<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'My Profile - User Management System';
$message = '';
$error = '';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get current user data
$user_query = "SELECT * FROM users WHERE id = :id";
$user_stmt = $db->prepare($user_query);
$user_stmt->bindParam(':id', $_SESSION['user_id']);
$user_stmt->execute();
$user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email)) {
        $error = 'Username and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!empty($new_password) && empty($current_password)) {
        $error = 'Current password is required to set a new password.';
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long.';
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        // Verify current password if new password is provided
        if (!empty($new_password)) {
            if (!password_verify($current_password, $user_data['password'])) {
                $error = 'Current password is incorrect.';
            }
        }
        
        if (empty($error)) {
            // Check if username or email already exists for other users
            $check_query = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':username', $username);
            $check_stmt->bindParam(':email', $email);
            $check_stmt->bindParam(':id', $_SESSION['user_id']);
            $check_stmt->execute();
            
            if ($check_stmt->rowCount() > 0) {
                $error = 'Username or email already exists.';
            } else {
                // Update user profile
                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->bindParam(':password', $hashed_password);
                } else {
                    $update_query = "UPDATE users SET username = :username, email = :email WHERE id = :id";
                    $update_stmt = $db->prepare($update_query);
                }
                
                $update_stmt->bindParam(':username', $username);
                $update_stmt->bindParam(':email', $email);
                $update_stmt->bindParam(':id', $_SESSION['user_id']);
                
                if ($update_stmt->execute()) {
                    // Update session data
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    
                    // Refresh user data
                    $user_stmt->execute();
                    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $message = 'Profile updated successfully!';
                } else {
                    $error = 'Failed to update profile. Please try again.';
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<main class="container my-5">
    <div id="alertContainer"></div>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-user-edit me-2"></i>My Profile
            </h1>
            <p class="text-muted">Manage your account information and settings</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <!-- Messages -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 120px; height: 120px; font-size: 3rem;">
                        <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                    </div>
                    <h4><?php echo htmlspecialchars($user_data['username']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <strong>Member Since</strong>
                            <p class="text-muted mb-0"><?php echo date('M Y', strtotime($user_data['created_at'])); ?></p>
                        </div>
                        <div class="col-6">
                            <strong>Last Login</strong>
                            <p class="text-muted mb-0">
                                <?php echo $user_data['last_login'] ? date('M j', strtotime($user_data['last_login'])) : 'Never'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Stats -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Account Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>User ID</span>
                        <strong>#<?php echo $user_data['id']; ?></strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Account Status</span>
                        <span class="status-badge status-active">Active</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Member Since</span>
                        <strong><?php echo date('M j, Y', strtotime($user_data['created_at'])); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Profile Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Basic Information
                                </h6>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="username" name="username" 
                                           placeholder="Username" required 
                                           value="<?php echo htmlspecialchars($user_data['username']); ?>">
                                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                    <div class="invalid-feedback">Please enter a username.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" 
                                           placeholder="Email Address" required 
                                           value="<?php echo htmlspecialchars($user_data['email']); ?>">
                                    <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Password Change -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-lock me-2"></i>Change Password
                                    <small class="text-muted">(Leave blank to keep current password)</small>
                                </h6>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="current_password" name="current_password" 
                                           placeholder="Current Password">
                                    <label for="current_password"><i class="fas fa-key me-2"></i>Current Password</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           placeholder="New Password" minlength="6">
                                    <label for="new_password"><i class="fas fa-lock me-2"></i>New Password</label>
                                    <div class="invalid-feedback">Password must be at least 6 characters long.</div>
                                    <div id="passwordStrength"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Confirm New Password" minlength="6">
                                    <label for="confirm_password"><i class="fas fa-lock me-2"></i>Confirm New Password</label>
                                    <div class="invalid-feedback">Please confirm your new password.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize password strength checker
    showPasswordStrength('new_password', 'passwordStrength');
    
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const currentPassword = document.getElementById('current_password');
    
    // Require current password when new password is entered
    newPassword.addEventListener('input', function() {
        if (this.value) {
            currentPassword.setAttribute('required', 'required');
            confirmPassword.setAttribute('required', 'required');
        } else {
            currentPassword.removeAttribute('required');
            confirmPassword.removeAttribute('required');
        }
    });
    
    // Confirm password validation
    confirmPassword.addEventListener('input', function() {
        if (this.value !== newPassword.value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>