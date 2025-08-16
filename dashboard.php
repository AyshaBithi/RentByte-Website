<?php
require_once 'includes/config.php';
requireLogin();

// Get user information
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// Get user's pending rental requests
$pending_requests_query = "SELECT r.*, g.name as gadget_name, g.image, g.brand, g.model
                          FROM rentals r
                          JOIN gadgets g ON r.gadget_id = g.id
                          WHERE r.user_id = ? AND r.status = 'pending'
                          ORDER BY r.created_at DESC";
$pending_requests = executeQuery($pending_requests_query, [$user_id], 'i');

// Get user's active rentals
$active_rentals_query = "SELECT r.*, g.name as gadget_name, g.image, g.brand, g.model
                        FROM rentals r
                        JOIN gadgets g ON r.gadget_id = g.id
                        WHERE r.user_id = ? AND r.status IN ('confirmed', 'active')
                        ORDER BY r.start_date ASC";
$active_rentals = executeQuery($active_rentals_query, [$user_id], 'i');

// Get user's rental history
$history_query = "SELECT r.*, g.name as gadget_name, g.image, g.brand, g.model
                 FROM rentals r
                 JOIN gadgets g ON r.gadget_id = g.id
                 WHERE r.user_id = ? AND r.status IN ('completed', 'cancelled')
                 ORDER BY r.created_at DESC LIMIT 10";
$rental_history = executeQuery($history_query, [$user_id], 'i');

// Get available gadgets for browsing
$available_gadgets_query = "SELECT g.*, c.name as category_name 
                           FROM gadgets g 
                           JOIN categories c ON g.category_id = c.id 
                           WHERE g.status = 'available' 
                           ORDER BY g.created_at DESC LIMIT 6";
$available_gadgets = executeQuery($available_gadgets_query);

include('includes/header.php');
?>

<style>
/* Import the main website's CSS variables */
:root{
    --white: #eaeaea;
    --accent: #484d72;
    --light-accent: #878ec7;
    --black: #000000;
    --dark-gray: #d5d5d5;
    --darkkk-gray: #9a9a9a;
    --gray: #f9f9f9;
    --blue-gray: #f7f7fb;
}

body {
    background-color: var(--white);
    font-family: "Poppins", sans-serif;
    margin: 0;
    padding: 0;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 100px 20px 20px 20px; /* Top padding for fixed navbar */
    min-height: 100vh;
}

.dashboard-header {
    background-color: var(--white);
    color: var(--black);
    padding: 40px;
    border-radius: 16px;
    margin-bottom: 40px;
    text-align: center;
    border: 2px solid var(--accent);
}

.dashboard-header h1 {
    font-size: 48px;
    font-weight: 700;
    color: var(--accent);
    margin-bottom: 10px;
}

.dashboard-header p {
    font-size: 20px;
    color: var(--black);
    margin-bottom: 30px;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
    margin-bottom: 40px;
}

.stat-card {
    background: var(--white);
    padding: 30px;
    border-radius: 16px;
    border: 2px solid var(--accent);
    text-align: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(72, 77, 114, 0.2);
}

.stat-number {
    font-size: 3em;
    font-weight: bold;
    color: var(--accent);
    margin-bottom: 10px;
}

.stat-card div:last-child {
    font-size: 18px;
    color: var(--black);
    font-weight: 500;
}

.dashboard-section {
    background: var(--white);
    padding: 30px;
    border-radius: 16px;
    border: 2px solid var(--accent);
    margin-bottom: 40px;
}

.section-title {
    font-size: 28px;
    margin-bottom: 30px;
    color: var(--accent);
    font-weight: 700;
    text-align: center;
    border-bottom: 3px solid var(--light-accent);
    padding-bottom: 15px;
}

/* Button styles matching the main website */
.btn-2 {
    width: 160px;
    height: 50px;
    font-size: 20px;
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
    margin: 0 10px;
}

.btn-2:hover {
    background-color: var(--black);
    filter: drop-shadow(5px 5px 10px rgba(216,216,216,0.6));
    color: white;
}

.btn-secondary {
    background-color: var(--darkkk-gray);
    border-color: var(--darkkk-gray);
}

.btn-secondary:hover {
    background-color: var(--black);
    border-color: var(--black);
}

