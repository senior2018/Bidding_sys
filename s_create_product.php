<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is a seller
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $starting_price = mysqli_real_escape_string($conn, $_POST['starting_price']);
    
    // Insert product information into the 'products' table
    $query = "INSERT INTO products (seller_id, name, description, starting_price, status) VALUES ('$seller_id', '$name', '$description', '$starting_price', 'pending')";
    if (mysqli_query($conn, $query)) {
        $product_id = mysqli_insert_id($conn);

        // Handle multiple image uploads
        $target_dir = "uploads/";
        $images = $_FILES['images'];

        for ($i = 0; $i < count($images['name']); $i++) {
            $image_name = $images['name'][$i];
            $tmp_name = $images['tmp_name'][$i];
            $target_file = $target_dir . basename($image_name);

            // Move uploaded file to the target directory
            if (move_uploaded_file($tmp_name, $target_file)) {
                $query = "INSERT INTO product_images (product_id, image_path) VALUES ('$product_id', '$target_file')";
                if (!mysqli_query($conn, $query)) {
                    error_log("Failed to insert image path into database: " . mysqli_error($conn));
                    echo "Error: Failed to save image path.";
                }
            } else {
                error_log("Failed to move uploaded file to target directory: $target_file");
                echo "Error: Failed to upload image.";
            }
        }

        // Redirect to manage products page
        header('Location: s_manage_product.php');
        exit();
    } else {
        error_log("Failed to insert product into database: " . mysqli_error($conn));
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Create Product</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Create Product</h1>

        <!-- Form to create a new product -->
        <form method="post" action="s_create_product.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="starting_price">Starting Price</label>
                <input type="number" id="starting_price" name="starting_price" required>
            </div>
            <div class="form-group">
                <label for="images">Product Images</label>
                <input type="file" id="images" name="images[]" multiple required>
            </div>
            <button type="submit">Create Product</button>
        </form>
    </div>
</body>
</html>
