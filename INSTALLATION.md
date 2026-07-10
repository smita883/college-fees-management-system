# College Fee Management System - Installation Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for future dependency management)

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/smita883/college-fees-management-system.git
cd college-fees-management-system
```

### 2. Create Database

```sql
CREATE DATABASE college_fee_management;
USE college_fee_management;
```

### 3. Import Database Schema

```bash
mysql -u root -p college_fee_management < database/schema.sql
```

### 4. Configure Database Connection

Edit `app/config/database.php` with your database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'college_fee_management');
```

### 5. Configure Application Settings

Edit `app/config/constants.php` with your application settings:

```php
define('APP_NAME', 'College Fee Management System');
define('APP_URL', 'http://localhost/college-fees-management-system');
define('DEBUG_MODE', true);
```

### 6. Set Permissions

```bash
chmod -R 755 .
chmod -R 777 logs/
chmod -R 777 uploads/
```

### 7. Access the Application

- Open your browser and navigate to: `http://localhost/college-fees-management-system/public/login.php`
- Default login credentials will be provided after initial setup

## Initial Setup

### Create Default Admin User

Run the setup script:

```bash
php database/setup.php
```

This will create:
- Default admin account
- Sample departments
- Sample courses

## Directory Structure

```
college-fees-management-system/
├── app/
│   ├── config/          # Configuration files
│   ├── controllers/     # Application controllers
│   ├── helpers/         # Helper classes
│   ├── middleware/      # Middleware classes
│   ├── api/             # API endpoints
│   └── views/           # View templates
├── public/              # Public files (login, dashboard)
├── database/            # Database files and migrations
├── logs/                # Application logs
├── uploads/             # User uploaded files
├── .htaccess            # Apache rewrite rules
└── README.md
```

## Features

### Core Features
- **User Management**: Admin, Manager, Accounts Staff, Faculty
- **Student Management**: Add, update, delete student records
- **Course Management**: Manage courses and departments
- **Fee Structure**: Define fees for courses and departments
- **Fee Collection**: Record student fee payments
- **Receipt Generation**: Generate and manage receipts
- **Activity Logging**: Track all system activities
- **Reports**: Generate various fee collection reports

### Security Features
- Password hashing with bcrypt
- CSRF token validation
- SQL injection prevention with prepared statements
- XSS prevention with output escaping
- Session-based authentication
- Role-based access control

## User Roles

- **Admin**: Full system access
- **Manager**: Can manage students, courses, departments
- **Accounts**: Can process payments and generate receipts
- **Faculty**: Can view student information only
- **Staff**: General staff with limited access

## API Endpoints

### Student API
- `GET /app/api/student_api.php?action=list` - Get all students
- `POST /app/api/student_api.php?action=add` - Add new student
- `POST /app/api/student_api.php?action=update` - Update student
- `POST /app/api/student_api.php?action=delete` - Delete student

### Payment API
- `GET /app/api/payment_api.php?action=list` - Get all payments
- `POST /app/api/payment_api.php?action=record` - Record new payment
- `GET /app/api/payment_api.php?action=summary` - Get payment summary
- `GET /app/api/payment_api.php?action=history&student_id=X` - Get student payment history

### Receipt API
- `GET /app/api/receipt_api.php?action=list` - Get all receipts
- `POST /app/api/receipt_api.php?action=issue` - Issue new receipt
- `GET /app/api/receipt_api.php?action=get_by_number&number=X` - Get receipt by number
- `POST /app/api/receipt_api.php?action=cancel` - Cancel receipt

## Troubleshooting

### Database Connection Error
- Check MySQL credentials in `app/config/database.php`
- Ensure MySQL server is running
- Verify database name is correct

### Permission Issues
- Ensure write permissions for `logs/` and `uploads/` directories
- Run: `chmod -R 777 logs/ uploads/`

### Session Issues
- Check PHP session configuration
- Ensure `session.save_path` is writable

### Login Issues
- Clear browser cache and cookies
- Verify database migrations were successful
- Check activity logs for error details

## Support

For support and issues, please contact the development team or check the GitHub repository issues page.

## License

This project is proprietary and confidential.