.btn-callout {
    width: 200px;
    height: 50px;
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
    margin: 0 10px;
    gap: 8px;
}

.btn-callout:hover {
    background-color: var(--black);
    filter: drop-shadow(5px 5px 10px rgba(216,216,216,0.6));
    color: white;
}

.btn-callout p {
    margin: 0;
    color: inherit;
    text-align: center;
    flex: 1;
}

/* Button container styling */
.dashboard-header .btn-2 {
    margin: 0 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Ensure all buttons have consistent styling */
a.btn-2, button.btn-2 {
    width: 240px;
    height: 50px;
    font-size: 20px;
    background-color: var(--accent);
    border: 1px solid var(--accent);
    border-radius: 12px;
    color: #ffffff !important;
    transition: all ease-in-out 0.2s;
    cursor: pointer;
    filter: drop-shadow(5px 5px 10px rgba(216, 216, 216, 0.2));
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    box-sizing: border-box;
}

a.btn-2:hover, button.btn-2:hover {
    background-color: var(--black) !important;
    filter: drop-shadow(5px 5px 10px rgba(216,216,216,0.6));
    color: white !important;
}

a.btn-2.btn-secondary, button.btn-2.btn-secondary {
    background-color: var(--darkkk-gray) !important;
    border-color: var(--darkkk-gray) !important;
}

a.btn-2.btn-secondary:hover, button.btn-2.btn-secondary:hover {
    background-color: var(--black) !important;
    border-color: var(--black) !important;
}

.gadget-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.gadget-card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    border: 2px solid var(--light-accent);
    transition: all 0.3s ease;
    height: 420px;
    display: flex;
    flex-direction: column;
}

.gadget-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(72, 77, 114, 0.2);
    border-color: var(--accent);
}

.gadget-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
    flex-shrink: 0;
}

.gadget-info {
    padding: 20px;
    display: flex;
    flex-direction: column;
    height: 200px;
    justify-content: space-between;
}

.gadget-content {
    flex: 1;
}

.gadget-info h3 {
    margin: 0 0 8px 0;
    color: var(--accent);
    font-size: 18px;
    font-weight: 600;
    line-height: 1.2;
}

.gadget-brand {
    color: var(--black);
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
}

.gadget-meta {
    color: var(--darkkk-gray);
    font-size: 13px;
    margin-bottom: 15px;
    line-height: 1.3;
}

.gadget-footer {
    text-align: center;
    flex-shrink: 0;
}

.gadget-price {
    font-size: 16px;
    font-weight: 700;
    color: var(--accent);
    display: block;
    margin-bottom: 12px;
}

.rental-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: var(--white);
    border-radius: 12px;
    overflow: hidden;
}

.rental-table th,
.rental-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid var(--light-accent);
}

.rental-table th {
    background-color: var(--accent);
    font-weight: 600;
    color: white;
    font-size: 16px;
}

