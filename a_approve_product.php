<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch all products that need approval
$query = "SELECT * FROM products WHERE status = 'pending'";
$result = mysqli_query($conn, $query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $update_query = "UPDATE products SET status = 'approved' WHERE id = $product_id";
    } elseif ($action == 'reject') {
        $update_query = "UPDATE products SET status = 'rejected' WHERE id = $product_id";
    }
    mysqli_query($conn, $update_query);

    header('Location: a_approve_product.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Approve Products</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        
    <h1>Approve Products</h1>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Description</th>
                <th>Starting Price</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo $product['starting_price']; ?></td>
                <td><img src="<?php echo $product['image']; ?>" alt="Product Image" width="100"></td>
                <td>
                    <form action="a_approve_product.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" name="action" value="approve">Approve</button>
                        <button type="submit" name="action" value="reject">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>