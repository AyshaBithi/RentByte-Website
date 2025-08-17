<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RentByte - Database Installation</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --white: #eaeaea;
            --accent: #484d72;
            --light-accent: #878ec7;
            --black: #000000;
            --dark-gray: #d5d5d5;
            --darkkk-gray: #9a9a9a;
            --gray: #f9f9f9;
            --blue-gray: #f7f7fb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: var(--white);
            color: var(--black);
            line-height: 1.6;
        }

        .install-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(72, 77, 114, 0.1);
        }

        .header h1 {
            font-size: 48px;
            font-weight: bold;
            color: var(--accent);
            margin-bottom: 10px;
        }

        .header .accent {
            color: var(--light-accent);
        }

        .header p {
            font-size: 20px;
            color: var(--darkkk-gray);
            margin-bottom: 0;
        }

        .installation-box {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 15px 40px rgba(72, 77, 114, 0.1);
            margin-bottom: 30px;
        }

        .installation-title {
            font-size: 32px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .installation-title i {
            color: var(--light-accent);
        }

        .progress-item {
            padding: 12px 0;
            font-size: 16px;
            color: var(--black);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-item i {
            color: #28a745;
            font-size: 18px;
        }

        .success-box {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border: 2px solid #28a745;
            color: #155724;
            padding: 30px;
            margin: 30px 0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.2);
        }

        .success-box h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .success-box i {
            color: #28a745;
            font-size: 32px;
        }

        .error-box {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
            color: #721c24;
            padding: 30px;
            margin: 30px 0;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.2);
        }

        .error-box h3 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .error-box i {
            color: #dc3545;
            font-size: 32px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .info-list li {
            padding: 8px 0;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-list li i {
            color: var(--light-accent);
            font-size: 16px;
            width: 20px;
        }

        .btn-2 {
            width: 200px;
            height: 55px;
            font-size: 18px;
            background-color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 12px;
            color: #ffffff;
            transition: all ease-in-out 0.2s;
            cursor: pointer;
            filter: drop-shadow(5px 5px 10px rgba(216, 216, 216, 0.2));
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            margin: 10px 15px 10px 0;
        }

        .btn-2:hover {
            background-color: var(--black);
            filter: drop-shadow(5px 5px 10px rgba(216,216,216,0.6));
            color: white;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: var(--light-accent);
            border-color: var(--light-accent);
        }

        .btn-secondary:hover {
            background-color: var(--black);
            border-color: var(--black);
        }

        .button-container {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--dark-gray);
            border-radius: 50%;
            border-top-color: var(--accent);
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .step-loading {
            color: var(--darkkk-gray);
        }

        .step-completed {
            color: var(--black);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="header">
            <h1>Rent<span class="accent">Byte</span></h1>
            <p>Database Installation Wizard</p>
        </div>

        <?php if (!isset($_POST['install'])): ?>
        <!-- Database Configuration Form -->
        <div class="installation-box">
            <div class="installation-title">
                <i class="fas fa-database"></i>
                Database Configuration
            </div>

            <p style="margin-bottom: 30px; color: var(--darkkk-gray); font-size: 16px;">
                Please provide your database connection details to set up RentByte.
            </p>

            <form method="POST" action="install.php" style="max-width: 500px;">
                <div style="margin-bottom: 20px;">
                    <label for="host" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--black);">
                        <i class="fas fa-server" style="margin-right: 8px; color: var(--accent);"></i>Database Host
                    </label>
                    <input type="text" id="host" name="host" value="localhost" required
                           style="width: 100%; padding: 12px 16px; border: 2px solid var(--dark-gray); border-radius: 8px; font-size: 16px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="username" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--black);">
                        <i class="fas fa-user" style="margin-right: 8px; color: var(--accent);"></i>Username
                    </label>
                    <input type="text" id="username" name="username" value="root" required
                           style="width: 100%; padding: 12px 16px; border: 2px solid var(--dark-gray); border-radius: 8px; font-size: 16px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="password" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--black);">
                        <i class="fas fa-lock" style="margin-right: 8px; color: var(--accent);"></i>Password
                    </label>
                    <input type="password" id="password" name="password" value=""
                           style="width: 100%; padding: 12px 16px; border: 2px solid var(--dark-gray); border-radius: 8px; font-size: 16px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s;">
                    <small style="color: var(--darkkk-gray); font-size: 14px;">Leave empty if no password is set</small>
                </div>

                <div style="margin-bottom: 30px;">
                    <label for="database" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--black);">
                        <i class="fas fa-database" style="margin-right: 8px; color: var(--accent);"></i>Database Name
                    </label>
                    <input type="text" id="database" name="database" value="rent" required
                           style="width: 100%; padding: 12px 16px; border: 2px solid var(--dark-gray); border-radius: 8px; font-size: 16px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s;">
                    <small style="color: var(--darkkk-gray); font-size: 14px;">Database will be created if it doesn't exist</small>
                </div>

                <button type="submit" name="install" class="btn-2" style="width: 100%; margin: 0;">
                    <i class="fas fa-play" style="margin-right: 8px;"></i>Start Installation
                </button>
            </form>
        </div>

        <style>
            input:focus {
                outline: none;
                border-color: var(--accent) !important;
                box-shadow: 0 0 0 3px rgba(72, 77, 114, 0.1);
            }
        </style>

        <?php else: ?>
        <!-- Installation Process -->
        <div class="installation-box">
            <div class="installation-title">
                <i class="fas fa-cogs"></i>
                Installing Database
            </div>

            <div id="progress-container">
                <!-- Progress will be added here -->
            </div>

<?php
/**
 * Database Installation Script for RentByte
 *
 * This script creates the database and tables for the RentByte system.
 * Run this once to set up the database.
 */

// Get database configuration from form
$host = $_POST['host'];
$username = $_POST['username'];
$password = $_POST['password'];
$database = $_POST['database'];

// Flush output buffer to show progress in real-time
ob_start();

try {
    // Step 1: Database Connection
    echo "<script>
        document.getElementById('progress-container').innerHTML = '<div class=\"progress-item step-loading\"><div class=\"loading\"></div>Connecting to database server...</div>';
    </script>";
    ob_flush();
    flush();
    usleep(1000000); // 1 second delay

    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<script>
        document.getElementById('progress-container').innerHTML = '<div class=\"progress-item step-completed fade-in\"><i class=\"fas fa-check-circle\"></i>Database connection established</div>';
    </script>";
    ob_flush();
    flush();
    usleep(500000); // 0.5 second delay

    // Step 2: Installing Database
    echo "<script>
        var container = document.getElementById('progress-container');
        container.innerHTML += '<div class=\"progress-item step-loading\" id=\"install-step\"><div class=\"loading\"></div>Installing RentByte database...</div>';
    </script>";
    ob_flush();
    flush();
    usleep(1500000); // 1.5 second delay

    // Read and execute SQL file
    $sql = file_get_contents('database/schema.sql');

    if ($sql === false) {
        throw new Exception("Could not read schema.sql file. Make sure the database/schema.sql file exists.");
    }

    // Split SQL into individual statements and execute them
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false &&
                    strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
            }
        }
    }

    // Update the specific step to completed
    echo "<script>
        var installStep = document.getElementById('install-step');
        if (installStep) {
            installStep.innerHTML = '<i class=\"fas fa-check-circle\"></i>RentByte database installed successfully';
            installStep.className = 'progress-item step-completed fade-in';
        }
    </script>";
    ob_flush();
    flush();
    usleep(500000); // 0.5 second delay

    // Silently update the config file with user's credentials
    $config_content = "<?php
