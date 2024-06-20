<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Update the status of upcoming auctions that are now active
$current_time = date('Y-m-d H:i:s');
$query = "UPDATE auctions SET status='ongoing' WHERE status='upcoming' AND start_time <= '$current_time'";
mysqli_query($conn, $query);

// Fetch all auctions
$query = "SELECT a.id AS auction_id, p.id AS product_id, p.name, p.description, p.image, p.starting_price, a.start_time, a.duration, a.status 
          FROM auctions a 
          JOIN products p ON a.product_id = p.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Auctions</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Auctions</h1>
        <div class="section">
            <div class="a-buttons">
                <button onclick="showOngoingAuctions()" class="submit">Ongoing Auctions</button>
                <button onclick="showUpcomingAuctions()" class="submit">Upcoming Auctions</button>
            </div>
            <table id="auctionTable">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>No of items</th>
                        <th>Picture of item</th>
                        <th>Description of item</th>
                        <th>Location of bidding</th>
                        <th>Starting price</th>
                        <th>Bidding time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($auction = mysqli_fetch_assoc($result)): ?>
                    <tr class="auction-row" data-status="<?php echo $auction['status']; ?>">
                        <td><?php echo htmlspecialchars($auction['name']); ?></td>
                        <td>1</td>
                        <td><img src="<?php echo htmlspecialchars($auction['image']); ?>" alt="<?php echo htmlspecialchars($auction['name']); ?>" width="100"></td>
                        <td>
                            <div id="description-<?php echo $auction['auction_id']; ?>" class="description" style="display: none;">
                                <?php echo htmlspecialchars($auction['description']); ?>
                            </div>
                            <span id="show-button-<?php echo $auction['auction_id']; ?>" class="show-hide-button" onclick="toggleDescription(<?php echo $auction['auction_id']; ?>, 'auction')">Show</span>
                            <span id="hide-button-<?php echo $auction['auction_id']; ?>" class="show-hide-button" style="display:none;" onclick="toggleDescription(<?php echo $auction['auction_id']; ?>, 'auction')">Hide</span>
                        </td>
                        <td>Online</td>
                        <td><?php echo htmlspecialchars($auction['starting_price']); ?> TSH</td>
                        <td>
                            Start: <?php echo htmlspecialchars($auction['start_time']); ?><br>
                            Duration: <?php echo htmlspecialchars($auction['duration']); ?> hours<br>
                            <?php if ($auction['status'] == 'ongoing'): ?>
                                <a href="bid_product.php?auction_id=<?php echo $auction['auction_id']; ?>&product_id=<?php echo $auction['product_id']; ?>" class="bid-button">Bid Now</a>
                            <?php endif; ?>
                        </td>
                        <td><?php echo ucfirst($auction['status']); ?></td>
                        <td>
                            <?php if ($auction['status'] == 'ongoing'): ?>
                                <a href="bid_product.php?auction_id=<?php echo $auction['auction_id']; ?>&product_id=<?php echo $auction['product_id']; ?>" class="bid-button">Bid</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
