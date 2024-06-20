<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit();
}

// Fetch available products for bidding
$query_products = "SELECT * FROM products WHERE status = 'active'";
$result_products = mysqli_query($conn, $query_products);

// Fetch user's bids
$user_id = $_SESSION['user_id'];
$query_user_bids = "SELECT b.id, p.name AS product_name, b.bid_amount, b.bid_time 
                    FROM bids b
                    JOIN products p ON b.product_id = p.id
                    WHERE b.buyer_id = $user_id
                    ORDER BY b.bid_time DESC";
$result_user_bids = mysqli_query($conn, $query_user_bids);

// Fetch notifications about auction results
$query_notifications = "SELECT p.name AS product_name, n.message, n.created_at 
                        FROM notifications n
                        JOIN products p ON n.product_id = p.id
                        WHERE n.user_id = $user_id
                        ORDER BY n.created_at DESC";
$result_notifications = mysqli_query($conn, $query_notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Buyer Dashboard</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Buyer Dashboard</h1>
        
        <div class="section">
            <h2>Your Bids</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Bid Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bid = mysqli_fetch_assoc($result_user_bids)): ?>
                        <tr>
                            <td><?php echo $bid['product_name']; ?></td>
                            <td><?php echo $bid['bid_amount']; ?></td>
                            <td><?php echo $bid['bid_time']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Notifications</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($notification = mysqli_fetch_assoc($result_notifications)): ?>
                        <tr>
                            <td><?php echo $notification['product_name']; ?></td>
                            <td><?php echo $notification['message']; ?></td>
                            <td><?php echo $notification['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>