/**
 * Database Configuration File for RentByte
 *
 * This file contains database connection settings and utility functions
 * for the RentByte rental system.
 */

// Database configuration constants
define('DB_HOST', '$host');
define('DB_USERNAME', '$username');
define('DB_PASSWORD', '$password');
define('DB_NAME', '$database');

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
    static \$connection = null;

    if (\$connection === null) {
        \$connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check connection
        if (\$connection->connect_error) {
            error_log(\"Database connection failed: \" . \$connection->connect_error);
            return false;
        }

        // Set charset to utf8
        \$connection->set_charset(\"utf8\");
    }

    return \$connection;
}

/**
 * Execute a prepared statement safely
 * @param string \$query SQL query with placeholders
 * @param array \$params Parameters for the query
 * @param string \$types Parameter types (e.g., 'ssi' for string, string, integer)
 * @return mysqli_result|bool Query result or false on failure
 */
function executeQuery(\$query, \$params = [], \$types = '') {
    \$conn = getDBConnection();
    if (!\$conn) {
        return false;
    }

    \$stmt = \$conn->prepare(\$query);
    if (!\$stmt) {
        error_log(\"Prepare failed: \" . \$conn->error);
        return false;
    }

    if (!empty(\$params)) {
        \$stmt->bind_param(\$types, ...\$params);
    }

    \$result = \$stmt->execute();
    if (!\$result) {
        error_log(\"Execute failed: \" . \$stmt->error);
        return false;
    }

    // For SELECT queries, return the result set
    // For INSERT, UPDATE, DELETE queries, return the statement result
    if (stripos(trim(\$query), 'SELECT') === 0) {
        return \$stmt->get_result();
    } else {
        return \$result;
    }
}

/**
 * Sanitize input data
 * @param string \$data Input data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput(\$data) {
    \$data = trim(\$data);
    \$data = stripslashes(\$data);
    \$data = htmlspecialchars(\$data);
    return \$data;
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset(\$_SESSION['user_id']) && !empty(\$_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool True if user is admin, false otherwise
 */
function isAdmin() {
    return isset(\$_SESSION['role']) && \$_SESSION['role'] === 'admin';
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
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit();
    }
}

/**
 * Set success message in session
 * @param string \$message Success message
 */
function setSuccessMessage(\$message) {
    \$_SESSION['success_message'] = \$message;
}

/**
 * Set error message in session
 * @param string \$message Error message
 */
function setErrorMessage(\$message) {
    \$_SESSION['error_message'] = \$message;
}

/**
 * Get and clear success message from session
 * @return string|null Success message or null
 */
function getSuccessMessage() {
    if (isset(\$_SESSION['success_message'])) {
        \$message = \$_SESSION['success_message'];
        unset(\$_SESSION['success_message']);
        return \$message;
    }
    return null;
}

/**
 * Get and clear error message from session
 * @return string|null Error message or null
 */
function getErrorMessage() {
    if (isset(\$_SESSION['error_message'])) {
        \$message = \$_SESSION['error_message'];
        unset(\$_SESSION['error_message']);
        return \$message;
    }
    return null;
}

/**
 * Format currency for display
 * @param float \$amount Amount to format
 * @return string Formatted currency string
 */
function formatCurrency(\$amount) {
    return '৳' . number_format(\$amount, 2);
}

/**
 * Calculate rental duration in days
 * @param string \$start_date Start date
 * @param string \$end_date End date
 * @return int Number of days
 */
function calculateRentalDays(\$start_date, \$end_date) {
    \$start = new DateTime(\$start_date);
    \$end = new DateTime(\$end_date);
    \$interval = \$start->diff(\$end);
    return \$interval->days + 1; // Include both start and end dates
}

/**
 * Generate a unique rental ID
 * @return string Unique rental ID
 */
function generateRentalId() {
    return 'RB' . date('Ymd') . rand(1000, 9999);
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset(\$_SESSION['csrf_token'])) {
        \$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return \$_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string \$token Token to verify
 * @return bool True if token is valid, false otherwise
 */
function verifyCSRFToken(\$token) {
    return isset(\$_SESSION['csrf_token']) && hash_equals(\$_SESSION['csrf_token'], \$token);
}
?>";

    file_put_contents('includes/config.php', $config_content);

    // Final completion step
    echo "<script>
        document.getElementById('progress-container').innerHTML += '<div class=\"progress-item step-completed fade-in\"><i class=\"fas fa-check-circle\"></i><strong>Installation completed successfully!</strong></div>';
    </script>";
    ob_flush();
    flush();

    echo "</div>"; // Close installation-box

    echo "<div class='success-box'>";
    echo "<h3><i class='fas fa-check-circle'></i>Installation Completed Successfully!</h3>";
    echo "<p><strong>The RentByte database has been set up with the following:</strong></p>";
    echo "<ul class='info-list'>";
    echo "<li><i class='fas fa-database'></i>Database: <strong>$database</strong></li>";
    echo "<li><i class='fas fa-table'></i>Tables: users, categories, gadgets, rentals, rental_history</li>";
    echo "<li><i class='fas fa-user-shield'></i>Default admin user: <strong>admin@mail.com</strong> (password: <strong>password</strong>)</li>";
    echo "<li><i class='fas fa-box'></i>Sample categories and gadgets</li>";
    echo "</ul>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul class='info-list'>";
    echo "<li><i class='fas fa-trash'></i>Delete this install.php file for security</li>";
    echo "<li><i class='fas fa-sign-in-alt'></i>Login with admin credentials to manage the system</li>";
    echo "<li><i class='fas fa-plus'></i>Add more gadgets and categories as needed</li>";
    echo "</ul>";
    echo "</div>";

    echo "<div class='button-container'>";
    echo "<a href='index.php' class='btn-2'><i class='fas fa-home' style='margin-right: 8px;'></i>Go to Homepage</a>";
    echo "<a href='login.php' class='btn-2 btn-secondary'><i class='fas fa-sign-in-alt' style='margin-right: 8px;'></i>Login as Admin</a>";
    echo "</div>";

} catch (Exception $e) {
    echo "<script>
        document.getElementById('progress-container').innerHTML += '<div class=\"progress-item\" style=\"color: #dc3545;\"><i class=\"fas fa-exclamation-triangle\"></i><strong>Installation failed!</strong></div>';
    </script>";

    echo "</div>"; // Close installation-box

    echo "<div class='error-box'>";
    echo "<h3><i class='fas fa-exclamation-triangle'></i>Installation Failed</h3>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Please check:</strong></p>";
    echo "<ul class='info-list'>";
    echo "<li><i class='fas fa-server'></i>MySQL server is running</li>";
    echo "<li><i class='fas fa-key'></i>Database credentials are correct</li>";
    echo "<li><i class='fas fa-user-check'></i>User has permission to create databases</li>";
    echo "<li><i class='fas fa-file'></i>The database/schema.sql file exists</li>";
    echo "</ul>";
    echo "</div>";

    echo "<div class='button-container'>";
    echo "<a href='install.php' class='btn-2'><i class='fas fa-redo' style='margin-right: 8px;'></i>Try Again</a>";
    echo "</div>";
}

ob_end_flush();
?>

        <?php endif; ?>

    </div>
</body>
</html>


