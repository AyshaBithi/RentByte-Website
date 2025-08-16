<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $full_name = sanitizeInput($_POST['full_name']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validation
        if (empty($username) || empty($email) || empty($full_name) || empty($password) || empty($confirm_password)) {
            $error_message = 'Please fill in all required fields.';
        } elseif (strlen($username) < 3) {
            $error_message = 'Username must be at least 3 characters long.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error_message = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Passwords do not match.';
        } else {
            // Check if username or email already exists
            $query = "SELECT id FROM users WHERE username = ? OR email = ?";
            $result = executeQuery($query, [$username, $email], 'ss');

            if ($result && $result->num_rows > 0) {
                $error_message = 'Username or email already exists.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO users (username, email, password, full_name) VALUES (?, ?, ?, ?)";
                $result = executeQuery($query, [$username, $email, $hashed_password, $full_name], 'ssss');

                if ($result) {
                    setSuccessMessage('Account created successfully! Please login.');
                    header('Location: login.php');
                    exit();
                } else {
                    $error_message = 'Error creating account. Please try again.';
                }
            }
        }
    }
}

include('includes/header.php');
?>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    font-size: 14px;
}

.alert-error {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
</style>

<!-- Sign-up Form Section -->
<section class="signup-section">
    <div class="signup-container">
        <h2>Create Your Account</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form id="signup-form" action="signup.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <div class="form-group">
                <label for="username">Username: <span class="required">*</span></label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email: <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name: <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
            </div>



            <div class="form-group">
                <label for="password">Password: <span class="required">*</span></label>
                <input type="password" id="password" name="password" required>
                <small>Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password: <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-signup">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>

        <button class="btn-gadget btn-herogadget" type="submit" form="signup-form">
            <p>Sign Up</p>
        </button>
    </div>
</section>

<?php include('includes/footer.php')?>