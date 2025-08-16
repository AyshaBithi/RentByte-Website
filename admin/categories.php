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
                
                if (empty($name)) {
                    $error_message = 'Category name is required.';
                } else {
                    // Check if category already exists
                    $check_query = "SELECT id FROM categories WHERE name = ?";
                    $check_result = executeQuery($check_query, [$name], 's');
                    
                    if ($check_result && $check_result->num_rows > 0) {
                        $error_message = 'Category with this name already exists.';
                    } else {
                        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
                        $result = executeQuery($query, [$name, $description], 'ss');
                        
                        if ($result) {
                            $success_message = 'Category added successfully!';
                        } else {
                            $error_message = 'Error adding category. Please try again.';
                        }
                    }
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $status = sanitizeInput($_POST['status']);
                
                if (empty($name)) {
                    $error_message = 'Category name is required.';
                } else {
                    // Check if category name already exists (excluding current category)
                    $check_query = "SELECT id FROM categories WHERE name = ? AND id != ?";
                    $check_result = executeQuery($check_query, [$name, $id], 'si');
                    
                    if ($check_result && $check_result->num_rows > 0) {
                        $error_message = 'Category with this name already exists.';
                    } else {
                        $query = "UPDATE categories SET name = ?, description = ?, status = ? WHERE id = ?";
                        $result = executeQuery($query, [$name, $description, $status, $id], 'sssi');
                        
                        if ($result) {
                            $success_message = 'Category updated successfully!';
                        } else {
                            $error_message = 'Error updating category. Please try again.';
                        }
                    }
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                
                // Check if category has gadgets
                $gadget_check = executeQuery("SELECT COUNT(*) as count FROM gadgets WHERE category_id = ?", [$id], 'i');
                $gadget_count = $gadget_check->fetch_assoc()['count'];
                
                if ($gadget_count > 0) {
                    $error_message = 'Cannot delete category that contains gadgets. Please move or delete the gadgets first.';
                } else {
                    $query = "DELETE FROM categories WHERE id = ?";
                    $result = executeQuery($query, [$id], 'i');
                    
                    if ($result) {
                        $success_message = 'Category deleted successfully!';
                    } else {
                        $error_message = 'Error deleting category. Please try again.';
                    }
                }
                break;
        }
    }
}

// Get categories with gadget counts
$categories_query = "SELECT c.*, COUNT(g.id) as gadget_count 
                    FROM categories c 
                    LEFT JOIN gadgets g ON c.id = g.category_id 
                    GROUP BY c.id 
                    ORDER BY c.name";
$categories_result = executeQuery($categories_query);

// Get category for editing if requested
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_query = "SELECT * FROM categories WHERE id = ?";
    $edit_result = executeQuery($edit_query, [$edit_id], 'i');
    if ($edit_result && $edit_result->num_rows > 0) {
        $edit_category = $edit_result->fetch_assoc();
    }
}

$page_title = 'Manage Categories';
include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="page-header">
        <h1>Manage Categories</h1>
        <button onclick="openModal('addCategoryModal')" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Category
        </button>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Categories Grid -->
    <div class="categories-grid">
        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
            <?php while ($category = $categories_result->fetch_assoc()): ?>
                <div class="category-card">
                    <div class="category-header">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <span class="status-badge status-<?php echo $category['status']; ?>">
                            <?php echo ucfirst($category['status']); ?>
                        </span>
                    </div>
                    
                    <div class="category-body">
                        <p><?php echo htmlspecialchars($category['description'] ?: 'No description provided.'); ?></p>
                        
                        <div class="category-stats">
                            <div class="stat-item">
                                <i class="fas fa-mobile-alt"></i>
                                <span><?php echo $category['gadget_count']; ?> Gadgets</span>
                            </div>
                            <div class="stat-item">
                                <i class="fas fa-calendar"></i>
                                <span>Created <?php echo formatDate($category['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="category-actions">
                        <a href="?edit=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <?php if ($category['gadget_count'] == 0): ?>
                            <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this category?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-sm btn-secondary" disabled title="Cannot delete category with gadgets">
                                <i class="fas fa-lock"></i> Protected
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-folder fa-3x"></i>
                <h3>No Categories Found</h3>
                <p>Start by creating your first gadget category.</p>
                <button onclick="openModal('addCategoryModal')" class="btn btn-primary">Add Category</button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><?php echo $edit_category ? 'Edit Category' : 'Add New Category'; ?></h2>
            <span class="close" onclick="closeModal('addCategoryModal')">&times;</span>
        </div>
        
        <form method="POST" class="category-form">
            <input type="hidden" name="action" value="<?php echo $edit_category ? 'edit' : 'add'; ?>">
            <?php if ($edit_category): ?>
                <input type="hidden" name="id" value="<?php echo $edit_category['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo $edit_category ? htmlspecialchars($edit_category['name']) : ''; ?>"
                       placeholder="e.g., Laptops, Smartphones, Cameras">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" 
                          placeholder="Brief description of this category..."><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
            </div>
            
            <?php if ($edit_category): ?>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="active" <?php echo ($edit_category['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($edit_category['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            <?php endif; ?>
            
            <div class="form-actions">
                <button type="button" onclick="closeModal('addCategoryModal')" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_category ? 'Update Category' : 'Add Category'; ?>
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

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.category-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.category-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.category-header h3 {
    margin: 0;
    color: #333;
}

.category-body {
    padding: 20px;
}

.category-body p {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.5;
}

.category-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #666;
    font-size: 14px;
}

.stat-item i {
    color: #667eea;
}

.category-actions {
    display: flex;
    gap: 10px;
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.status-active { background-color: #d4edda; color: #155724; }
.status-inactive { background-color: #f8d7da; color: #721c24; }

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
.btn-sm { padding: 6px 12px; font-size: 12px; }

.btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: translateY(-1px);
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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

.category-form {
    padding: 20px;
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
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.no-data i {
    color: #ccc;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        gap: 15px;
        align-items: stretch;
    }
    
    .category-stats {
        flex-direction: column;
        gap: 10px;
    }
    
    .category-actions {
        flex-direction: column;
    }
}
</style>

<script>
// Show edit modal if editing
<?php if ($edit_category): ?>
    document.addEventListener('DOMContentLoaded', function() {
        openModal('addCategoryModal');
    });
<?php endif; ?>
</script>

<?php include '../includes/admin_footer.php'; ?>
