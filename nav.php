<?php
// session_start();

// Function to check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

// Function to check if user is seller
function isSeller() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'seller';
}

// Function to check if user is buyer
function isBuyer() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'buyer';
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['username']);
}

// Function to display the username or account options
function displayUserOptions() {
    if (isLoggedIn()) {
        $username = htmlspecialchars($_SESSION['username']);
        echo '<li class="dropdown">';
        echo '<a href="#">' . $username . '</a>';
        echo '<div class="dropdown-content">';
        echo '<a href="logout.php">Logout</a>';
        echo '</div>';
        echo '</li>';
    } else {
        echo '<li class="dropdown">';
        echo '<a href="#">Account</a>';
        echo '<div class="dropdown-content">';
        echo '<a href="login.php">Login</a>';
        echo '<a href="register.php">Register</a>';
        echo '</div>';
        echo '</li>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="nav">
        <div class="nav-logo">
            <h2 class="logo">OEAS</h2>
        </div>
        <div class="nav-menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="news.php">News</a></li>
                <li><a href="auction.php">Auction</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isLoggedIn()) : ?>
                    <?php if (isAdmin()) : ?>
                        <li><a href="a_dashboard.php">Dashboard</a></li>
                        <li><a href="a_manage_user.php">Manage Users</a></li>
                        <li><a href="a_approve_product.php">Approve Products</a></li>
                        <li><a href="a_manage_auction.php">Manage Auctions</a></li>
                    <?php elseif (isSeller()) : ?>
                        <li><a href="s_dashboard.php">Dashboard</a></li>
                        <li><a href="s_create_product.php">Create Product</a></li>
                        <li><a href="s_manage_product.php">Manage Products</a></li>
                    <?php elseif (isBuyer()) : ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                <?php endif; ?>
                <?php displayUserOptions(); ?>
            </ul>
        </div>
    </div>
</body>
</html>