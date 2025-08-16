<?php
require_once '../includes/config.php';
requireAdmin();

$error_message = '';
$success_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_status':
                $rental_id = (int)$_POST['rental_id'];
                $status = sanitizeInput($_POST['status']);
                $notes = sanitizeInput($_POST['notes']);
                
                // Update rental status
                $query = "UPDATE rentals SET status = ? WHERE id = ?";
                $result = executeQuery($query, [$status, $rental_id], 'si');
                
                if ($result) {
                    // Add to rental history
                    $history_query = "INSERT INTO rental_history (rental_id, status, notes, changed_by) VALUES (?, ?, ?, ?)";
                    executeQuery($history_query, [$rental_id, $status, $notes, $_SESSION['user_id']], 'issi');
                    
                    // Update gadget status if rental is completed or cancelled
                    if ($status == 'completed' || $status == 'cancelled') {
                        $gadget_query = "UPDATE gadgets SET status = 'available' WHERE id = (SELECT gadget_id FROM rentals WHERE id = ?)";
                        executeQuery($gadget_query, [$rental_id], 'i');
                    } elseif ($status == 'active') {
                        $gadget_query = "UPDATE gadgets SET status = 'rented' WHERE id = (SELECT gadget_id FROM rentals WHERE id = ?)";
                        executeQuery($gadget_query, [$rental_id], 'i');
                    }
                    
                    $success_message = 'Rental status updated successfully!';
                } else {
                    $error_message = 'Error updating rental status.';
                }
                break;
        }
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$date_filter = isset($_GET['date']) ? sanitizeInput($_GET['date']) : '';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "r.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($date_filter) {
    $where_conditions[] = "DATE(r.created_at) = ?";
    $params[] = $date_filter;
    $param_types .= 's';
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get rentals with user and gadget information
$rentals_query = "SELECT r.*, u.username, u.full_name, u.email, u.phone, 
                  g.name as gadget_name, g.brand, g.model, g.image
                  FROM rentals r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN gadgets g ON r.gadget_id = g.id 
                  $where_clause
                  ORDER BY r.created_at DESC";

$rentals_result = executeQuery($rentals_query, $params, $param_types);

// Get status counts for filter buttons
$status_counts = [];
$status_query = "SELECT status, COUNT(*) as count FROM rentals GROUP BY status";
$status_result = executeQuery($status_query);
if ($status_result) {
    while ($row = $status_result->fetch_assoc()) {
        $status_counts[$row['status']] = $row['count'];
    }
}

