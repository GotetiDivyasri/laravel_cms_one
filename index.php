<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$page_title = 'Welcome - User Management System';
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
    <!-- Hero Section -->
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-lg-6 text-center text-white">
                    <div class="fade-in">
                        <i class="fas fa-users fa-5x mb-4 opacity-75"></i>
                        <h1 class="display-4 fw-bold mb-4">User Management System</h1>
                        <p class="lead mb-5">A complete PHP-based solution for managing users with a beautiful Bootstrap interface. Secure, responsive, and feature-rich.</p>
                        
                        <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                            <a href="login.php" class="btn btn-light btn-lg px-5 py-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </a>
                            <a href="register.php" class="btn btn-outline-light btn-lg px-5 py-3">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="auth-card slide-in" style="animation-delay: 0.3s;">
                        <div class="auth-header">
                            <i class="fas fa-rocket"></i>
                            <h2>Features</h2>
                        </div>
                        <div class="auth-body">
                            <div class="row g-4">
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                                        <h6>Secure Authentication</h6>
                                        <small class="text-muted">Password hashing & validation</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-mobile-alt fa-2x text-success mb-2"></i>
                                        <h6>Responsive Design</h6>
                                        <small class="text-muted">Works on all devices</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-database fa-2x text-info mb-2"></i>
                                        <h6>CRUD Operations</h6>
                                        <small class="text-muted">Complete user management</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <i class="fas fa-chart-bar fa-2x text-warning mb-2"></i>
                                        <h6>Dashboard Analytics</h6>
                                        <small class="text-muted">User statistics & insights</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="auth-footer">
                            <p class="mb-0">Ready to get started? <a href="register.php">Create your account</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-6 fw-bold">Why Choose Our System?</h2>
                    <p class="lead text-muted">Built with modern technologies and best practices</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-code fa-3x text-primary mb-3"></i>
                            <h5>Clean Code</h5>
                            <p class="text-muted">Well-structured PHP code following best practices and security standards.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-palette fa-3x text-success mb-3"></i>
                            <h5>Modern UI/UX</h5>
                            <p class="text-muted">Beautiful Bootstrap 5 interface with custom styling and smooth animations.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-lock fa-3x text-danger mb-3"></i>
                            <h5>Security First</h5>
                            <p class="text-muted">Secure password hashing, SQL injection prevention, and session management.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-users me-2"></i>User Management System</h5>
                    <p class="mb-0">A complete PHP-based user management solution with Bootstrap design.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>