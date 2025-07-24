# User Management System

A complete PHP-based user management system with a modern Bootstrap 5 interface. This system provides secure user authentication, CRUD operations, and a responsive dashboard with beautiful design.

## Features

### 🔐 Authentication System
- **Secure Registration** - User registration with password validation
- **Login/Logout** - Secure session-based authentication
- **Password Hashing** - BCrypt password encryption
- **Session Management** - Secure session handling

### 👥 User Management (CRUD)
- **Create Users** - Add new users with validation
- **Read Users** - View user lists and individual profiles
- **Update Users** - Edit user information and passwords
- **Delete Users** - Remove users with confirmation dialogs

### 📊 Dashboard & Analytics
- **User Statistics** - Total, active, and new user counts
- **Recent Users** - Display of recently registered users
- **Quick Actions** - Easy access to common functions
- **Responsive Cards** - Beautiful statistical displays

### 🎨 Modern UI/UX
- **Bootstrap 5** - Latest Bootstrap framework
- **Responsive Design** - Works on all devices
- **Font Awesome Icons** - Beautiful iconography
- **Custom Animations** - Smooth transitions and effects
- **Professional Styling** - Modern gradient designs

### 🛡️ Security Features
- **SQL Injection Prevention** - Prepared statements
- **XSS Protection** - Input sanitization
- **CSRF Protection** - Form validation
- **Password Strength** - Real-time password validation
- **Session Security** - Secure session management

## File Structure

```
user-management-system/
├── assets/
│   ├── css/
│   │   └── style.css          # Custom CSS styles
│   └── js/
│       └── script.js          # Custom JavaScript
├── config/
│   └── database.php           # Database configuration
├── includes/
│   ├── header.php             # Common header
│   └── footer.php             # Common footer
├── dashboard.php              # Main dashboard
├── users.php                  # User management (CRUD)
├── login.php                  # Login page
├── register.php               # Registration page
├── logout.php                 # Logout handler
├── profile.php                # User profile management
├── index.php                  # Landing page
├── database.sql               # Database schema
└── README.md                  # This file
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PDO PHP extension

### Setup Instructions

1. **Clone or Download**
   ```bash
   git clone <repository-url>
   cd user-management-system
   ```

2. **Database Setup**
   - Create a MySQL database named `user_management`
   - Import the `database.sql` file:
   ```sql
   mysql -u username -p user_management < database.sql
   ```

3. **Configure Database**
   - Edit `config/database.php`
   - Update database credentials:
   ```php
   private $host = 'localhost';
   private $db_name = 'user_management';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Web Server Setup**
   - Place files in your web server directory
   - Ensure proper permissions for PHP files
   - Access via browser: `http://localhost/user-management-system/`

5. **Default Login**
   - Username: `admin`
   - Password: `admin123`

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);
```

## Usage

### User Registration
1. Navigate to the registration page
2. Fill in username, email, and password
3. Password strength is validated in real-time
4. Account is created and ready for login

### User Login
1. Use username/email and password
2. Session is created upon successful authentication
3. Redirected to dashboard

### Dashboard Features
- View user statistics
- Quick action buttons
- Recent users table
- Responsive design

### User Management
- **List Users**: View all users with search functionality
- **Add User**: Create new user accounts
- **Edit User**: Modify user information
- **View User**: See detailed user information
- **Delete User**: Remove users with confirmation

### Profile Management
- Edit personal information
- Change password with current password verification
- View account statistics

## Customization

### Styling
- Modify `assets/css/style.css` for custom styles
- CSS variables available for easy theme changes
- Bootstrap classes can be overridden

### Functionality
- Add new fields to user table and forms
- Implement role-based access control
- Add email verification features
- Integrate with external APIs

### Security Enhancements
- Implement two-factor authentication
- Add password reset functionality
- Enable account lockout after failed attempts
- Add audit logging

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Technologies Used

- **Backend**: PHP 7.4+, MySQL, PDO
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3.0
- **Icons**: Font Awesome 6.0.0
- **Security**: BCrypt, Prepared Statements

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support, please create an issue in the repository or contact the development team.

## Changelog

### Version 1.0.0
- Initial release
- Complete authentication system
- User CRUD operations
- Responsive dashboard
- Modern Bootstrap 5 design
- Security implementations

---

**Note**: This system is designed for educational and development purposes. For production use, additional security measures and testing are recommended.