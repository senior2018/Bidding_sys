<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is a seller
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'seller') {
    header('Location: login.php');
    exit();
}

if (isset($_POST['edit_product'])) {
    // Debugging: Check if the form is submitted
    echo "Save Changes button clicked.<br>";
    var_dump($_POST);

    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $starting_price = mysqli_real_escape_string($conn, $_POST['starting_price']);
    $update_query = "UPDATE products SET name = '$name', description = '$description', starting_price = '$starting_price' WHERE id = $product_id";
    
    if (!mysqli_query($conn, $update_query)) {
        die("Update Query Failed: " . mysqli_error($conn));
    }

    if (!empty($_FILES['images']['name'][0])) {
        $target_dir = "uploads/";
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $image_name = basename($_FILES['images']['name'][$key]);
            $target_file = $target_dir . $image_name;
            if (move_uploaded_file($tmp_name, $target_file)) {
                $insert_image_query = "INSERT INTO product_images (product_id, image_path) VALUES ('$product_id', '$target_file')";
                if (!mysqli_query($conn, $insert_image_query)) {
                    die("Insert Image Query Failed: " . mysqli_error($conn));
                }
            } else {
                error_log("Failed to move uploaded file to target directory: $target_file");
            }
        }
    }

    echo "Product updated successfully.<br>"; // Debugging: Check if the product update is successful
    header('Location: s_manage_product.php');
    exit();
}

if (isset($_POST['delete_product'])) {
    echo "Delete button clicked.<br>"; // Debugging: Check if the delete button is clicked

    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $delete_query = "DELETE FROM products WHERE id = $product_id";
    if (!mysqli_query($conn, $delete_query)) {
        die("Delete Query Failed: " . mysqli_error($conn));
    }
    header('Location: s_manage_product.php');
    exit();
}

if (isset($_POST['delete_image'])) {
    echo "Delete Image button clicked.<br>"; // Debugging: Check if the delete image button is clicked

    $image_id = mysqli_real_escape_string($conn, $_POST['image_id']);
    $image_path = mysqli_real_escape_string($conn, $_POST['image_path']);
    $delete_image_query = "DELETE FROM product_images WHERE id = $image_id";
    if (mysqli_query($conn, $delete_image_query)) {
        unlink($image_path);
    } else {
        die("Delete Image Query Failed: " . mysqli_error($conn));
    }
    header('Location: s_manage_product.php');
    exit();
}

$seller_id = $_SESSION['user_id'];
$query = "SELECT * FROM products WHERE seller_id = $seller_id";
$result = mysqli_query($conn, $query);

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
                                <button class="edit-button" onclick="toggleEditForm(<?php echo $product['id']; ?>)">Edit</button>

                                <form method="post" action="s_manage_product.php" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product" class="delete-button">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <tr class="edit-form-container" id="edit-form-<?php echo $product['id']; ?>" style="display: none;">
                            <td colspan="5">
                                <form action="s_manage_product.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="edit-form-row">
                                        <label for="name-<?php echo $product['id']; ?>" class="edit-form-label">Name:</label>
                                        <input type="text" id="name-<?php echo $product['id']; ?>" name="name" class="edit-form-input" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                    </div>
                                    <div class="edit-form-row">
                                        <label for="description-<?php echo $product['id']; ?>" class="edit-form-label">Description:</label>
                                        <input type="text" id="description-<?php echo $product['id']; ?>" name="description" class="edit-form-input" value="<?php echo htmlspecialchars($product['description']); ?>" required>
                                    </div>
                                    <div class="edit-form-row">
                                        <label for="starting_price-<?php echo $product['id']; ?>" class="edit-form-label">Starting Price:</label>
                                        <input type="number" id="starting_price-<?php echo $product['id']; ?>" name="starting_price" class="edit-form-input" value="<?php echo htmlspecialchars($product['starting_price']); ?>" required>
                                    </div>
                                    <div class="edit-form-row">
                                        <label for="images-<?php echo $product['id']; ?>" class="edit-form-label">Add New Images:</label>
                                        <input type="file" id="images-<?php echo $product['id']; ?>" name="images[]" class="edit-form-input" multiple>
                                    </div>
                                    <div class="existing-images-container">
                                        <?php
                                        $images_query = "SELECT * FROM product_images WHERE product_id = " . $product['id'];
                                        $images_result = mysqli_query($conn, $images_query);
                                        while ($image = mysqli_fetch_assoc($images_result)): ?>
                                            <div class="existing-image-wrapper">
                                                <img src="<?php echo htmlspecialchars($image['image_path']); ?>" class="existing-image" alt="Product Image">
                                                <form method="post" action="s_manage_product.php" onsubmit="return confirm('Are you sure you want to delete this image?');">
                                                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                                    <input type="hidden" name="image_path" value="<?php echo $image['image_path']; ?>">
                                                    <button type="submit" name="delete_image" class="delete-image-button">Delete Image</button>
                                                </form>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <div class="edit-form-button-row">
                                        <button class="edit-form-button" type="submit" name="edit_product">Save Changes</button>
                                        <button type="button" class="edit-form-button" onclick="toggleEditForm(<?php echo $product['id']; ?>)">Cancel</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleEditForm(productId) {
            var editForm = document.getElementById('edit-form-' + productId);
            if (editForm.style.display === 'none' || editForm.style.display === '') {
                editForm.style.display = 'table-row';
            } else {
                editForm.style.display = 'none';
            }
        }

        function toggleDescription(productId) {
            var description = document.getElementById('description-' + productId);
            var showButton = document.getElementById('show-button-' + productId);
            var hideButton = document.getElementById('hide-button-' + productId);

            if (description.style.display === 'none' || description.style.display === '') {
                description.style.display = 'block';
                showButton.style.display = 'none';
                hideButton.style.display = 'inline';
            } else {
                description.style.display = 'none';
                showButton.style.display = 'inline';
                hideButton.style.display = 'none';
            }
        }
    </script>
</body>
</html>
