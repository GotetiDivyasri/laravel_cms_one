<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Dashboard - User Management System';

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Get user statistics
$stats = [
    'total_users' => 0,
    'active_users' => 0,
    'new_users_today' => 0,
    'new_users_this_month' => 0
];

try {
    // Total users
    $query = "SELECT COUNT(*) as count FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Active users (logged in within last 30 days)
    $query = "SELECT COUNT(*) as count FROM users WHERE last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['active_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // New users today
    $query = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['new_users_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // New users this month
    $query = "SELECT COUNT(*) as count FROM users WHERE YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW())";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['new_users_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
} catch (Exception $e) {
    // Handle error silently for demo
}

// Get recent users
$recent_users = [];
try {
    $query = "SELECT id, username, email, created_at, last_login FROM users ORDER BY created_at DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle error silently for demo
}

include 'includes/header.php';
?>

<main class="container my-5">
    <div id="alertContainer"></div>
    
    <!-- Welcome Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-primary text-white rounded-4 p-4 fade-in">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="h2 mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                        <p class="mb-0 opacity-75">Here's what's happening with your user management system today.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <i class="fas fa-tachometer-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card slide-in">
                <div class="card-body text-center">
                    <div class="card-icon bg-primary mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="card-title text-primary"><?php echo number_format($stats['total_users']); ?></h3>
                    <p class="card-text">Total Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card slide-in" style="animation-delay: 0.1s;">
                <div class="card-body text-center">
                    <div class="card-icon bg-success mx-auto">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3 class="card-title text-success"><?php echo number_format($stats['active_users']); ?></h3>
                    <p class="card-text">Active Users</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card slide-in" style="animation-delay: 0.2s;">
                <div class="card-body text-center">
                    <div class="card-icon bg-info mx-auto">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3 class="card-title text-info"><?php echo number_format($stats['new_users_today']); ?></h3>
                    <p class="card-text">New Today</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card dashboard-card slide-in" style="animation-delay: 0.3s;">
                <div class="card-body text-center">
                    <div class="card-icon bg-warning mx-auto">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="card-title text-warning"><?php echo number_format($stats['new_users_this_month']); ?></h3>
                    <p class="card-text">This Month</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h4 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="users.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="users.php?action=add" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="profile.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-user-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="settings.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-cog me-2"></i>Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h4><i class="fas fa-clock me-2"></i>Recent Users</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Joined</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_users)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No users found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_users as $user): ?>
                                    <tr>
                                        <td><strong>#<?php echo $user['id']; ?></strong></td>
                                        <td>
                                            <i class="fas fa-user me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <span class="status-badge status-active">
                                                    <?php echo date('M j, Y', strtotime($user['last_login'])); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="users.php?action=view&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary btn-action">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-outline-warning btn-action">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 text-center">
                    <a href="users.php" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>View All Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>