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
$auction_id = $_GET['auction_id'] ?? null;
$product_id = $_GET['product_id'] ?? null;

if ($auction_id === null || $product_id === null) {
    echo "Auction ID or Product ID is missing.";
    exit();
}

// Fetch the auction details
$query = "SELECT a.id AS auction_id, p.id AS product_id, p.name, p.description, p.starting_price, a.start_time, a.duration, a.status
          FROM auctions a 
          JOIN products p ON a.product_id = p.id 
          WHERE a.id = '$auction_id' AND p.id = '$product_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $auction = mysqli_fetch_assoc($result);
} else {
    echo "Invalid auction or product.";
    exit();
}

// Fetch all images for the product
$image_query = "SELECT image_path FROM product_images WHERE product_id = '$product_id'";
$image_result = mysqli_query($conn, $image_query);
$images = [];
while ($row = mysqli_fetch_assoc($image_result)) {
    $images[] = $row['image_path'];
}

// Fetch the current highest bid
$current_bid_query = "SELECT MAX(bid_amount) AS current_price FROM bids WHERE product_id = '$product_id'";
$current_bid_result = mysqli_query($conn, $current_bid_query);
$current_bid_row = mysqli_fetch_assoc($current_bid_result);
$current_price = $current_bid_row['current_price'] ?? $auction['starting_price'];

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

    // Check if the bid amount is greater than the current price
    if ($bid_amount <= $current_price) {
        echo "Bid amount must be greater than the current price.";
        exit();
    }

    // Insert the bid
    $insert_bid_query = "INSERT INTO bids (product_id, buyer_id, bid_amount, bid_time) 
                        VALUES ('$product_id', '$user_id', '$bid_amount', NOW())";
    if (mysqli_query($conn, $insert_bid_query)) {
        echo "Bid placed successfully!";
        header("Location: bid_product.php?auction_id=$auction_id&product_id=$product_id");
        exit();
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
                <?php if (!empty($images)): ?>
                    <img id="main-image" src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($auction['name']); ?> Image" class="main-image">
                    <div class="thumbnails">
                        <?php foreach ($images as $image): ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Thumbnail" onclick="document.getElementById('main-image').src='<?php echo htmlspecialchars($image); ?>'">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No images available for this product.</p>
                <?php endif; ?>
            </div>
            <div class="auction-details">
                <div class="bid-info">
                    <label>Current price: <span id="current-price"><?php echo htmlspecialchars($current_price); ?> TSH</span></label>
                    <form method="POST" class="bid-form-price" action="bid_product.php?auction_id=<?php echo $auction['auction_id']; ?>&product_id=<?php echo $auction['product_id']; ?>" onsubmit="return validateBidAmount();">
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
