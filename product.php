<?php
require_once 'includes/config.php';

// Get categories
$categories_query = "SELECT * FROM categories WHERE status = 'active' ORDER BY name";
$categories_result = executeQuery($categories_query);

// Get selected category
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Get gadgets based on category
if ($selected_category > 0) {
    $gadgets_query = "SELECT g.*, c.name as category_name
                     FROM gadgets g
                     JOIN categories c ON g.category_id = c.id
                     WHERE g.category_id = ? AND g.status = 'available'
                     ORDER BY g.name";
    $gadgets_result = executeQuery($gadgets_query, [$selected_category], 'i');
} else {
    $gadgets_query = "SELECT g.*, c.name as category_name
                     FROM gadgets g
                     JOIN categories c ON g.category_id = c.id
                     WHERE g.status = 'available'
                     ORDER BY c.name, g.name";
    $gadgets_result = executeQuery($gadgets_query);
}

include('includes/header.php');
?>

<!-- product page -->
<section class="products" id="products">
    <h1>Our Product Collection</h1>
    <p class="products-sub-line">
        Choose from our wide range of gadgets
    </p>

    <!-- Category Filter -->
    <?php if ($categories_result && $categories_result->num_rows > 0): ?>
        <div style="text-align: center; margin: 30px 0;">
            <div style="display: inline-flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                <a href="product.php" style="padding: 8px 16px; border: 2px solid var(--accent); background: <?php echo $selected_category == 0 ? 'var(--accent)' : 'white'; ?>; color: <?php echo $selected_category == 0 ? 'white' : 'var(--accent)'; ?>; text-decoration: none; border-radius: 20px; font-size: 14px; transition: all 0.3s;">All Categories</a>
                <?php $categories_result->data_seek(0); ?>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <a href="product.php?category=<?php echo $category['id']; ?>"
                       style="padding: 8px 16px; border: 2px solid var(--accent); background: <?php echo $selected_category == $category['id'] ? 'var(--accent)' : 'white'; ?>; color: <?php echo $selected_category == $category['id'] ? 'white' : 'var(--accent)'; ?>; text-decoration: none; border-radius: 20px; font-size: 14px; transition: all 0.3s;">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="products-container">
        <?php if ($gadgets_result && $gadgets_result->num_rows > 0): ?>
            <?php while ($gadget = $gadgets_result->fetch_assoc()): ?>
                <div class="products-gadget-item">
                    <img class="gadget-pic" src="<?php echo htmlspecialchars($gadget['image']); ?>" alt="<?php echo htmlspecialchars($gadget['name']); ?>">
                    <div class="gadget-info-container">
                        <div class="gadget-info">
                            <div class="gadget-price">
                                <h5><?php echo formatCurrency($gadget['price_per_day']); ?></h5>
                                <h6>/Day</h6>
                            </div>
                            <div class="gadget-location">
                                <i class="fa-solid fa-location-dot"></i>
                                <h6><?php echo htmlspecialchars($gadget['location']); ?></h6>
                            </div>
                        </div>
                        <h2><?php echo htmlspecialchars($gadget['name']); ?></h2>
                        <button class="btn-2 btn-gadget" onclick="window.location.href='gadget-details.php?id=<?php echo $gadget['id']; ?>'">
                            <p>Rent Now</p>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; color: #666; grid-column: 1 / -1;">
                <h3>No gadgets found</h3>
                <p>There are no available gadgets in this category at the moment.</p>
                <a href="product.php" style="padding: 8px 16px; border: 2px solid var(--accent); background: var(--accent); color: white; text-decoration: none; border-radius: 20px;">View All Categories</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?>
