<?php
require_once '../includes/config.php';
requireAdmin();

$error_message = '';
$success_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $category_id = (int)$_POST['category_id'];
                $price_per_day = (float)$_POST['price_per_day'];
                $location = sanitizeInput($_POST['location']);
                $brand = sanitizeInput($_POST['brand']);
                $model = sanitizeInput($_POST['model']);
                $specifications = sanitizeInput($_POST['specifications']);
                $condition_note = sanitizeInput($_POST['condition_note']);
                $image = sanitizeInput($_POST['image']);
                
                if (empty($name) || empty($description) || $category_id <= 0 || $price_per_day <= 0) {
                    $error_message = 'Please fill in all required fields.';
                } else {
                    $query = "INSERT INTO gadgets (name, description, category_id, price_per_day, location, brand, model, specifications, condition_note, image) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $result = executeQuery($query, [$name, $description, $category_id, $price_per_day, $location, $brand, $model, $specifications, $condition_note, $image], 'ssidssssss');
                    
                    if ($result) {
                        $success_message = 'Gadget added successfully!';
                    } else {
                        $error_message = 'Error adding gadget. Please try again.';
                    }
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $category_id = (int)$_POST['category_id'];
                $price_per_day = (float)$_POST['price_per_day'];
                $location = sanitizeInput($_POST['location']);
                $brand = sanitizeInput($_POST['brand']);
                $model = sanitizeInput($_POST['model']);
                $specifications = sanitizeInput($_POST['specifications']);
                $condition_note = sanitizeInput($_POST['condition_note']);
                $image = sanitizeInput($_POST['image']);
                $status = sanitizeInput($_POST['status']);
                
                if (empty($name) || empty($description) || $category_id <= 0 || $price_per_day <= 0) {
                    $error_message = 'Please fill in all required fields.';
                } else {
                    $query = "UPDATE gadgets SET name = ?, description = ?, category_id = ?, price_per_day = ?, location = ?, brand = ?, model = ?, specifications = ?, condition_note = ?, image = ?, status = ? WHERE id = ?";
                    $result = executeQuery($query, [$name, $description, $category_id, $price_per_day, $location, $brand, $model, $specifications, $condition_note, $image, $status, $id], 'ssidsssssssi');
                    
                    if ($result) {
                        $success_message = 'Gadget updated successfully!';
                    } else {
                        $error_message = 'Error updating gadget. Please try again.';
                    }
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $query = "DELETE FROM gadgets WHERE id = ?";
                $result = executeQuery($query, [$id], 'i');
                
                if ($result) {
                    $success_message = 'Gadget deleted successfully!';
                } else {
                    $error_message = 'Error deleting gadget. Please try again.';
                }
                break;
        }
    }
}

// Get gadgets with category names
$gadgets_query = "SELECT g.*, c.name as category_name 
                 FROM gadgets g 
                 LEFT JOIN categories c ON g.category_id = c.id 
                 ORDER BY g.created_at DESC";
$gadgets_result = executeQuery($gadgets_query);

// Get categories for dropdown
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY name";
$categories_result = executeQuery($categories_query);

// Get gadget for editing if requested
$edit_gadget = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = "SELECT * FROM gadgets WHERE id = ?";
    $edit_result = executeQuery($edit_query, [$edit_id], 'i');
    if ($edit_result && $edit_result->num_rows > 0) {
        $edit_gadget = $edit_result->fetch_assoc();
    }
}

