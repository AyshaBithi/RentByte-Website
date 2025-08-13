<?php include('includes/header.php')?>

<!-- Sign-up Form Section -->
<section class="signup-section">
    <div class="signup-container">
        <h2>Login</h2>
        <form action="signup.php" method="POST"> <!-- Replace 'signup.php' with your form handling script -->
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-signup">Sign Up</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign In</a></p> 
        <button class="btn-gadget btn-herogadget" onclick="window.location.href='product.php'">
            <p>Login</p>
        
        </button>
    </div>
    
</section>

<?php include('includes/footer.php')?>
