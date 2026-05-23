<?php
/**
 * Database Connection File
 * Library Management System
 * 
 * This file establishes connection to MySQL database
 * Using PDO for secure database operations with prepared statements
 */

// Database configuration
$host = 'localhost';
$dbname = 'library_management';
$username = 'root';
$password = '';

// Set default timezone
date_default_timezone_set('Asia/Kolkata');

try {
    // Create PDO connection
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    // Set error reporting for development (remove in production)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
} catch(PDOException $e) {
    // Display error message if connection fails
    die("Database Connection Failed: " . $e->getMessage());
}

/**
 * Function to sanitize input data
 * @param string $data - Data to sanitize
 * @return string - Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Function to display success message
 * @param string $message - Message to display
 */
function showSuccess($message) {
    return '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Function to display error message
 * @param string $message - Message to display
 */
function showError($message) {
    return '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Function to display warning message
 * @param string $message - Message to display
 */
function showWarning($message) {
    return '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Warning!</strong> ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
}

/**
 * Function to redirect to another page
 * @param string $url - URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Function to check if admin is logged in
 * @return bool - True if logged in, false otherwise
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Function to get current admin ID
 * @return int - Admin ID or 0 if not logged in
 */
function getCurrentAdminId() {
    return isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;
}

/**
 * Function to get current admin name
 * @return string - Admin name or empty string if not logged in
 */
function getCurrentAdminName() {
    return isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : '';
}
?>
