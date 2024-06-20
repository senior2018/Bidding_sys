<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch the number of registered users
$query_users = "SELECT COUNT(*) AS total_users FROM users";
$result_users = mysqli_query($conn, $query_users);
$total_users = mysqli_fetch_assoc($result_users)['total_users'];

// Fetch the number of logged-in users (active users)
$query_active_users = "SELECT COUNT(*) AS active_users FROM users WHERE last_login IS NOT NULL";
$result_active_users = mysqli_query($conn, $query_active_users);
$active_users = mysqli_fetch_assoc($result_active_users)['active_users'];

// Fetch the number of inactive users (not logged-in)
$query_inactive_users = "SELECT COUNT(*) AS inactive_users FROM users WHERE last_login IS NULL";
$result_inactive_users = mysqli_query($conn, $query_inactive_users);
$inactive_users = mysqli_fetch_assoc($result_inactive_users)['inactive_users'];

// Fetch the number of active auctions
$query_active_auctions = "SELECT COUNT(*) AS active_auctions FROM auctions WHERE status='ongoing'";
$result_active_auctions = mysqli_query($conn, $query_active_auctions);
$active_auctions = mysqli_fetch_assoc($result_active_auctions)['active_auctions'];

// Fetch notifications of who bid on which product
$query_bids = "SELECT b.id, u.username, p.name AS product_name, b.bid_amount, b.bid_time
               FROM bids b
               JOIN users u ON b.buyer_id = u.id
               JOIN products p ON b.product_id = p.id
               ORDER BY b.bid_time DESC";
$result_bids = mysqli_query($conn, $query_bids);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Admin Dashboard</h1>
        <div class="stats">
            <div class="stat-box">
                <h2>Total Registered Users</h2>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-box">
                <h2>Active Users</h2>
                <p><?php echo $active_users; ?></p>
            </div>
            <div class="stat-box">
                <h2>Inactive Users</h2>
                <p><?php echo $inactive_users; ?></p>
            </div>
            <div class="stat-box">
                <h2>Active Auctions</h2>
                <p><?php echo $active_auctions; ?></p>
            </div>
        </div>
        <div class="notifications">
            <h2>Bid Notifications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Product</th>
                        <th>Bid Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_bids)): ?>
                        <tr>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['product_name']; ?></td>
                            <td><?php echo $row['bid_amount']; ?></td>
                            <td><?php echo $row['bid_time']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>