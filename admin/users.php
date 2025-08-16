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
                $user_id = (int)$_POST['user_id'];
                $status = sanitizeInput($_POST['status']);
                
                $query = "UPDATE users SET status = ? WHERE id = ? AND role = 'user'";
                $result = executeQuery($query, [$status, $user_id], 'si');
                
                if ($result) {
                    $success_message = 'User status updated successfully!';
                } else {
                    $error_message = 'Error updating user status.';
                }
                break;
                
            case 'delete':
                $user_id = (int)$_POST['user_id'];
                
                // Check if user has active rentals
                $rental_check = executeQuery("SELECT COUNT(*) as count FROM rentals WHERE user_id = ? AND status IN ('confirmed', 'active')", [$user_id], 'i');
                $rental_count = $rental_check->fetch_assoc()['count'];
                
                if ($rental_count > 0) {
                    $error_message = 'Cannot delete user with active rentals.';
                } else {
                    $query = "DELETE FROM users WHERE id = ? AND role = 'user'";
                    $result = executeQuery($query, [$user_id], 'i');
                    
                    if ($result) {
                        $success_message = 'User deleted successfully!';
                    } else {
                        $error_message = 'Error deleting user.';
                    }
                }
                break;
        }
    }
}

// Get users with rental statistics
$users_query = "SELECT u.*, 
                COUNT(r.id) as total_rentals,
                SUM(CASE WHEN r.status IN ('confirmed', 'active') THEN 1 ELSE 0 END) as active_rentals,
                SUM(CASE WHEN r.status = 'completed' THEN r.total_amount ELSE 0 END) as total_spent
                FROM users u 
                LEFT JOIN rentals r ON u.id = r.user_id 
                WHERE u.role = 'user' 
                GROUP BY u.id 
                ORDER BY u.created_at DESC";
$users_result = executeQuery($users_query);

$page_title = 'Manage Users';
include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Users</h1>
        <div class="header-stats">
            <span class="stat-item">
                <strong><?php echo $users_result ? $users_result->num_rows : 0; ?></strong> Total Users
            </span>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Users Table -->
    <div class="table-container">
        <div class="table-header">
            <h2>All Users</h2>
            <div class="table-actions">
                <input type="text" id="searchInput" placeholder="Search users..." class="search-input">
                <button onclick="exportTable('usersTable', 'users')" class="btn btn-outline">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <?php if ($users_result && $users_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="admin-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Info</th>
                            <th>Contact</th>
                            <th>Rental Stats</th>
                            <th>Total Spent</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
                                            <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($user['email']); ?></div>
                                    <?php if ($user['phone']): ?>
                                        <small><?php echo htmlspecialchars($user['phone']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="rental-stats">
                                        <span class="stat-badge">
                                            <?php echo $user['total_rentals']; ?> Total
                                        </span>
                                        <?php if ($user['active_rentals'] > 0): ?>
                                            <span class="stat-badge active">
                                                <?php echo $user['active_rentals']; ?> Active
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo formatCurrency($user['total_spent'] ?: 0); ?></strong>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="status-select status-<?php echo $user['status']; ?>">
                                            <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="suspended" <?php echo $user['status'] == 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </form>
                                </td>
                                <td><?php echo formatDate($user['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="user-details.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($user['active_rentals'] == 0): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this user? This action cannot be undone.')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Cannot delete user with active rentals">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-users fa-3x"></i>
                <h3>No Users Found</h3>
                <p>No users have registered yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-container {
    max-width: 1200px;
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

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.rental-stats {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.stat-badge {
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    background: #e9ecef;
    color: #495057;
    display: inline-block;
}

.stat-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-select {
    padding: 4px 8px;
    border: 1px solid #ced4da;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
}

.status-select.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-select.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.status-select.status-suspended {
    background-color: #fff3cd;
    color: #856404;
}

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
.btn-danger { background-color: #dc3545; color: white; }
.btn-outline { background: transparent; border: 1px solid #667eea; color: #667eea; }
.btn-sm { padding: 6px 10px; font-size: 12px; }

.btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .table-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .search-input {
        width: 100%;
    }
    
    .user-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .rental-stats {
        flex-direction: row;
        flex-wrap: wrap;
    }
}
</style>

<script>
// Initialize search functionality
document.addEventListener('DOMContentLoaded', function() {
    searchTable('searchInput', 'usersTable');
});
</script>

<?php include '../includes/admin_footer.php'; ?>