$page_title = 'Manage Gadgets';
include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Gadgets</h1>
        <button onclick="openModal('addGadgetModal')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Gadget
        </button>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Gadgets Table -->
    <div class="table-container">
        <div class="table-header">
            <h2>All Gadgets</h2>
            <div class="table-actions">
                <input type="text" id="searchInput" placeholder="Search gadgets..." class="search-input">
                <button onclick="exportTable('gadgetsTable', 'gadgets')" class="btn btn-outline">
                    <i class="fas fa-download"></i> Export
                </button>
            </div>
        </div>

        <?php if ($gadgets_result && $gadgets_result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="admin-table" id="gadgetsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Brand/Model</th>
                            <th>Price/Day</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($gadget = $gadgets_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $gadget['id']; ?></td>
                                <td>
                                    <img src="../<?php echo htmlspecialchars($gadget['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($gadget['name']); ?>" 
                                         class="gadget-thumb">
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($gadget['name']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($gadget['category_name'] ?: 'Uncategorized'); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($gadget['brand']); ?><br>
                                    <small><?php echo htmlspecialchars($gadget['model']); ?></small>
                                </td>
                                <td><?php echo formatCurrency($gadget['price_per_day']); ?></td>
                                <td><?php echo htmlspecialchars($gadget['location']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $gadget['status']; ?>">
                                        <?php echo ucfirst($gadget['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?edit=<?php echo $gadget['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete()">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $gadget['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-mobile-alt fa-3x"></i>
                <h3>No Gadgets Found</h3>
                <p>Start by adding your first gadget to the inventory.</p>
                <button onclick="openModal('addGadgetModal')" class="btn btn-primary">Add Gadget</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Gadget Modal -->
<div id="addGadgetModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php echo $edit_gadget ? 'Edit Gadget' : 'Add New Gadget'; ?></h2>
            <span class="close" onclick="closeModal('addGadgetModal')">&times;</span>
        </div>
        
        <form method="POST" class="gadget-form">
            <input type="hidden" name="action" value="<?php echo $edit_gadget ? 'edit' : 'add'; ?>">
            <?php if ($edit_gadget): ?>
                <input type="hidden" name="id" value="<?php echo $edit_gadget['id']; ?>">
            <?php endif; ?>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="name">Gadget Name *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo $edit_gadget ? htmlspecialchars($edit_gadget['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php if ($categories_result): ?>
                            <?php $categories_result->data_seek(0); ?>
                            <?php while ($category = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                        <?php echo ($edit_gadget && $edit_gadget['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required rows="3"><?php echo $edit_gadget ? htmlspecialchars($edit_gadget['description']) : ''; ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" 
                           value="<?php echo $edit_gadget ? htmlspecialchars($edit_gadget['brand']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" 
                           value="<?php echo $edit_gadget ? htmlspecialchars($edit_gadget['model']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price_per_day">Price per Day ($) *</label>
                    <input type="number" id="price_per_day" name="price_per_day" step="0.01" min="0" required 
                           value="<?php echo $edit_gadget ? $edit_gadget['price_per_day'] : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="location">Location *</label>
                    <input type="text" id="location" name="location" required 
                           value="<?php echo $edit_gadget ? htmlspecialchars($edit_gadget['location']) : ''; ?>">
                </div>
            </div>
            
            <?php if ($edit_gadget): ?>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="available" <?php echo ($edit_gadget['status'] == 'available') ? 'selected' : ''; ?>>Available</option>
                        <option value="rented" <?php echo ($edit_gadget['status'] == 'rented') ? 'selected' : ''; ?>>Rented</option>
                        <option value="maintenance" <?php echo ($edit_gadget['status'] == 'maintenance') ? 'selected' : ''; ?>>Maintenance</option>
                        <option value="inactive" <?php echo ($edit_gadget['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="image">Image URL</label>
                <input type="text" id="image" name="image" 
                       value="<?php echo $edit_gadget ? htmlspecialchars($edit_gadget['image']) : ''; ?>"
                       placeholder="assets/img/gadget-name.jpg">
            </div>
            
            <div class="form-group">
                <label for="specifications">Specifications</label>
                <textarea id="specifications" name="specifications" rows="3"><?php echo $edit_gadget ? htmlspecialchars($edit_gadget['specifications']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="condition_note">Condition Notes</label>
                <textarea id="condition_note" name="condition_note" rows="2"><?php echo $edit_gadget ? htmlspecialchars($edit_gadget['condition_note']) : ''; ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" onclick="closeModal('addGadgetModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_gadget ? 'Update Gadget' : 'Add Gadget'; ?>
                </button>
            </div>
        </form>
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

.gadget-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 5px;
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
    margin: 5% auto;
    padding: 0;
    border-radius: 10px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
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

.gadget-form {
    padding: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ced4da;
    border-radius: 5px;
    font-size: 14px;
}

.form-group textarea {
    resize: vertical;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 30px;
    padding-top: 20px;
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
    .form-row {
        grid-template-columns: 1fr;
    }
    
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
}
</style>

<script>
// Initialize search functionality
document.addEventListener('DOMContentLoaded', function() {
    searchTable('searchInput', 'gadgetsTable');
    
    // Show edit modal if editing
    <?php if ($edit_gadget): ?>
        openModal('addGadgetModal');
    <?php endif; ?>
});
</script>

<?php include '../includes/admin_footer.php'; ?>
