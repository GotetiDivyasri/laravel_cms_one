-- User Management System Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS user_management;
USE user_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at),
    INDEX idx_last_login (last_login)
);

-- Insert sample admin user (password: admin123)
INSERT INTO users (username, email, password, created_at) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW());

-- Insert sample users for demonstration
INSERT INTO users (username, email, password, created_at, last_login) VALUES 
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-15 10:30:00', '2024-01-20 14:25:00'),
('jane_smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-10 09:15:00', '2024-01-19 16:45:00'),
('mike_wilson', 'mike@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-08 11:20:00', NULL),
('sarah_johnson', 'sarah@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-12 13:45:00', '2024-01-18 10:30:00');

-- Optional: User roles table (for future expansion)
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: User role assignments (for future expansion)
CREATE TABLE IF NOT EXISTS user_role_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES user_roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
);

-- Insert default roles
INSERT INTO user_roles (role_name, description) VALUES 
('admin', 'System administrator with full access'),
('user', 'Regular user with limited access'),
('moderator', 'User with moderation privileges');

-- Optional: User sessions table (for session management)
CREATE TABLE IF NOT EXISTS user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
);

-- Optional: User activity log (for audit trail)
CREATE TABLE IF NOT EXISTS user_activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Optional: Password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
);

-- Create views for common queries
CREATE VIEW active_users AS
SELECT id, username, email, created_at, last_login
FROM users 
WHERE status = 'active' AND last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY);

CREATE VIEW user_statistics AS
SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN last_login >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as active_users,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as new_users_today,
    COUNT(CASE WHEN YEAR(created_at) = YEAR(NOW()) AND MONTH(created_at) = MONTH(NOW()) THEN 1 END) as new_users_this_month
FROM users;