$page_title = 'Manage Rentals';
include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Rentals</h1>
        <div class="header-stats">
            <span class="stat-item">
                <strong><?php echo $rentals_result ? $rentals_result->num_rows : 0; ?></strong> 
                <?php echo $status_filter ? ucfirst($status_filter) : 'Total'; ?> Rentals
            </span>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Filter Buttons -->
    <div class="filter-section">
        <div class="status-filters">
            <a href="rentals.php" class="filter-btn <?php echo !$status_filter ? 'active' : ''; ?>">
                All Rentals
            </a>
            <a href="rentals.php?status=pending" class="filter-btn <?php echo $status_filter == 'pending' ? 'active' : ''; ?>">
                Pending (<?php echo $status_counts['pending'] ?? 0; ?>)
            </a>
            <a href="rentals.php?status=confirmed" class="filter-btn <?php echo $status_filter == 'confirmed' ? 'active' : ''; ?>">
                Confirmed (<?php echo $status_counts['confirmed'] ?? 0; ?>)
            </a>
            <a href="rentals.php?status=active" class="filter-btn <?php echo $status_filter == 'active' ? 'active' : ''; ?>">
                Active (<?php echo $status_counts['active'] ?? 0; ?>)
            </a>
            <a href="rentals.php?status=completed" class="filter-btn <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
                Completed (<?php echo $status_counts['completed'] ?? 0; ?>)
            </a>
            <a href="rentals.php?status=cancelled" class="filter-btn <?php echo $status_filter == 'cancelled' ? 'active' : ''; ?>">
                Cancelled (<?php echo $status_counts['cancelled'] ?? 0; ?>)
            </a>
        </div>
        
        <div class="date-filter">
            <input type="date" id="dateFilter" value="<?php echo $date_filter; ?>" onchange="filterByDate(this.value)">
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="table-container">
        <div class="table-header">
            <h2>Rental Records</h2>
            <div class="table-actions">
                <input type="text" id="searchInput" placeholder="Search rentals..." class="search-input">
                <button onclick="exportTable('rentalsTable', 'rentals')" class="btn btn-outline">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <?php if ($rentals_result && $rentals_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="admin-table" id="rentalsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Gadget</th>
                            <th>Rental Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($rental = $rentals_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $rental['id']; ?></td>
                                <td>
                                    <div class="customer-info">
                                        <strong><?php echo htmlspecialchars($rental['full_name']); ?></strong><br>
                                        <small>@<?php echo htmlspecialchars($rental['username']); ?></small><br>
                                        <small><?php echo htmlspecialchars($rental['email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div class="gadget-info">
                                        <img src="../<?php echo htmlspecialchars($rental['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($rental['gadget_name']); ?>" 
                                             class="gadget-thumb">
                                        <div>
                                            <strong><?php echo htmlspecialchars($rental['gadget_name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($rental['brand'] . ' ' . $rental['model']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="rental-period">
                                        <strong><?php echo formatDate($rental['start_date']); ?></strong><br>
                                        <small>to</small><br>
                                        <strong><?php echo formatDate($rental['end_date']); ?></strong><br>
                                        <small>(<?php echo $rental['total_days']; ?> days)</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-info">
                                        <strong><?php echo formatCurrency($rental['total_amount']); ?></strong><br>
                                        <small><?php echo formatCurrency($rental['price_per_day']); ?>/day</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $rental['status']; ?>">
                                        <?php echo ucfirst($rental['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($rental['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="openStatusModal(<?php echo $rental['id']; ?>, '<?php echo $rental['status']; ?>')" 
                                                class="btn btn-sm btn-primary" title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="rental-details.php?id=<?php echo $rental['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-clipboard-list fa-3x"></i>
                <h3>No Rentals Found</h3>
                <p>No rental records match your current filters.</p>
                <?php if ($status_filter || $date_filter): ?>
                    <a href="rentals.php" class="btn btn-primary">Clear Filters</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Update Rental Status</h2>
            <span class="close" onclick="closeModal('statusModal')">&times;</span>
        </div>
        
        <form method="POST" id="statusForm">
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="rental_id" id="modalRentalId">
            
            <div class="form-group">
                <label for="modalStatus">New Status:</label>
                <select id="modalStatus" name="status" required>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="modalNotes">Notes (Optional):</label>
                <textarea id="modalNotes" name="notes" rows="3" placeholder="Add any notes about this status change..."></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" onclick="closeModal('statusModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e9ecef;
}

.header-stats {
    display: flex;
    gap: 20px;
}

.stat-item {
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 5px;
    font-size: 14px;
}

.filter-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.status-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 2px solid #e9ecef;
    background: white;
    color: #333;
    text-decoration: none;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.date-filter input {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 5px;
}

.table-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.table-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    width: 250px;
}

.customer-info,
.gadget-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.gadget-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
}

.rental-period,
.amount-info {
    text-align: center;
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

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary { background-color: #007bff; color: white; }
.btn-secondary { background-color: #6c757d; color: white; }
.btn-info { background-color: #17a2b8; color: white; }
.btn-outline { background: transparent; border: 1px solid #667eea; color: #667eea; }
.btn-sm { padding: 6px 10px; font-size: 12px; }

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e9ecef;
}

.close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 20px;
    padding: 0 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 14px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 20px;
    border-top: 1px solid #e9ecef;
}

.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-data i {
    color: #ccc;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .filter-section {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .status-filters {
        justify-content: center;
    }
    
    .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .search-input {
        width: 100%;
    }
    
    .customer-info,
    .gadget-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<script>
// Initialize search functionality
document.addEventListener('DOMContentLoaded', function() {
    searchTable('searchInput', 'rentalsTable');
});

function openStatusModal(rentalId, currentStatus) {
    document.getElementById('modalRentalId').value = rentalId;
    document.getElementById('modalStatus').value = currentStatus;
    document.getElementById('modalNotes').value = '';
    openModal('statusModal');
}

function filterByDate(date) {
    const url = new URL(window.location);
    if (date) {
        url.searchParams.set('date', date);
    } else {
        url.searchParams.delete('date');
    }
    window.location = url;
}
</script>

<?php include '../includes/admin_footer.php'; ?>
