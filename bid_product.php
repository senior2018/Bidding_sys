<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get auction and product details based on provided auction_id and product_id
$auction_id = $_GET['auction_id'];
$product_id = $_GET['product_id'];

// Fetch the auction details
$query = "SELECT a.id AS auction_id, p.id AS product_id, p.name, p.description, p.image, p.starting_price, a.start_time, a.duration, a.status
          FROM auctions a 
          JOIN products p ON a.product_id = p.id 
          WHERE a.id = '$auction_id' AND p.id = '$product_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $auction = mysqli_fetch_assoc($result);
} else {
    echo "Invalid auction or product.";
    exit;
}   

// Handle bid submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bid_amount = $_POST['bid_amount'];
    $user_id = $_SESSION['user_id'];

    // Check if the user exists in the users table
    $check_user_query = "SELECT id FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $check_user_query);
    if (mysqli_num_rows($result) === 0) {
        echo "User ID does not exist in the users table.";
        exit();
    }

    // Insert the bid
    $insert_bid_query = "INSERT INTO bids (product_id, buyer_id, bid_amount, bid_time) 
                        VALUES ('$product_id', '$user_id', '$bid_amount', NOW())";
    if (mysqli_query($conn, $insert_bid_query)) {
        echo "Bid placed successfully!";
    } else {
        echo "Error placing bid: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Page</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="container">
        <h1>LOT <?php echo htmlspecialchars($auction['product_id']); ?> - <?php echo htmlspecialchars($auction['name']); ?></h1>
        <div class="auction-item">
            <div class="image-gallery">
                <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="<?php echo htmlspecialchars($auction['name']); ?> Image">
                <div class="thumbnails">
                    <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="Thumbnail 1">
                    <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="Thumbnail 2">
                    <img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="Thumbnail 3">
                </div>
            </div>
            <div class="auction-details">
                <div class="bid-info">
                    <p>Current price: <span id="current-price"><?php echo htmlspecialchars($auction['starting_price']); ?> TSH</span></p>
                    <form method="POST" action="bid_product.php?auction_id=<?php echo $auction['auction_id']; ?>&product_id=<?php echo $auction['product_id']; ?>" onsubmit="return validateBidAmount();">
                        <label for="bid_amount">Bid Amount:</label>
                        <input type="number" name="bid_amount" id="bid_amount" required>
                        <button type="submit">Place Bid</button>
                    </form>
                </div>
                <p class="description">
                    <?php echo htmlspecialchars($auction['description']); ?>
                </p>
            </div>
        </div>
    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>