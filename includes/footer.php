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
        $footer_class = 'product-footer';
        $show_callout_title = false;
        $show_gadgets_button = false;
        $show_contact_button = true;
        $gadgets_link = 'product.php';
        break;
    
    case 'signup':
    case 'login':
        $footer_class = 'index-footer';
        $show_callout_title = false;
        $show_gadgets_button = false;
        $show_contact_button = true;
        $gadgets_link = 'signup.php';
        break;
    
    case 'detail':
        $footer_class = 'index-footer';
        $show_callout_title = false;
        $show_gadgets_button = true;
        $show_contact_button = true;
        $gadgets_link = 'product.php';
        break;
    
    default: // index
        $footer_class = 'index-footer';
        $show_callout_title = true;
        $show_gadgets_button = true;
        $show_contact_button = true;
        $gadgets_link = 'signup.php';
}
?>
<!-- footer section -->
<footer class="<?php echo $footer_class; ?>">
    <div class="callout">
        <?php if ($show_callout_title): ?>
        <h2>Let's Rent from ByteRent today!</h2>
        <?php endif; ?>
        <p class="callout-description">
            Need assistance or ready to rent your wanted product? Contact us now!
        </p>
        <div class="callout-buttons">
            <?php if ($show_gadgets_button): ?>
            <button
                class="btn-2 btn-callout"
                onclick="window.location.href='<?php echo $gadgets_link; ?>'">
                <p>Check Our Gadgets</p>
                <i class="fa-solid fa-arrow-long"></i>
            </button>
            <?php endif; ?>

            <?php if ($show_contact_button): ?>
            <button
                class="btn-2 btn-callout btn-callout-black"
                onclick="window.location.href='contact.html'">
                <p>Conatct Now</p>
                <i class="fa-solid fa-arrow-long"></i>
            </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer-bottom">
        <a href="index.php" class="footer-brand">Rent<b class="accent">Byte</b></a>
        <div class="socials">
            <a href="#" class="social-item">
                <i class="fa-brands fa-facebook-f"></i>
            </a>
            <a href="#" class="social-item">
                <i class="fa-brands fa-instagram"></i>
            </a>
            <a href="#" class="social-item">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a href="#" class="social-item">
                <i class="fa-brands fa-telegram"></i>
            </a>
        </div>
    </div>
</footer>

</body>
</html>
