<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Manage Users - User Management System';
$action = $_GET['action'] ?? 'list';
$user_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($action == 'add' || $action == 'edit') {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($username) || empty($email)) {
            $error = 'Username and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif ($action == 'add' && empty($password)) {
            $error = 'Password is required for new users.';
        } elseif (!empty($password) && strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } else {
            if ($action == 'add') {
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
                        $message = 'User added successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to add user. Please try again.';
                    }
                }
            } elseif ($action == 'edit' && $user_id) {
                // Check if username or email already exists for other users
                $check_query = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id";
                $check_stmt = $db->prepare($check_query);
                $check_stmt->bindParam(':username', $username);
                $check_stmt->bindParam(':email', $email);
                $check_stmt->bindParam(':id', $user_id);
                $check_stmt->execute();
                
                if ($check_stmt->rowCount() > 0) {
                    $error = 'Username or email already exists.';
                } else {
                    // Update user
                    if (!empty($password)) {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $update_query = "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id";
                        $update_stmt = $db->prepare($update_query);
                        $update_stmt->bindParam(':password', $hashed_password);
                    } else {
                        $update_query = "UPDATE users SET username = :username, email = :email WHERE id = :id";
                        $update_stmt = $db->prepare($update_query);
                    }
                    
                    $update_stmt->bindParam(':username', $username);
                    $update_stmt->bindParam(':email', $email);
                    $update_stmt->bindParam(':id', $user_id);
                    
                    if ($update_stmt->execute()) {
                        $message = 'User updated successfully!';
                        $action = 'list';
                    } else {
                        $error = 'Failed to update user. Please try again.';
                    }
                }
            }
        }
    }
}

// Handle delete action
if ($action == 'delete' && $user_id) {
    if ($user_id == $_SESSION['user_id']) {
        $error = 'You cannot delete your own account.';
    } else {
        $delete_query = "DELETE FROM users WHERE id = :id";
        $delete_stmt = $db->prepare($delete_query);
        $delete_stmt->bindParam(':id', $user_id);
        
        if ($delete_stmt->execute()) {
            $message = 'User deleted successfully!';
        } else {
            $error = 'Failed to delete user. Please try again.';
        }
    }
    $action = 'list';
}

// Get user data for edit/view
$user_data = null;
if (($action == 'edit' || $action == 'view') && $user_id) {
    $user_query = "SELECT * FROM users WHERE id = :id";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->bindParam(':id', $user_id);
    $user_stmt->execute();
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user_data) {
        $error = 'User not found.';
        $action = 'list';
    }
}

// Get all users for list view
$users = [];
if ($action == 'list') {
    $users_query = "SELECT * FROM users ORDER BY created_at DESC";
    $users_stmt = $db->prepare($users_query);
    $users_stmt->execute();
    $users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>

<main class="container my-5">
    <div id="alertContainer"></div>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2">
                <i class="fas fa-users me-2"></i>
                <?php
                switch ($action) {
                    case 'add':
                        echo 'Add New User';
                        break;
                    case 'edit':
                        echo 'Edit User';
                        break;
                    case 'view':
                        echo 'View User';
                        break;
                    default:
                        echo 'Manage Users';
                }
                ?>
            </h1>
            <p class="text-muted">Manage your user accounts and permissions</p>
        </div>
        <div class="col-md-4 text-md-end">
            <?php if ($action == 'list'): ?>
                <a href="?action=add" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </a>
            <?php else: ?>
                <a href="users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            <?php endif; ?>
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
    
    <?php if ($action == 'list'): ?>
        <!-- Users List -->
        <div class="table-container">
            <div class="table-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-list me-2"></i>All Users</h4>
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search users...">
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No users found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong>#<?php echo $user['id']; ?></strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 35px; height: 35px; font-size: 14px;">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if ($user['last_login']): ?>
                                            <small class="text-success">
                                                <?php echo date('M j, Y g:i A', strtotime($user['last_login'])); ?>
                                            </small>
                                        <?php else: ?>
                                            <small class="text-muted">Never</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['last_login'] && strtotime($user['last_login']) > strtotime('-30 days')): ?>
                                            <span class="status-badge status-active">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="?action=view&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-info btn-action" 
                                               data-bs-toggle="tooltip" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?action=edit&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-warning btn-action"
                                               data-bs-toggle="tooltip" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-action btn-delete"
                                                   data-name="<?php echo htmlspecialchars($user['username']); ?>"
                                                   data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    <?php elseif ($action == 'add' || $action == 'edit'): ?>
        <!-- Add/Edit User Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-<?php echo $action == 'add' ? 'user-plus' : 'user-edit'; ?> me-2"></i>
                            <?php echo $action == 'add' ? 'Add New User' : 'Edit User'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" name="username" 
                                               placeholder="Username" required 
                                               value="<?php echo $user_data ? htmlspecialchars($user_data['username']) : ''; ?>">
                                        <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                                        <div class="invalid-feedback">Please enter a username.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control" id="email" name="email" 
                                               placeholder="Email Address" required 
                                               value="<?php echo $user_data ? htmlspecialchars($user_data['email']) : ''; ?>">
                                        <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                        <div class="invalid-feedback">Please enter a valid email address.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Password" <?php echo $action == 'add' ? 'required' : ''; ?> minlength="6">
                                <label for="password">
                                    <i class="fas fa-lock me-2"></i>Password 
                                    <?php echo $action == 'edit' ? '(leave blank to keep current)' : ''; ?>
                                </label>
                                <div class="invalid-feedback">
                                    Password must be at least 6 characters long.
                                </div>
                                <?php if ($action == 'add'): ?>
                                    <div id="passwordStrength"></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <a href="users.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?php echo $action == 'add' ? 'Add User' : 'Update User'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    <?php elseif ($action == 'view' && $user_data): ?>
        <!-- View User -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>User Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width: 100px; height: 100px; font-size: 2rem;">
                                    <?php echo strtoupper(substr($user_data['username'], 0, 1)); ?>
                                </div>
                                <h4><?php echo htmlspecialchars($user_data['username']); ?></h4>
                                <p class="text-muted"><?php echo htmlspecialchars($user_data['email']); ?></p>
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">User ID:</th>
                                        <td>#<?php echo $user_data['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Username:</th>
                                        <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Member Since:</th>
                                        <td><?php echo date('F j, Y g:i A', strtotime($user_data['created_at'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Login:</th>
                                        <td>
                                            <?php if ($user_data['last_login']): ?>
                                                <?php echo date('F j, Y g:i A', strtotime($user_data['last_login'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never logged in</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            <?php if ($user_data['last_login'] && strtotime($user_data['last_login']) > strtotime('-30 days')): ?>
                                                <span class="status-badge status-active">Active</span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="users.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                            <div>
                                <a href="?action=edit&id=<?php echo $user_data['id']; ?>" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit User
                                </a>
                                <?php if ($user_data['id'] != $_SESSION['user_id']): ?>
                                    <a href="?action=delete&id=<?php echo $user_data['id']; ?>" 
                                       class="btn btn-danger btn-delete ms-2"
                                       data-name="<?php echo htmlspecialchars($user_data['username']); ?>">
                                        <i class="fas fa-trash me-2"></i>Delete User
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>

<script>
    // Initialize password strength checker for add form
    <?php if ($action == 'add'): ?>
    document.addEventListener('DOMContentLoaded', function() {
        showPasswordStrength('password', 'passwordStrength');
    });
    <?php endif; ?>
</script>