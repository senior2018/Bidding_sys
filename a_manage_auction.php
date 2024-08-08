<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch all approved products for selection
$product_query = "SELECT * FROM products WHERE status = 'approved'";
$product_result = mysqli_query($conn, $product_query);

// Handle form submission for adding, editing, deleting, starting, and ending auctions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_auction'])) {
        $product_id = $_POST['product_id'];
        $start_time = $_POST['start_time'];
        $duration = $_POST['duration'];

        // Insert auction parameters into the database
        $query = "INSERT INTO auctions (product_id, start_time, duration) VALUES ('$product_id', '$start_time', '$duration')";
        mysqli_query($conn, $query);

    } elseif (isset($_POST['edit_auction'])) {
        $auction_id = $_POST['auction_id'];
        $start_time = $_POST['start_time'];
        $duration = $_POST['duration'];

        // Update auction parameters in the database
        $query = "UPDATE auctions SET start_time = '$start_time', duration = '$duration' WHERE id = '$auction_id'";
        mysqli_query($conn, $query);

    } elseif (isset($_POST['delete_auction'])) {
        $auction_id = $_POST['auction_id'];

        // Delete auction from the database
        $query = "DELETE FROM auctions WHERE id = '$auction_id'";
        mysqli_query($conn, $query);

    } elseif (isset($_POST['end_auction'])) {
        $auction_id = $_POST['auction_id'];
        $product_id = $_POST['product_id'];

        endAuction($product_id);

    } elseif (isset($_POST['start_auction'])) {
        $auction_id = $_POST['auction_id'];

        // Update auction status to 'ongoing' and set start_time to current time
        $current_time = date('Y-m-d H:i:s');
        $query = "UPDATE auctions SET status = 'ongoing', start_time = '$current_time' WHERE id = '$auction_id'";
        mysqli_query($conn, $query);
    }

    header('Location: a_manage_auction.php');
    exit();
}

// Fetch all auctions for display
$auction_query = "SELECT a.id AS auction_id, p.id AS product_id, p.name, a.start_time, a.duration, a.status 
                  FROM auctions a 
                  JOIN products p ON a.product_id = p.id";
$auction_result = mysqli_query($conn, $auction_query);

function endAuction($product_id) {
    global $conn;

    // Get the highest bid
    $query = "SELECT buyer_id, bid_amount FROM bids WHERE product_id = $product_id ORDER BY bid_amount DESC LIMIT 1";
    $result = mysqli_query($conn, $query);
    $highest_bid = mysqli_fetch_assoc($result);

    if ($highest_bid) {
        $winner_id = $highest_bid['buyer_id'];
        $winning_bid = $highest_bid['bid_amount'];

        // Update product status to 'sold_out'
        $update_query = "UPDATE products SET status = 'sold_out', max_bid_amount = $winning_bid WHERE id = $product_id";
        mysqli_query($conn, $update_query);

        // Update auction status to 'ended'
        $update_auction_query = "UPDATE auctions SET status = 'ended' WHERE product_id = $product_id";
        mysqli_query($conn, $update_auction_query);

        // Notify the winner
        notifyWinner($winner_id, $product_id, $winning_bid);
    }
}

function notifyWinner($user_id, $product_id, $bid_amount) {
    global $conn;

    $message = "Congratulations! You have won the auction with a bid of $bid_amount. Please proceed to payment. <br><a href='payment.php?product_id=$product_id'>Click for payment process</a>";
    $escaped_message = mysqli_real_escape_string($conn, $message);

    // Insert notification into the notifications table
    $notification_query = "INSERT INTO notifications (user_id, product_id, message) VALUES ($user_id, $product_id, '$escaped_message')";
    mysqli_query($conn, $notification_query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Manage Auctions</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Manage Auctions</h1>
        <form action="a_manage_auction.php" method="post">
            <h2>Add Auction</h2>
            <label for="product_id">Product:</label>
            <select id="product_id" name="product_id" required class="role-dropdown">
                <?php while ($product = mysqli_fetch_assoc($product_result)): ?>
                    <option value="<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></option>
                <?php endwhile; ?>
            </select>

            <label for="start_time">Start Time:</label>
            <input type="datetime-local" id="start_time" name="start_time" required><br>

            <label for="duration">Duration (hours):</label>
            <input type="number" id="duration" name="duration" required placeholder="In hours">

            <button type="submit" name="add_auction">Set Auction</button>
        </form>

        <h2>Current Auctions</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Start Time</th>
                    <th>Duration (hours)</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($auction = mysqli_fetch_assoc($auction_result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($auction['name']); ?></td>
                        <td><?php echo htmlspecialchars($auction['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($auction['duration']); ?></td>
                        <td><?php echo ucfirst($auction['status']); ?></td>
                        <td>
                            <button onclick="toggleEditForm(<?php echo $auction['auction_id']; ?>)">Edit</button>
                            <form action="a_manage_auction.php" method="post" style="display:inline-block;">
                                <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">
                                <button type="submit" name="delete_auction">Delete</button>
                            </form>
                            <?php if ($auction['status'] == 'upcoming'): ?>
                                <form action="a_manage_auction.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">
                                    <button type="submit" name="start_auction">Start Now</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($auction['status'] != 'ended' && $auction['status'] != 'sold_out'): ?>
                                <form action="a_manage_auction.php" method="post" style="display:inline-block;">
                                    <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $auction['product_id']; ?>">
                                    <button type="submit" name="end_auction">End Auction</button>
                                </form>
                            <?php endif; ?>
                            <div id="edit-form-<?php echo $auction['auction_id']; ?>" class="edit-form">
                                <form method="post" action="a_manage_auction.php" id="edit-form-<?php echo $auction['auction_id']; ?>">
                                    <input type="hidden" name="auction_id" value="<?php echo $auction['auction_id']; ?>">
                                    <label for="start_time_<?php echo $auction['auction_id']; ?>">Start Time:</label>
                                    <input type="datetime-local" id="start_time_<?php echo $auction['auction_id']; ?>" name="start_time" value="<?php echo date('Y-m-d\TH:i', strtotime($auction['start_time'])); ?>" required>
                                    <label for="duration_<?php echo $auction['auction_id']; ?>">Duration:</label>
                                    <input type="number" id="duration_<?php echo $auction['auction_id']; ?>" name="duration" value="<?php echo $auction['duration']; ?>" required placeholder="In hours" required>
                                    <button type="submit" name="edit_auction">Save</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
    function toggleEditForm(auctionId) {
        var form = document.getElementById(`edit-form-${auctionId}`);
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
    </script>

</body>
</html>
