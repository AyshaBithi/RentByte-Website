<?php
require_once 'includes/config.php';

$gadget_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error_message = '';
$success_message = '';

if ($gadget_id <= 0) {
    header('Location: product.php');
    exit();
}

// Get gadget details
$gadget_query = "SELECT g.*, c.name as category_name
                FROM gadgets g
                JOIN categories c ON g.category_id = c.id
                WHERE g.id = ?";
$gadget_result = executeQuery($gadget_query, [$gadget_id], 'i');

if (!$gadget_result || $gadget_result->num_rows == 0) {
    header('Location: product.php');
    exit();
}

$gadget = $gadget_result->fetch_assoc();

// Handle rental request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rent_gadget'])) {
    if (!isLoggedIn()) {
        setErrorMessage('Please login to rent gadgets.');
        header('Location: login.php');
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $delivery_type = $_POST['delivery_type'];
    $delivery_address = sanitizeInput($_POST['delivery_address']);
    $notes = sanitizeInput($_POST['notes']);
    
    // Validation
    if (empty($start_date) || empty($end_date)) {
        $error_message = 'Please select start and end dates.';
    } elseif (strtotime($start_date) < strtotime(date('Y-m-d'))) {
        $error_message = 'Start date cannot be in the past.';
    } elseif (strtotime($end_date) <= strtotime($start_date)) {
        $error_message = 'End date must be after start date.';
    } elseif ($delivery_type !== 'pickup' && empty($delivery_address)) {
        $error_message = 'Please provide delivery address for delivery options.';
    } else {
        // Check if gadget is available for the selected dates
        $availability_query = "SELECT id FROM rentals 
                              WHERE gadget_id = ? 
                              AND status IN ('confirmed', 'active') 
                              AND ((start_date <= ? AND end_date >= ?) 
                                   OR (start_date <= ? AND end_date >= ?) 
                                   OR (start_date >= ? AND end_date <= ?))";
        $availability_result = executeQuery($availability_query, 
            [$gadget_id, $start_date, $start_date, $end_date, $end_date, $start_date, $end_date], 
            'issssss');
        
        if ($availability_result && $availability_result->num_rows > 0) {
            $error_message = 'Gadget is not available for the selected dates.';
        } else {
            // Calculate rental details
            $start_timestamp = strtotime($start_date);
            $end_timestamp = strtotime($end_date);
            $total_days = ceil(($end_timestamp - $start_timestamp) / (60 * 60 * 24));
            $price_per_day = $gadget['price_per_day'];
            $total_amount = $total_days * $price_per_day;
            
            // Create rental record
            $rental_query = "INSERT INTO rentals (user_id, gadget_id, start_date, end_date, total_days, price_per_day, total_amount, delivery_type, delivery_address, notes) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $rental_result = executeQuery($rental_query, 
                [$user_id, $gadget_id, $start_date, $end_date, $total_days, $price_per_day, $total_amount, $delivery_type, $delivery_address, $notes], 
                'iissiddsss');
            
            if ($rental_result) {
                setSuccessMessage('Rental request submitted successfully! We will contact you soon.');
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Error submitting rental request. Please try again.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gadget['name']); ?> - RentByte</title>
    <link rel="stylesheet" href="assets/css/detailstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <a href="index.php" class="brand">
            <h1>Rent<span class="accent">Byte</span></h1>
        </a>
        <div class="menu">
            <a href="index.php">Home</a>
            <a href="product.php">Products</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </div>
        <?php if (isLoggedIn()): ?>
            <div class="user-menu">
                <a href="<?php echo isAdmin() ? 'admin/dashboard.php' : 'dashboard.php'; ?>">
                    <button class="btn-2">
                        <?php echo isAdmin() ? 'Admin Panel' : 'Dashboard'; ?>
                    </button>
                </a>
            </div>
        <?php else: ?>
            <a href="signup.php">
                <button class="btn-2">
                    Get Started
                </button>
            </a>
        <?php endif; ?>
    </nav>

    <!-- Breadcrumb -->
    <div class="pagination">
        <p><a href="index.php">Home</a> > <a href="product.php">Products</a> > <?php echo htmlspecialchars($gadget['category_name']); ?> > <?php echo htmlspecialchars($gadget['name']); ?></p>
    </div>
    <!-- Product Section -->
    <section class="product-container">
        <!-- Left side - Images -->
        <div class="img-card">
            <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" id="featured-image">
            <!-- Small images placeholder - you can add multiple images later -->
            <div class="small-Card">
                <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" class="small-Img">
                <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" class="small-Img">
                <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" class="small-Img">
                <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" class="small-Img">
            </div>
        </div>

        <!-- Right side - Product Info -->
        <div class="product-info">
            <h3><?php echo htmlspecialchars($gadget['name']); ?></h3>
            <div class="gadget-price">
                <h5><?php echo htmlspecialchars($gadget['brand'] . ' ' . $gadget['model']); ?> <?php echo formatCurrency($gadget['price_per_day']); ?></h5>
                <h6>/Day</h6>
            </div>
            <p><?php echo htmlspecialchars($gadget['description']); ?></p>

            <?php if ($gadget['specifications']): ?>
                <p><strong>Specifications:</strong> <?php echo htmlspecialchars($gadget['specifications']); ?></p>
            <?php endif; ?>

            <div class="sizes">
                <p>Location:</p>
                <select name="location" id="location" class="size-option" disabled>
                    <option value="<?php echo htmlspecialchars($gadget['location']); ?>"><?php echo htmlspecialchars($gadget['location']); ?></option>
                </select>
            </div>

            <?php if ($error_message): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 15px 0;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="quantity">
                <?php if ($gadget['status'] === 'available'): ?>
                    <?php if (isLoggedIn()): ?>
                        <button onclick="openRentalModal()">Rent Now</button>
                    <?php else: ?>
                        <button onclick="window.location.href='login.php'">Login to Rent</button>
                    <?php endif; ?>
                <?php else: ?>
                    <button disabled style="background: #6c757d; cursor: not-allowed;">Not Available</button>
                <?php endif; ?>
            </div>

            <div>
                <p>Delivery:</p>
                <p>Free standard shipping on orders over $35 before tax, plus free returns.</p>
                <div class="delivery">
                    <p>TYPE</p> <p>HOW LONG</p> <p>HOW MUCH</p>
                </div>
                <hr>
                <div class="delivery">
                    <p>Standard delivery</p>
                    <p>1-4 business days</p>
                    <p>$4.50</p>
                </div>
                <hr>
                <div class="delivery">
                    <p>Express delivery</p>
                    <p>1 business day</p>
                    <p>$10.00</p>
                </div>
                <hr>
                <div class="delivery">
                    <p>Pick up</p>
                    <p>1-3 business days</p>
                    <p>Free</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Rental Modal -->
    <div id="rentalModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 10px; width: 90%; max-width: 600px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Rent <?php echo htmlspecialchars($gadget['name']); ?></h2>
                <span onclick="closeRentalModal()" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa;">&times;</span>
            </div>

            <form method="POST" id="rentalForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="start_date" style="display: block; margin-bottom: 5px; font-weight: bold;">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                    <div>
                        <label for="end_date" style="display: block; margin-bottom: 5px; font-weight: bold;">End Date:</label>
                        <input type="date" id="end_date" name="end_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="delivery_type" style="display: block; margin-bottom: 5px; font-weight: bold;">Delivery Option:</label>
                    <select id="delivery_type" name="delivery_type" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="pickup">Pickup (Free)</option>
                        <option value="standard">Standard Delivery ($4.50)</option>
                        <option value="express">Express Delivery ($10.00)</option>
                    </select>
                </div>

                <div id="delivery_address_group" style="display: none; margin-bottom: 20px;">
                    <label for="delivery_address" style="display: block; margin-bottom: 5px; font-weight: bold;">Delivery Address:</label>
                    <textarea id="delivery_address" name="delivery_address" placeholder="Enter your full delivery address" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; height: 80px; resize: vertical;"></textarea>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="notes" style="display: block; margin-bottom: 5px; font-weight: bold;">Additional Notes (Optional):</label>
                    <textarea id="notes" name="notes" placeholder="Any special requirements or notes" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; height: 60px; resize: vertical;"></textarea>
                </div>

                <div id="rental_summary" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <h3>Rental Summary</h3>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Rental Days:</span>
                        <span id="total_days">0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Price per Day:</span>
                        <span><?php echo formatCurrency($gadget['price_per_day']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Subtotal:</span>
                        <span id="subtotal">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <span>Delivery Fee:</span>
                        <span id="delivery_fee">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 2px solid var(--accent); padding-top: 10px;">
                        <span>Total Amount:</span>
                        <span id="total_amount">$0.00</span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" onclick="closeRentalModal()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                    <button type="submit" name="rent_gadget" style="background: var(--accent); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Submit Rental Request</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-bottom">
            <a href="index.php" class="footer-brand">RentByte</a>
            <div class="socials">
                <a href="#" class="social-item"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-item"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-item"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-item"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const deliveryTypeSelect = document.getElementById('delivery_type');
    const deliveryAddressGroup = document.getElementById('delivery_address_group');
    const rentalSummary = document.getElementById('rental_summary');
    const pricePerDay = <?php echo $gadget['price_per_day']; ?>;

    function updateSummary() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const deliveryType = deliveryTypeSelect.value;

        if (startDate && endDate && endDate > startDate) {
            const timeDiff = endDate.getTime() - startDate.getTime();
            const totalDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            const subtotal = totalDays * pricePerDay;
            
            let deliveryFee = 0;
            if (deliveryType === 'standard') deliveryFee = 4.50;
            else if (deliveryType === 'express') deliveryFee = 10.00;
            
            const totalAmount = subtotal + deliveryFee;

            document.getElementById('total_days').textContent = totalDays;
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('delivery_fee').textContent = '$' + deliveryFee.toFixed(2);
            document.getElementById('total_amount').textContent = '$' + totalAmount.toFixed(2);
            
            rentalSummary.style.display = 'block';
        } else {
            rentalSummary.style.display = 'none';
        }
    }

    function toggleDeliveryAddress() {
        if (deliveryTypeSelect.value === 'pickup') {
            deliveryAddressGroup.style.display = 'none';
            document.getElementById('delivery_address').required = false;
        } else {
            deliveryAddressGroup.style.display = 'block';
            document.getElementById('delivery_address').required = true;
        }
        updateSummary();
    }

    startDateInput.addEventListener('change', updateSummary);
    endDateInput.addEventListener('change', updateSummary);
    deliveryTypeSelect.addEventListener('change', function() {
        toggleDeliveryAddress();
        updateSummary();
    });

    // Set minimum end date when start date changes
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        startDate.setDate(startDate.getDate() + 1);
        endDateInput.min = startDate.toISOString().split('T')[0];
    });
});

// Modal functions
function openRentalModal() {
    document.getElementById('rentalModal').style.display = 'block';
}

function closeRentalModal() {
    document.getElementById('rentalModal').style.display = 'none';
}

// Small image click functionality
document.addEventListener('DOMContentLoaded', function() {
    const smallImages = document.querySelectorAll('.small-Img');
    const featuredImage = document.getElementById('featured-image');

    smallImages.forEach(function(img) {
        img.addEventListener('click', function() {
            featuredImage.src = this.src;

            // Remove active class from all small images
            smallImages.forEach(function(smallImg) {
                smallImg.classList.remove('sm-card');
            });

            // Add active class to clicked image
            this.classList.add('sm-card');
        });
    });

    // Set first image as active by default
    if (smallImages.length > 0) {
        smallImages[0].classList.add('sm-card');
    }
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('rentalModal');
    if (event.target === modal) {
        closeRentalModal();
    }
});
</script>

</body>
</html>
