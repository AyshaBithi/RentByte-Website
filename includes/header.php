<?php
// Get the page type parameter, default to 'index' if not provided
$page_type = isset($_GET['page_type']) ? $_GET['page_type'] : 'index';

// If page_type is not passed via GET, try to determine from the current file
if ($page_type === 'index') {
    $current_file = basename($_SERVER['PHP_SELF']);
    switch ($current_file) {
        case 'product.php':
        case 'product2.php':
            $page_type = 'product';
            break;
        case 'signup.php':
            $page_type = 'signup';
            break;
        case 'login.php':
            $page_type = 'login';
            break;
        case 'detail.php':
            $page_type = 'detail';
            break;
        default:
            $page_type = 'index';
    }
}

// Set page-specific variables based on page type
switch ($page_type) {
    case 'product':
        $css_file = 'assets/css/product2style.css';
        $js_file = 'assets/js/index.js';
        $title = 'RentByte';
        $logo_path = 'assets/img/logo.jpg';
        $icon_path = 'assets/img/logo.jpg';
        $show_full_nav = true;
        $show_get_started = true;
        $include_scripts = true;
        break;
    
    case 'signup':
        $css_file = 'assets/css/signupstyle.css';
        $js_file = 'assets/js/index.js';
        $title = 'RentByte';
        $logo_path = 'assets/img/logo.jpg';
        $icon_path = 'assets/img/logo.jpg';
        $show_full_nav = false;
        $show_get_started = false;
        $include_scripts = true;
        break;
    
    case 'login':
        $css_file = 'assets/css/signupstyle.css';
        $js_file = 'assets/js/index.js';
        $title = 'RentByte';
        $logo_path = 'assets/img/logo.jpg';
        $icon_path = 'assets/img/logo.jpg';
        $show_full_nav = false;
        $show_get_started = false;
        $include_scripts = true;
        break;
    
    case 'detail':
        $css_file = 'assets/css/detailstyle.css';
        $js_file = null; // No JS file for detail page
        $title = 'ByteRent';
        $logo_path = 'assets/img/logo.jpg';
        $icon_path = 'assets/img/logo.jpg';
        $show_full_nav = true;
        $show_get_started = false;
        $include_scripts = false;
        break;
    
    default: // index
        $css_file = 'assets/css/style.css';
        $js_file = 'assets/js/index.js';
        $title = 'RentByte';
        $logo_path = 'assets/img/logo.jpg';
        $icon_path = 'assets/img/logo.jpg';
        $show_full_nav = true;
        $show_get_started = true;
        $include_scripts = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo $css_file; ?>">
    <?php if ($js_file): ?>
    <script src="<?php echo $js_file; ?>" defer></script>
    <?php endif; ?>

    <?php if ($include_scripts): ?>
    <!-- link to scroll -->
    <script src="https://unpkg.com/scrollreveal"></script>
    <!-- for icon-->
    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
    />
    <?php endif; ?>

    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?php echo $icon_path; ?>" type="image/x-icon">
    
</head>
<body>

    <!-- navbar -->
    <nav>
        <a href="index.php" class="brand">
            <img class="logo" src="<?php echo $logo_path; ?>" alt="">
        </a>
        <div class="menu">
            <div class="btn">
                <i class="fas fa times close btn"></i>
            </div>
            <a href="index.php">HOME</a>
            <?php if ($show_full_nav): ?>
            <a href="product.php">GADGETS</a>
            <a href="index.php#about">ABOUT</a>
            <a href="index.php#review">REVIEWS</a>
            <?php endif; ?>
        </div>
        <?php if ($show_get_started): ?>
            <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'dashboard.php'; ?>" class="btn-2 btn-hero">
                        <?php echo isAdmin() ? 'Admin Panel' : 'Dashboard'; ?>
                    </a>
                    <a href="logout.php" class="btn-2 btn-secondary" style="margin-left: 10px;">
                        Logout
                    </a>
                </div>
            <?php else: ?>
                <a href="signup.php">
                    <button class="btn-2 btn-hero">
                        Get Started
                    </button>
                </a>
            <?php endif; ?>
        <?php endif; ?>

        <div class="btn">
            <i class="fas fa-bars menu-btn"></i>
        </div>
    </nav>

    <!-- navbar end -->
