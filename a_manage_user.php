<?php
session_start();
include 'db_connect.php';
include 'authent.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Handle form submission to update user role
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    // Update the user's role in the database
    $update_role_query = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_role_query);
    mysqli_stmt_bind_param($stmt, 'si', $new_role, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect back to the manage users page to show updated roles
    header('Location: a_manage_user.php');
    exit();
}

// Handle delete user request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['delete_user'];

    // Delete the user from the database
    $delete_user_query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_user_query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Redirect back to the manage users page to show updated list
    header('Location: a_manage_user.php');
    exit();
}

// Fetch all users except admins
$query_users = "SELECT * FROM users WHERE role != 'admin'";
$result_users = mysqli_query($conn, $query_users);

// Check if there was a database error
if (!$result_users) {
    die("There was an error while fetching users: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Manage Users</title>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="content">
        <h1>Manage Users</h1>
        
        <div class="section">
            <h2>Users List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Change Role</th>
                        <th>Delete User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result_users)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <!-- Use $_SERVER['PHP_SELF'] to submit the form to the same page -->
                                <form method="post" action="a_manage_user.php">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="new_role" class="role-dropdown">
                                        <option value="buyer" <?php echo ($user['role'] == 'buyer') ? 'selected' : ''; ?>>Buyer</option>
                                        <option value="seller" <?php echo ($user['role'] == 'seller') ? 'selected' : ''; ?>>Seller</option>
                                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>
                                <!-- Add a form for deleting the user -->
                                <form method="post" action="a_manage_user.php" style="display:inline-block;">
                                    <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>