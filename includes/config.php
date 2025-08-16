<?php
/**
 * Database Configuration File for RentByte
 * 
 * This file contains database connection settings and utility functions
 * for the RentByte rental system.
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'rent');

// Set default timezone
date_default_timezone_set('Asia/Dhaka');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Create database connection
 * @return mysqli|false Database connection object or false on failure
 */
function getDBConnection() {
    static $connection = null;
    
    if ($connection === null) {
        $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        // Check connection
        if ($connection->connect_error) {
            error_log("Database connection failed: " . $connection->connect_error);
            return false;
        }
        
        // Set charset to utf8
        $connection->set_charset("utf8");
    }
    
    return $connection;
}

/**
 * Execute a prepared statement safely
 * @param string $query SQL query with placeholders
 * @param array $params Parameters for the query
 * @param string $types Parameter types (e.g., 'ssi' for string, string, integer)
 * @return mysqli_result|bool Query result or false on failure
 */
function executeQuery($query, $params = [], $types = '') {
    $conn = getDBConnection();
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $result = $stmt->execute();
    if (!$result) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }

    // For SELECT queries, return the result set
    // For INSERT, UPDATE, DELETE queries, return the statement result
    if (stripos(trim($query), 'SELECT') === 0) {
        return $stmt->get_result();
    } else {
        return $result;
    }
}

/**
 * Sanitize input data
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirect to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Redirect to login page if not admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if token is valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Display success message
 * @param string $message Success message
 */
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

/**
 * Display error message
 * @param string $message Error message
 */
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

/**
 * Get and clear success message
 * @return string|null Success message or null
 */
function getSuccessMessage() {
    if (isset($_SESSION['success_message'])) {
        $message = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
        return $message;
    }
    return null;
}

/**
 * Get and clear error message
 * @return string|null Error message or null
 */
function getErrorMessage() {
    if (isset($_SESSION['error_message'])) {
        $message = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
        return $message;
    }
    return null;
}

/**
 * Format currency
 * @param float $amount Amount to format
 * @return string Formatted currency
 */
function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

/**
 * Format date
 * @param string $date Date to format
 * @return string Formatted date
 */
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime
 * @param string $datetime Datetime to format
 * @return string Formatted datetime
 */
function formatDateTime($datetime) {
    return date('M d, Y g:i A', strtotime($datetime));
}

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
