<?php
require_once 'includes/config.php';

// Get featured gadgets for homepage
$featured_gadgets_query = "SELECT g.*, c.name as category_name
                          FROM gadgets g
                          JOIN categories c ON g.category_id = c.id
                          WHERE g.status = 'available'
                          ORDER BY g.created_at DESC LIMIT 6";
$featured_gadgets = executeQuery($featured_gadgets_query);

// Get success/error messages
$success_message = getSuccessMessage();
$error_message = getErrorMessage();

include('includes/header.php');
?>

    <!-- Alert Messages -->
    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <style>
        .alert {
            padding: 15px;
            margin: 20px auto;
            max-width: 1200px;
            border-radius: 5px;
            font-weight: 500;
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

        .gadget-brand {
            color: #666;
            font-size: 0.9em;
            margin: 5px 0 15px 0;
            font-weight: normal;
        }

        .no-gadgets-message {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            grid-column: 1 / -1;
        }

        .no-gadgets-message h3 {
            margin-bottom: 10px;
            color: #333;
        }
    </style>

    <!-- hero section -->
    <div class="hero-page">
        <div class="hero-headlines">
            <h1>
                Easy And Fast Way To Rent Gadgets with Rent<b class="accent">Byte </b>
            </h1>
            <p>
                We offer a wide variety of gadgets to suit your needs.
            </p>
            <a href="signup.php">
                <button class="btn-2 btn-hero" onclick="window.location.href='contact.html'">
                    Get Started
                </button>
            </a>
           
        </div>
        <img src="assets/img/hero_img.png" class="hero-page-img" alt="img">
    </div>    

    <!-- hero section end -->


























    <!-- about section -->

    <section class="about" id="about">
        <div class="about-container">
            <h1> 
                Your Premium Choice For Gadget Renting
            </h1>
            <p class="about-sub-line"> 
                Experience the ultimate convenience with ByteRent, where renting a gadget is as easy as a few clicks
            </p>
            
            <div class="about-info">
                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/img/efficiency (1).png" alt="img">
                    <h5>Efficiency</h5>
                    <p>
                        RentByte stands out for its stramlined rental process, ensuring
                        customers can quickly book their desired gadget
                    </p>
                </div>

                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/img/diversity.png" alt="img">
                    <h5>Diversity</h5>
                    <p>
                        RentByte boasts a diverse fleet of well-maintained gadgets,
                        catering to the varying needs of its customers
                    </p>
                </div>

                <div class="about-info-item">
                    <hr class="about-hr" />
                    <img src="assets/img/service.png" alt="img">
                    <h5>Exeptional Service</h5>
                    <p>
                        Beyond just providing gadgets, RentByte is also committed to 
                        delivering customer service at every touchpoint
                    </p>
                </div>
            </div>
        </div>
    </section>

<!-- about section end -->































<!-- products section -->

<section class="products" id="products">
    <h1>Our Product Collection</h1>
        <p class="products-sub-line">
            Choose from our wide range of gadgets
        </p>
        <div class="products-container">
            <?php if ($featured_gadgets && $featured_gadgets->num_rows > 0): ?>
                <?php while ($gadget = $featured_gadgets->fetch_assoc()): ?>
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
                            <p class="gadget-brand"><?php echo htmlspecialchars($gadget['brand'] . ' ' . $gadget['model']); ?></p>
                            <button class="btn-2 btn-gadget" onclick="window.location.href='gadget-details.php?id=<?php echo $gadget['id']; ?>'">
                                <p>Rent Now</p>
                            </button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-gadgets-message">
                    <h3>No gadgets available at the moment</h3>
                    <p>Please check back later for new arrivals!</p>
                </div>
            <?php endif; ?>
        </div>

        <button class="btn-gadget btn-herogadget" onclick="window.location.href='product.php'">
            <p>See All</p>
        
        </button>
    </section>



















    










    <!-- review section -->

    <section class="review" id="review">
        <h1>Hear what our clients say about us</h1>

        <div class="review-container">

            <!-- review 1 -->
            <div class="review-item">

                <div class="review-people">
                    <!-- <img src="assets/img/testimonial-1.jpg" alt="img"/> -->
                    <h5> Sarah. H</h5>
                </div>

                <p>
                    Renting with <b class="accent">RentByte</b> was an absolute breeze!
                    From booking online to picking up the gadget, everything was smooth and efficient
                </p>
            </div>


            <!-- review 2 -->
            <div class="review-item">

                <div class="review-people">
                    <!-- <img src="assets/img/testimonial-2.jpg" alt="img"/> -->
                    <h5>Micheal T.</h5>
                </div>

                <p>
                    I can't recommend <b class="accent">RentByte</b> enough!
                    Their diverse collection allowed me to choose the perfect one for my need
                </p>
                
            </div>


            <!-- review 3 -->
            <div class="review-item">

                <div class="review-people">
                    <!-- <img src="assets/img/testimonial-3.jpg" alt="img"/> -->
                    <h5>William A.</h5>
                </div>

                <p>
                    <b class="accent">RentByte</b> exceeded all my expectations! Their 
                    flexibility with drop offs and pickups made my renting easier
                </p>
                
            </div>
        </div>
    </section>


<!-- review section end -->



<?php include('includes/footer.php')?>