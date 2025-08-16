<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>RentByte Admin</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <!-- Admin Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .admin-nav {
            background: #2c3e50;
            color: white;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px 0;
        }

        .nav-brand h1 {
            font-size: 1.5em;
            font-weight: 600;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 0;
        }

        .nav-menu li {
            position: relative;
        }

        .nav-menu a {
            display: block;
            padding: 20px 15px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            background-color: #34495e;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .dropdown-toggle:hover {
            background-color: #34495e;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            min-width: 180px;
            display: none;
            z-index: 1001;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
        }

        .dropdown-menu a:hover {
            background-color: #f8f9fa;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .main-content {
            min-height: calc(100vh - 70px);
            padding: 20px 0;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
                gap: 10px;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-menu a {
                padding: 10px 12px;
            }

            .nav-user {
                order: -1;
                align-self: flex-end;
            }
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeaa7;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>🏢 RentByte Admin</h1>
            </div>
            
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a></li>
                <li><a href="users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a></li>
                <li><a href="gadgets.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'gadgets.php' ? 'active' : ''; ?>">
                    <i class="fas fa-mobile-alt"></i> Gadgets
                </a></li>
                <li><a href="rentals.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'rentals.php' ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i> Rentals
                </a></li>
                <li><a href="categories.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
                    <i class="fas fa-folder"></i> Categories
                </a></li>
            </ul>
            
            <div class="nav-user">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                </div>
                
                <div class="dropdown">
                    <button class="dropdown-toggle" onclick="toggleDropdown()">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu" id="userDropdown">
                        <a href="../dashboard.php"><i class="fas fa-user"></i> User Dashboard</a>
                        <a href="../index.php"><i class="fas fa-home"></i> View Website</a>
                        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <?php
        // Display success/error messages
        if ($success_msg = getSuccessMessage()) {
            echo '<div class="alert alert-success">' . htmlspecialchars($success_msg) . '</div>';
        }
        if ($error_msg = getErrorMessage()) {
            echo '<div class="alert alert-error">' . htmlspecialchars($error_msg) . '</div>';
        }
        ?>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const toggle = document.querySelector('.dropdown-toggle');
            
            if (!toggle.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    </script>
