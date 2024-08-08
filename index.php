<?php session_start() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="assets/css/style.css">
    <title>OEAS - Online Electronic Auction System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
        }
        .header {
            background: rgba(0, 0, 0, 0.1);
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .hero {
            background: url('assets/images/auction-bg.jpg') no-repeat center center/cover;
            height: 500px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .hero h2 {
            font-size: 3em;
            margin: 0 0 20px;
        }
        .hero p {
            font-size: 1.2em;
            margin: 0 0 30px;
        }
        .hero a {
            background: #ff7200;
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
        }
        .hero a:hover {
            background: #218838;
        }
        .features {
            display: flex;
            justify-content: space-around;
            padding: 50px 0;
            background: #fff;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
        .feature {
            text-align: center;
            padding: 20px;
            max-width: 300px;
        }
        .feature i {
            font-size: 3em;
            color: #007bff;
            margin-bottom: 10px;
        }
        .feature h3 {
            margin: 10px 0;
            font-size: 1.5em;
        }
        .feature p {
            color: #666;
        }
        footer {
            background: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }
        footer p {
            margin: 0;
        }
    </style>
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
            <a href="register.php">Get Started</a>
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
