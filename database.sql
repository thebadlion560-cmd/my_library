-- Library Management System Database
-- Run this SQL file in phpMyAdmin to create the database and tables

-- Create Database
CREATE DATABASE IF NOT EXISTS library_management;
USE library_management;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS issued_books;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS admins;

-- 1. Create admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- Password is hashed using MD5 for simplicity (use bcrypt in production)
INSERT INTO admins (name, email, password) VALUES 
('Admin User', 'admin@library.com', '0192023a7bbd73250516f069df18b500');

-- 2. Create books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    quantity INT NOT NULL DEFAULT 1,
    available_quantity INT NOT NULL DEFAULT 1,
    added_date DATE NOT NULL DEFAULT CURRENT_DATE
);

-- Insert sample books
INSERT INTO books (title, author, category, isbn, quantity, available_quantity) VALUES
('Introduction to Algorithms', 'Thomas H. Cormen', 'Computer Science', '978-0262033848', 5, 5),
('Data Structures and Algorithms', 'Mark Allen Weiss', 'Computer Science', '978-0132576277', 3, 3),
('The C Programming Language', 'Brian Kernighan', 'Computer Science', '978-0131103627', 4, 4),
('Clean Code', 'Robert C. Martin', 'Computer Science', '978-0132350884', 2, 2),
('Design Patterns', 'Erich Gamma', 'Computer Science', '978-0201633610', 3, 3);

-- 3. Create students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample students
INSERT INTO students (name, email, phone) VALUES
('Rahul Sharma', 'rahul@example.com', '9876543210'),
('Priya Singh', 'priya@example.com', '9876543211'),
('Amit Patel', 'amit@example.com', '9876543212'),
('Sneha Gupta', 'sneha@example.com', '9876543213');

-- 4. Create issued_books table
CREATE TABLE issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_id INT NOT NULL,
    issue_date DATE NOT NULL,
    return_date DATE,
    status ENUM('issued', 'returned') DEFAULT 'issued',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Insert sample issued books
INSERT INTO issued_books (book_id, student_id, issue_date, return_date, status) VALUES
(1, 1, '2024-01-15', '2024-01-20', 'returned'),
(2, 2, '2024-01-18', NULL, 'issued'),
(3, 3, '2024-01-20', NULL, 'issued');

-- Create indexes for better performance
CREATE INDEX idx_books_isbn ON books(isbn);
CREATE INDEX idx_books_category ON books(category);
CREATE INDEX idx_issued_books_status ON issued_books(status);
CREATE INDEX idx_issued_books_issue_date ON issued_books(issue_date);

-- Display success message
SELECT 'Database created successfully!' AS Message;
SELECT 'Default Admin Login:' AS Info;
SELECT 'Email: admin@library.com' AS Email;
SELECT 'Password: admin123' AS Password;
