<?php
session_start();
include 'db_connect.php';
// include 'function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "SELECT * FROM users WHERE username = '$identifier' OR email = '$identifier'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            if ($_SESSION['role'] == 'admin') {
                header('Location: a_dashboard.php');
            } elseif ($_SESSION['role'] == 'seller') {
                header('Location: s_dashboard.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username or email.";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/1.css">
    <title>login</title>
</head>
<body>
    
    <?php include 'nav.php'; ?>

    <div class="l-container">
        <div class="l-box">
            <!-------------------- login form-------------------->
            <div class="l-box-login" id="login">
                <div class="top-header">
                    <h3>Hello, Again</h3>
                    <small>We are happy to have you back.</small>
                </div>
                <form action="login.php" method="POST">
                    <div class="l-input-group">
                        <div class="l-input-field">
                            <input type="text" class="l-input-box" name="identifier" placeholder="username or email" required>
                        </div>
                        <div class="l-input-field">
                            <input type="password" class="l-input-box" name="password" placeholder="Password" required>
                        </div>
                        <div class="l-remember">
                            <input type="checkbox" id="login-check" class="l-check">
                            <label for="formCheck"> Remember Me</label>
                        </div>
                        <div class="l-input-field">
                            <input type="submit" class="l-input-submit" value="Sign In">
                        </div>
                        <div class="l-forgot">
                            <a href="#">Forgot password?</a>
                        </div>
                        <div class="l-dont">
                            <br><span> Don't have an account? <a href="register.php">Sign Up</a></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>`
</body>
</html>