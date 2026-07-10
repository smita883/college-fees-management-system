# College Fee Management System (CFMS)

## Overview
A secure web-based fee management system for college staff. This system allows authorized college personnel to manage student records, fee structures, fee collection, receipts, and generate comprehensive reports.

## Features
- **Student Management**: Add, update, delete student records
- **Course Management**: Manage course information and assignments
- **Department Management**: Organize departments and staff
- **Fee Structure**: Define and manage fee categories and amounts
- **Fee Collection**: Track fee payments from students
- **Receipt Management**: Generate and manage payment receipts
- **Reports**: Comprehensive financial and student reports
- **User Management**: Manage staff accounts and permissions
- **Role-Based Access Control**: Admin, Accounts, Receptionist, Manager roles
- **Security**: Password hashing, CSRF/XSS protection, input validation
- **Backup & Restore**: System backup and restoration capabilities

## Technology Stack
- **Backend**: PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Hosting**: Hostinger Business Web Hosting

## Project Deadline
3 Days

## Installation & Deployment
See `docs/INSTALLATION_GUIDE.md` for detailed setup instructions.

## Directory Structure
```
CFMS/
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── assets/
├── app/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── middleware/
│   └── helpers/
├── database/
│   ├── schema.sql
│   └── seeders/
├── docs/
├── tests/
└── vendor/
```

## Quick Start
1. Extract project files
2. Create MySQL database
3. Import `database/schema.sql`
4. Configure `app/config/config.php`
5. Set up admin credentials via installation script
6. Access system at `http://your-domain.com`

## Default Admin Credentials
- **Username**: admin
- **Password**: Generated during setup

## Security Features
- Passwords hashed with bcrypt
- Prepared statements for SQL injection prevention
- CSRF token validation
- XSS protection with output escaping
- Input validation and sanitization
- Secure session management
- Activity logging

## Support
For issues or questions, contact the development team.
