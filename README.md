# College Fee Management System

## Overview

A comprehensive PHP-based web application for managing college student fee payments, receipts, and related financial records.

## Features

### Core Functionality
- **Student Management**: Maintain comprehensive student records including personal and academic information
- **Course & Department Management**: Organize academic structure and department hierarchy
- **Fee Structure Management**: Define and manage fee structures for different courses and departments
- **Fee Collection**: Record and track student fee payments with multiple payment methods
- **Receipt Management**: Generate, issue, and manage receipt records
- **User Management**: Multi-role user system with role-based access control
- **Activity Logging**: Comprehensive audit trail of all system activities
- **Reporting**: Generate detailed reports on fee collection and student information

### Security Features
- Bcrypt password hashing
- CSRF token validation
- SQL injection prevention
- XSS protection
- Session-based authentication
- Role-based access control

### User Roles
1. **Admin**: Full system access and configuration
2. **Manager**: Manage students, courses, and departments
3. **Accounts**: Process payments and generate receipts
4. **Faculty**: View student information
5. **Staff**: Limited access to basic information

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Server**: Apache/Nginx

## Project Structure

```
college-fees-management-system/
├── app/
│   ├── config/              # Configuration and constants
│   ├── controllers/         # Business logic controllers
│   ├── helpers/             # Helper classes and functions
│   ├── middleware/          # Request middleware
│   ├── api/                 # RESTful API endpoints
│   └── views/               # View templates
├── public/                  # Public-facing files
├── database/                # Database schema and migrations
├── logs/                    # Application logs
├── uploads/                 # User uploads
└── docs/                    # Documentation
```

## Quick Start

1. **Clone Repository**
   ```bash
   git clone https://github.com/smita883/college-fees-management-system.git
   cd college-fees-management-system
   ```

2. **Setup Database**
   ```bash
   mysql -u root -p college_fee_management < database/schema.sql
   ```

3. **Configure Database**
   - Edit `app/config/database.php` with your credentials

4. **Set Permissions**
   ```bash
   chmod -R 755 .
   chmod -R 777 logs/ uploads/
   ```

5. **Access Application**
   - Open browser: `http://localhost/college-fees-management-system/public/login.php`

## API Documentation

### Student API
```
GET    /app/api/student_api.php?action=list              # List all students
GET    /app/api/student_api.php?action=get&id=X          # Get student by ID
POST   /app/api/student_api.php?action=add               # Add new student
POST   /app/api/student_api.php?action=update            # Update student
POST   /app/api/student_api.php?action=delete            # Delete student
```

### Payment API
```
GET    /app/api/payment_api.php?action=list              # List all payments
GET    /app/api/payment_api.php?action=summary           # Payment summary
POST   /app/api/payment_api.php?action=record            # Record payment
GET    /app/api/payment_api.php?action=history           # Student payment history
```

### Receipt API
```
GET    /app/api/receipt_api.php?action=list              # List receipts
GET    /app/api/receipt_api.php?action=get&id=X          # Get receipt by ID
POST   /app/api/receipt_api.php?action=issue             # Issue receipt
POST   /app/api/receipt_api.php?action=cancel            # Cancel receipt
```

## Configuration

### Database Configuration
Edit `app/config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'college_fee_management');
```

### Application Settings
Edit `app/config/constants.php`:
```php
define('APP_NAME', 'College Fee Management System');
define('APP_URL', 'http://localhost/college-fees-management-system');
define('DEBUG_MODE', true);
define('SESSION_TIMEOUT', 3600);
```

## Default Credentials

After setup, login with:
- **Username**: admin
- **Password**: (provided by setup script)

Change password immediately after first login.

## Logs

Application logs are stored in `logs/` directory.
- **Error logs**: `logs/error.log`
- **Activity logs**: Stored in database `activity_logs` table

## Support

For issues and support, please contact the development team or create an issue in the repository.

## License

Proprietary - All rights reserved

## Author

Developed by: Smita (smita883@github.com)
