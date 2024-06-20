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

// Handle form submission for adding, editing, and deleting auctions
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
    }

    header('Location: a_manage_auction.php');
    exit();
}

// Fetch all auctions for display
$auction_query = "SELECT a.id AS auction_id, p.name, a.start_time, a.duration, a.status 
                  FROM auctions a 
                  JOIN products p ON a.product_id = p.id";
$auction_result = mysqli_query($conn, $auction_query);
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
                <?php endwhile; ?>ption value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
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

    <script src="assets/js/script.js"></script>

</body>
</html>