.rental-table td {
    color: var(--black);
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-confirmed {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-active {
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #b3d7ff;
}

.status-completed {
    background-color: var(--dark-gray);
    color: var(--black);
    border: 1px solid var(--darkkk-gray);
}

.status-cancelled {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.btn-action {
    background-color: var(--accent);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
}

.btn-action:hover {
    background-color: var(--black);
    color: white;
}

.btn-rent {
    background-color: var(--light-accent);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-rent:hover {
    background-color: var(--accent);
    color: white;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}
</style>

<div class="dashboard-container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1>Welcome back, <?php echo htmlspecialchars($full_name); ?>!</h1>
        <p>Manage your rentals and discover new gadgets</p>
        <div style="margin-top: 30px;">
            <a href="product.php" class="btn-2">Browse Gadgets</a>
            <a href="logout.php" class="btn-2 btn-secondary">Logout</a>
        </div>
    </div>

    <!-- Dashboard Stats -->
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_requests ? $pending_requests->num_rows : 0; ?></div>
            <div>Pending Requests</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $active_rentals ? $active_rentals->num_rows : 0; ?></div>
            <div>Active Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $rental_history ? $rental_history->num_rows : 0; ?></div>
            <div>Completed Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $available_gadgets ? $available_gadgets->num_rows : 0; ?></div>
            <div>Available Gadgets</div>
        </div>
    </div>

    <!-- Rental Requests Section -->
    <div class="dashboard-section">
        <h2 class="section-title">Your Rental Requests</h2>
        <?php if ($pending_requests && $pending_requests->num_rows > 0): ?>
            <table class="rental-table">
                <thead>
                    <tr>
                        <th>Gadget</th>
                        <th>Requested Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $pending_requests->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($request['gadget_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($request['brand'] . ' ' . $request['model']); ?></small>
                            </td>
                            <td><?php echo formatDate($request['created_at']); ?></td>
                            <td><?php echo formatDate($request['start_date']); ?></td>
                            <td><?php echo formatDate($request['end_date']); ?></td>
                            <td><?php echo formatCurrency($request['total_amount']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="rental-details.php?id=<?php echo $request['id']; ?>" class="btn-action">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: var(--black); font-size: 18px;">
                No pending rental requests. <a href="product.php" style="color: var(--accent); text-decoration: none; font-weight: 600;">Browse gadgets</a> to make a rental request!
            </p>
        <?php endif; ?>
    </div>

    <!-- Active Rentals Section -->
    <div class="dashboard-section">
        <h2 class="section-title">Your Active Rentals</h2>
        <?php if ($active_rentals && $active_rentals->num_rows > 0): ?>
            <table class="rental-table">
                <thead>
                    <tr>
                        <th>Gadget</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rental = $active_rentals->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($rental['gadget_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?></small>
                            </td>
                            <td><?php echo formatDate($rental['start_date']); ?></td>
                            <td><?php echo formatDate($rental['end_date']); ?></td>
                            <td><?php echo formatCurrency($rental['total_amount']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $rental['status']; ?>">
                                    <?php echo ucfirst($rental['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="rental-details.php?id=<?php echo $rental['id']; ?>" class="btn-action">View Details</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: var(--black); font-size: 18px;">
                You don't have any active rentals. <a href="product.php" style="color: var(--accent); text-decoration: none; font-weight: 600;">Browse gadgets</a> to start renting!
            </p>
        <?php endif; ?>
    </div>

    <!-- Available Gadgets Section -->
    <div class="dashboard-section">
        <h2 class="section-title">Featured Available Gadgets</h2>
        <?php if ($available_gadgets && $available_gadgets->num_rows > 0): ?>
            <div class="gadget-grid">
                <?php while ($gadget = $available_gadgets->fetch_assoc()): ?>
                    <div class="gadget-card">
                        <img src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>" class="gadget-image">
                        <div class="gadget-info">
                            <div class="gadget-content">
                                <h3><?php echo htmlspecialchars($gadget['name']); ?></h3>
                                <div class="gadget-brand"><?php echo htmlspecialchars($gadget['brand'] . ' ' . $gadget['model']); ?></div>
                                <div class="gadget-meta">
                                    <?php echo htmlspecialchars($gadget['category_name']); ?><br>
                                    📍 <?php echo htmlspecialchars($gadget['location']); ?>
                                </div>
                            </div>
                            <div class="gadget-footer">
                                <span class="gadget-price">
                                    <?php echo formatCurrency($gadget['price_per_day']); ?>/day
                                </span>
                                <a href="gadget-details.php?id=<?php echo $gadget['id']; ?>" class="btn-rent">Rent Now</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div style="text-align: center; margin-top: 30px;">
                <a href="product.php" class="btn-2">View All Gadgets</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--black); font-size: 18px;">No gadgets available at the moment.</p>
        <?php endif; ?>
    </div>

    <!-- Rental History Section -->
    <div class="dashboard-section">
        <h2 class="section-title">Recent Rental History</h2>
        <?php if ($rental_history && $rental_history->num_rows > 0): ?>
            <table class="rental-table">
                <thead>
                    <tr>
                        <th>Gadget</th>
                        <th>Rental Period</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($rental = $rental_history->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($rental['gadget_name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?></small>
                            </td>
                            <td>
                                <?php echo formatDate($rental['start_date']); ?> - <?php echo formatDate($rental['end_date']); ?>
                            </td>
                            <td><?php echo formatCurrency($rental['total_amount']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $rental['status']; ?>">
                                    <?php echo ucfirst($rental['status']); ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($rental['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: var(--black); font-size: 18px;">No rental history found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
