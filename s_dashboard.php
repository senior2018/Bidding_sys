<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is a seller
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header('Location: ../login.php');
    exit();
}

// Fetch seller's products
$seller_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
$query_products = "SELECT * FROM products WHERE seller_id = '$seller_id'";
$result_products = mysqli_query($conn, $query_products);

// Fetch bids on seller's products
$query_bids = "SELECT b.id, p.name AS product_name, u.username AS bidder, b.bid_amount, b.bid_time AS created_at 
               FROM bids b
               JOIN products p ON b.product_id = p.id
               JOIN users u ON b.buyer_id = u.id
               WHERE p.seller_id = '$seller_id'
               ORDER BY b.bid_time DESC";
$result_bids = mysqli_query($conn, $query_bids);

// Fetch notifications about auction results for seller's products
$query_notifications = "SELECT p.name AS product_name, n.message, n.created_at 
                        FROM notifications n
                        JOIN products p ON n.product_id = p.id
                        WHERE p.seller_id = '$seller_id'
                        ORDER BY n.created_at DESC";
$result_notifications = mysqli_query($conn, $query_notifications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Seller Dashboard</title>
    <script src="assets/js/script.js"></script>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Seller Dashboard</h1>
        
        <div class="section">
            <h2>Your Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Start Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($result_products)): ?>
                        <tr>
                            <td><?php echo $product['name']; ?></td>
                            <td>
                                <div id="description-<?php echo $product['id']; ?>" class="description" style="display: none;">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </div>
                                <span id="show-button-<?php echo $product['id']; ?>" class="show-hide-button" onclick="toggleDescription(<?php echo $product['id']; ?>)">Show</span>
                                <span id="hide-button-<?php echo $product['id']; ?>" class="show-hide-button" style="display:none;" onclick="toggleDescription(<?php echo $product['id']; ?>)">Hide</span>
                            </td>
                            <td><?php echo $product['starting_price']; ?></td>
                            <td><?php echo $product['status']; ?></td>
                            <td><a href="s_manage_product.php?product_id=<?php echo $product['id']; ?>">Manage</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Bids on Your Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Bidder</th>
                        <th>Bid Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bid = mysqli_fetch_assoc($result_bids)): ?>
                        <tr>
                            <td><?php echo $bid['product_name']; ?></td>
                            <td><?php echo $bid['bidder']; ?></td>
                            <td><?php echo $bid['bid_amount']; ?></td>
                            <td><?php echo $bid['created_at']; ?></td>
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
