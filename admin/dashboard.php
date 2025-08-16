<?php
require_once '../includes/config.php';
requireAdmin();

// Get dashboard statistics
$stats = [];

// Total users
$users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$users_result = executeQuery($users_query);
$stats['users'] = $users_result ? $users_result->fetch_assoc()['total'] : 0;

// Total gadgets
$gadgets_query = "SELECT COUNT(*) as total FROM gadgets";
$gadgets_result = executeQuery($gadgets_query);
$stats['gadgets'] = $gadgets_result ? $gadgets_result->fetch_assoc()['total'] : 0;

// Active rentals
$active_rentals_query = "SELECT COUNT(*) as total FROM rentals WHERE status IN ('confirmed', 'active')";
$active_rentals_result = executeQuery($active_rentals_query);
$stats['active_rentals'] = $active_rentals_result ? $active_rentals_result->fetch_assoc()['total'] : 0;

// Total revenue
$revenue_query = "SELECT SUM(total_amount) as total FROM rentals WHERE status = 'completed'";
$revenue_result = executeQuery($revenue_query);
$stats['revenue'] = $revenue_result ? ($revenue_result->fetch_assoc()['total'] ?: 0) : 0;

// Recent rentals
$recent_rentals_query = "SELECT r.*, u.username, u.full_name, g.name as gadget_name 
                        FROM rentals r 
                        JOIN users u ON r.user_id = u.id 
                        JOIN gadgets g ON r.gadget_id = g.id 
                        ORDER BY r.created_at DESC LIMIT 10";
$recent_rentals = executeQuery($recent_rentals_query);

// Low stock gadgets (for future enhancement)
$low_stock_query = "SELECT * FROM gadgets WHERE status = 'maintenance' OR status = 'inactive' LIMIT 5";
$low_stock = executeQuery($low_stock_query);

$page_title = 'Admin Dashboard';
include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</p>
    </div>

    <!-- Dashboard Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['users']); ?></h3>
                <p>Total Users</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📱</div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['gadgets']); ?></h3>
                <p>Total Gadgets</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🔄</div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['active_rentals']); ?></h3>
                <p>Active Rentals</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-info">
                <h3><?php echo formatCurrency($stats['revenue']); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="gadgets.php" class="action-btn">
                <span class="btn-icon">📱</span>
                Manage Gadgets
            </a>
            <a href="users.php" class="action-btn">
                <span class="btn-icon">👥</span>
                Manage Users
            </a>
            <a href="rentals.php" class="action-btn">
                <span class="btn-icon">📋</span>
                Manage Rentals
            </a>
            <a href="categories.php" class="action-btn">
                <span class="btn-icon">📂</span>
                Manage Categories
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="dashboard-section">
        <h2>Recent Rentals</h2>
        <?php if ($recent_rentals && $recent_rentals->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Gadget</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rental = $recent_rentals->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $rental['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($rental['full_name']); ?></strong><br>
                                    <small>@<?php echo htmlspecialchars($rental['username']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($rental['gadget_name']); ?></td>
                                <td><?php echo formatDate($rental['start_date']); ?></td>
                                <td><?php echo formatDate($rental['end_date']); ?></td>
                                <td><?php echo formatCurrency($rental['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $rental['status']; ?>">
                                        <?php echo ucfirst($rental['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="rental-details.php?id=<?php echo $rental['id']; ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="section-footer">
                <a href="rentals.php" class="btn btn-outline">View All Rentals</a>
            </div>
        <?php else: ?>
            <p class="no-data">No recent rentals found.</p>
        <?php endif; ?>
    </div>

    <!-- System Alerts -->
    <?php if ($low_stock && $low_stock->num_rows > 0): ?>
        <div class="dashboard-section">
            <h2>System Alerts</h2>
            <div class="alert-list">
                <?php while ($gadget = $low_stock->fetch_assoc()): ?>
                    <div class="alert alert-warning">
                        <strong><?php echo htmlspecialchars($gadget['name']); ?></strong> 
                        is currently <?php echo $gadget['status']; ?>
                        <a href="gadgets.php?edit=<?php echo $gadget['id']; ?>" class="alert-action">Fix</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 20px;
}

.stat-icon {
    font-size: 2.5em;
    opacity: 0.8;
}

.stat-info h3 {
    font-size: 2em;
    margin: 0;
    color: #333;
}

.stat-info p {
    margin: 5px 0 0 0;
    color: #666;
}

.quick-actions {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 20px;
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    font-weight: 500;
    transition: all 0.3s;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.btn-icon {
    font-size: 1.2em;
}

.dashboard-section {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.dashboard-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
}

.table-responsive {
    overflow-x: auto;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.admin-table th,
.admin-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.admin-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.status-pending { background-color: #fff3cd; color: #856404; }
.status-confirmed { background-color: #cce5ff; color: #004085; }
.status-active { background-color: #d4edda; color: #155724; }
.status-completed { background-color: #d1ecf1; color: #0c5460; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
}

.btn-sm { padding: 6px 12px; font-size: 12px; }
.btn-primary { background-color: #007bff; color: white; }
.btn-outline { background: transparent; border: 1px solid #667eea; color: #667eea; }

.section-footer {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 40px;
}

.alert-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.alert {
    padding: 15px;
    border-radius: 5px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
}

.alert-action {
    background: #ffc107;
    color: #212529;
    padding: 5px 10px;
    border-radius: 3px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
}
</style>

<?php include '../includes/admin_footer.php'; ?>
