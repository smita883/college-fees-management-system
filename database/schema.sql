-- College Fee Management System - Database Schema

CREATE DATABASE IF NOT EXISTS college_fee_management;
USE college_fee_management;

-- Departments Table
CREATE TABLE IF NOT EXISTS departments (
    department_id INT PRIMARY KEY AUTO_INCREMENT,
    department_name VARCHAR(100) NOT NULL UNIQUE,
    department_code VARCHAR(20) UNIQUE,
    head_of_department VARCHAR(100),
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_name VARCHAR(100) NOT NULL,
    course_code VARCHAR(20) UNIQUE,
    department_id INT NOT NULL,
    duration_years INT,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id)
);

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('Admin', 'Manager', 'Accounts', 'Faculty', 'Staff') DEFAULT 'Staff',
    department_id INT,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    INDEX idx_username (username),
    INDEX idx_role (role)
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY AUTO_INCREMENT,
    roll_number VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    course_id INT NOT NULL,
    department_id INT NOT NULL,
    admission_date DATE,
    academic_year VARCHAR(10),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    INDEX idx_roll_number (roll_number),
    INDEX idx_course_id (course_id),
    INDEX idx_academic_year (academic_year)
);

-- Fee Structure Table
CREATE TABLE IF NOT EXISTS fee_structure (
    fee_id INT PRIMARY KEY AUTO_INCREMENT,
    fee_name VARCHAR(100) NOT NULL,
    course_id INT,
    department_id INT,
    academic_year VARCHAR(10),
    amount DECIMAL(10, 2) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    INDEX idx_academic_year (academic_year),
    INDEX idx_course_id (course_id)
);

-- Fee Payments Table
CREATE TABLE IF NOT EXISTS fee_payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    fee_id INT NOT NULL,
    amount_paid DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('Cash', 'Cheque', 'Online', 'DD') DEFAULT 'Cash',
    transaction_id VARCHAR(100),
    reference_number VARCHAR(100),
    paid_by VARCHAR(100),
    payment_status ENUM('Pending', 'Completed', 'Failed', 'Refunded') DEFAULT 'Completed',
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (fee_id) REFERENCES fee_structure(fee_id),
    FOREIGN KEY (created_by) REFERENCES users(user_id),
    INDEX idx_student_id (student_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_payment_status (payment_status)
);

-- Receipts Table
CREATE TABLE IF NOT EXISTS receipts (
    receipt_id INT PRIMARY KEY AUTO_INCREMENT,
    receipt_number VARCHAR(50) NOT NULL UNIQUE,
    payment_id INT NOT NULL,
    student_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    receipt_date DATE NOT NULL,
    issued_by INT,
    receipt_status ENUM('Issued', 'Cancelled', 'Duplicate') DEFAULT 'Issued',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES fee_payments(payment_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (issued_by) REFERENCES users(user_id),
    INDEX idx_receipt_number (receipt_number),
    INDEX idx_receipt_date (receipt_date),
    INDEX idx_student_id (student_id)
);

-- Activity Logs Table
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50),
    record_id INT,
    description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    status ENUM('Success', 'Failed', 'Warning') DEFAULT 'Success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
);

-- Session Management Table
CREATE TABLE IF NOT EXISTS sessions (
    session_id VARCHAR(128) PRIMARY KEY,
    user_id INT,
    session_data LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- System Settings Table
CREATE TABLE IF NOT EXISTS system_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create Indexes for better performance
CREATE INDEX idx_students_course ON students(course_id);
CREATE INDEX idx_students_department ON students(department_id);
CREATE INDEX idx_payments_student ON fee_payments(student_id);
CREATE INDEX idx_payments_fee ON fee_payments(fee_id);
CREATE INDEX idx_receipts_payment ON receipts(payment_id);
CREATE INDEX idx_logs_user ON activity_logs(user_id);

-- Insert Default Admin User (password: Admin@123)
INSERT INTO users (username, password_hash, first_name, last_name, email, role, is_active) 
VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36XQZ3H.m', 'System', 'Administrator', 'admin@cfms.local', 'Admin', 1);

-- Insert Sample Departments
INSERT INTO departments (department_name, department_code, description) VALUES
('Computer Science', 'CS', 'Department of Computer Science and Engineering'),
('Electronics', 'EC', 'Department of Electronics and Communication'),
('Mechanical', 'ME', 'Department of Mechanical Engineering'),
('Civil', 'CE', 'Department of Civil Engineering');

-- Insert Sample Courses
INSERT INTO courses (course_name, course_code, department_id, duration_years, description) VALUES
('B.Tech CSE', 'BTECH-CS', 1, 4, 'Bachelor of Technology in Computer Science'),
('B.Tech ECE', 'BTECH-EC', 2, 4, 'Bachelor of Technology in Electronics'),
('B.Tech ME', 'BTECH-ME', 3, 4, 'Bachelor of Technology in Mechanical'),
('B.Tech CE', 'BTECH-CE', 4, 4, 'Bachelor of Technology in Civil');

-- Insert Sample Fee Structure
INSERT INTO fee_structure (fee_name, course_id, academic_year, amount, description) VALUES
('Tuition Fee', 1, '2024-25', 50000, 'Tuition fee for B.Tech CSE'),
('Lab Fee', 1, '2024-25', 10000, 'Laboratory fee'),
('Semester Fee', 1, '2024-25', 5000, 'Semester administration fee'),
('Tuition Fee', 2, '2024-25', 50000, 'Tuition fee for B.Tech ECE'),
('Lab Fee', 2, '2024-25', 12000, 'Laboratory fee');

?>
