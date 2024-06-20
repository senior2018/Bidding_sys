<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is a seller
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

$seller_id = $_SESSION['user_id'];
$query = "SELECT * FROM products WHERE seller_id = $seller_id";
$result = mysqli_query($conn, $query);

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $delete_query = "DELETE FROM products WHERE id = $product_id";
    mysqli_query($conn, $delete_query);
    header('Location: s_manage_product.php');
    exit();
}

// Handle product update
if (isset($_POST['edit_product'])) {
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $starting_price = mysqli_real_escape_string($conn, $_POST['starting_price']);
    $update_query = "UPDATE products SET name = '$name', description = '$description', starting_price = '$starting_price' WHERE id = $product_id";
    mysqli_query($conn, $update_query);
    header('Location: s_manage_product.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Manage Products</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Manage Products</h1>
        <div class="section">
            <h2>My Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Description</th>
                        <th>Starting Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>
                                <div id="description-<?php echo $product['id']; ?>" class="description" style="display: none;">
                                    <?php echo htmlspecialchars($product['description']); ?>
                                </div>
                                <span id="show-button-<?php echo $product['id']; ?>" class="show-hide-button" onclick="toggleDescription(<?php echo $product['id']; ?>)">Show</span>
                                <span id="hide-button-<?php echo $product['id']; ?>" class="show-hide-button" style="display:none;" onclick="toggleDescription(<?php echo $product['id']; ?>)">Hide</span>
                            </td>
                            <td><?php echo htmlspecialchars($product['starting_price']); ?></td>
                            <td><?php echo htmlspecialchars($product['status']); ?></td>
                            <td>
                                <!-- Edit button -->
                                <button onclick="toggleEditForm(<?php echo $product['id']; ?>)">Edit</button>

                                <!-- Delete form -->
                                <form method="post" action="s_manage_product.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <!-- Edit form -->
                        <tr class="edit-form" id="edit-form-<?php echo $product['id']; ?>" style="display: none;">
                            <td colspan="5">
                                <form method="post" action="s_manage_product.php">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <label for="name-<?php echo $product['id']; ?>">Name:</label>
                                    <input type="text" id="name-<?php echo $product['id']; ?>" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    <label for="description-<?php echo $product['id']; ?>">Description:</label>
                                    <input type="text" id="description-<?php echo $product['id']; ?>" name="description" value="<?php echo htmlspecialchars($product['description']); ?>" required>
                                    <label for="starting_price-<?php echo $product['id']; ?>">Starting Price:</label>
                                    <input type="number" id="starting_price-<?php echo $product['id']; ?>" name="starting_price" value="<?php echo htmlspecialchars($product['starting_price']); ?>" required>
                                    <button type="submit" name="edit_product">Save Changes</button>
                                </form>
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