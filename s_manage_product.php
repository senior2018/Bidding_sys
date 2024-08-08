<?php
session_start();
include 'db_connect.php'; 
include 'authent.php'; 

// Check if the user is logged in and is a seller
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update product details
    if (isset($_POST['edit_product'])) {
        $product_id = $_POST['product_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $starting_price = $_POST['starting_price'];

        // Prepare and execute the update query
        $update_query = "UPDATE products SET name = ?, description = ?, starting_price = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, 'ssdi', $name, $description, $starting_price, $product_id);
        if (mysqli_stmt_execute($stmt)) {
            // Handle image uploads if any
            if (!empty($_FILES['images']['name'][0])) {
                $target_dir = "uploads/";
                foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                    $image_name = basename($_FILES['images']['name'][$key]);
                    $target_file = $target_dir . $image_name;
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        $insert_image_query = "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)";
                        $stmt_image = mysqli_prepare($conn, $insert_image_query);
                        mysqli_stmt_bind_param($stmt_image, 'is', $product_id, $target_file);
                        mysqli_stmt_execute($stmt_image);
                        mysqli_stmt_close($stmt_image);
                    } else {
                        error_log("Failed to move uploaded file to target directory: $target_file");
                    }
                }
            }
            // Redirect after successful update
            header('Location: s_manage_product.php');
            exit();
        } else {
            die("Update Query Failed: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }

    // Delete product
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $delete_query = "DELETE FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($stmt, 'i', $product_id);
        if (mysqli_stmt_execute($stmt)) {
            // Also delete associated images if necessary
            $delete_images_query = "DELETE FROM product_images WHERE product_id = ?";
            $stmt_images = mysqli_prepare($conn, $delete_images_query);
            mysqli_stmt_bind_param($stmt_images, 'i', $product_id);
            mysqli_stmt_execute($stmt_images);
            mysqli_stmt_close($stmt_images);
            header('Location: s_manage_product.php');
            exit();
        } else {
            die("Delete Query Failed: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }

    // Delete image
    if (isset($_POST['delete_image'])) {
        $image_id = $_POST['image_id'];
        $image_path = $_POST['image_path'];
        $delete_image_query = "DELETE FROM product_images WHERE id = ?";
        $stmt = mysqli_prepare($conn, $delete_image_query);
        mysqli_stmt_bind_param($stmt, 'i', $image_id);
        if (mysqli_stmt_execute($stmt)) {
            // Remove image file from server
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            header('Location: s_manage_product.php');
            exit();
        } else {
            die("Delete Image Query Failed: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch products for the logged-in seller
$seller_id = $_SESSION['user_id'];
$query = "SELECT * FROM products WHERE seller_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $seller_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

mysqli_stmt_close($stmt);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
        }

        .form-group label {
            width: 150px;
            margin-right: 10px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group input[type="file"] {
            flex: 1;
        }

        .form-group textarea {
            resize: none;
        }

        .image-container {
            display: inline-block;
            position: relative;
            margin-right: 10px;
        }

        .image-container img {
            width: 100px;
            height: auto;
            display: block;
        }

        .image-container .delete-image-form {
            position: absolute;
            top: 0;
            right: 0;
        }

        .image-container .delete-image-form button {
            background: red;
            color: white;
            border: none;
            padding: 2px 5px;
            cursor: pointer;
            font-size: 12px;
        }

        .edit-form-container {
            margin-top: 20px;
        }

        .edit-form-container .save-button,
        .edit-form-container .cancel-button {
            margin-top: 10px;
            padding: 5px 10px;
            font-size: 14px;
        }
    </style>
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
                                <button class="edit-button" onclick="toggleEditForm(<?php echo $product['id']; ?>)">Edit</button>

                                <form method="post" action="s_manage_product.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="edit-form-container" id="edit-form-<?php echo $product['id']; ?>" style="display:none;">
                            <td colspan="5">
                                <form method="post" action="s_manage_product.php" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                    <div class="form-group">
                                        <label for="name">Name:</label>
                                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea id="description" name="description" rows="2" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="starting_price">Starting Price:</label>
                                        <input type="number" id="starting_price" name="starting_price" value="<?php echo htmlspecialchars($product['starting_price']); ?>" step="0.01" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="images">Upload Images:</label>
                                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" name="edit_product" class="save-button">Save Changes</button>
                                        <button type="button" class="cancel-button" onclick="toggleEditForm(<?php echo $product['id']; ?>)">Cancel</button>
                                    </div>
                                </form>
                                <div class="existing-images">
                                    <?php
                                        $product_id = $product['id'];
                                        $image_query = "SELECT * FROM product_images WHERE product_id = $product_id";
                                        $image_result = mysqli_query($conn, $image_query);
                                        while ($image = mysqli_fetch_assoc($image_result)):
                                    ?>
                                        <div class="image-container">
                                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Product Image">
                                            <form method="post" action="s_manage_product.php" class="delete-image-form" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                                <input type="hidden" name="image_path" value="<?php echo htmlspecialchars($image['image_path']); ?>">
                                                <button type="submit" name="delete_image">X</button>
                                            </form>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleDescription(productId) {
            var description = document.getElementById('description-' + productId);
            var showButton = document.getElementById('show-button-' + productId);
            var hideButton = document.getElementById('hide-button-' + productId);

            if (description.style.display === 'none') {
                description.style.display = 'block';
                showButton.style.display = 'none';
                hideButton.style.display = 'inline';
            } else {
                description.style.display = 'none';
                showButton.style.display = 'inline';
                hideButton.style.display = 'none';
            }
        }

        function toggleEditForm(productId) {
            var editForm = document.getElementById('edit-form-' + productId);
            var isVisible = editForm.style.display === 'table-row';

            var allEditForms = document.getElementsByClassName('edit-form-container');
            for (var i = 0; i < allEditForms.length; i++) {
                allEditForms[i].style.display = 'none';
            }

            if (!isVisible) {
                editForm.style.display = 'table-row';
            }
        }
    </script>
</body>
</html>