<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <title>OEAS - Online Electronic Auction System</title>
</head>
<body>
    <div class="content">
        <?php include 'nav.php'; ?>
    </div>
    
    <div class="header">
        <h1>Welcome to OEAS</h1>
    </div>

    <div class="hero">
        <div class="hero-content">
            <h2>Online Electronic Auction System</h2>
            <p>Bid on your favorite items from the comfort of your home</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Go to Dashboard</a>
            <?php else: ?>
                <a href="register.php">Get Started</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="features">
        <div class="feature">
            <i class='bx bx-trophy'></i>
            <h3>Best Auctions</h3>
            <p>Participate in the best auctions with the highest quality products.</p>
        </div>
        <div class="feature">
            <i class='bx bx-shield'></i>
            <h3>Secure Bidding</h3>
            <p>Our platform ensures secure and transparent bidding for all users.</p>
        </div>
        <div class="feature">
            <i class='bx bx-support'></i>
            <h3>24/7 Support</h3>
            <p>We offer round-the-clock support to assist you with any issues.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 OEAS. All Rights Reserved.</p>
    </footer>
</body>
</html>
