<h3?php
// Include necessary files
include 'db_connect.php';
include 'function.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
    $national_id = mysqli_real_escape_string($conn, $_POST['national_id']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO users (firstname, lastname, mobile_number, national_id, email, username, password, role, approved) 
            VALUES ('$firstname', '$lastname', '$mobile_number', '$national_id', '$email', '$username', '$hashed_password', 'buyer', 1)";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Redirect to login page after successful registration
        header('Location: login.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Document</title>
</head>
<body>

<?php include 'nav.php'; ?>

<div class="r-container">
    <div class="r-box">
    <!-- Registration form -->
        <form action="register.php" method="POST">
            <div class="register-container" id="register">
                <div class="top-header">
                    <h3>Sign up Now</h3>
                    <br><small>We Are Happy To Have You With Us.</small>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" name="firstname" placeholder="Firstname" required>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="lastname" placeholder="Lastname" required>
                    </div>
                </div>
                <div class="two-forms">
                    <div class="input-box">
                        <input type="text" class="input-field" name="mobile_number" placeholder="Mobile Number" required>
                    </div>
                    <div class="input-box">
                        <input type="text" class="input-field" name="national_id" placeholder="National ID" required>
                    </div>
                </div>
                <div class="input-box">
                    <input type="email" class="input-field" name="email" placeholder="Email" required>
                </div>
                <div class="input-box">
                    <input type="text" class="input-field" name="username" placeholder="Username" required>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="password" placeholder="Password" required>
                </div>
                <div class="input-box">
                    <input type="password" class="input-field" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="input-box">
                    <input type="submit" class="submit" value="Sign Up">
                </div> 
                <div class="two-col">
                    <div class="two">
                        <label><a href="#">Terms & Conditions</a></label>
                    </div>
                    <div class="top">
                        <span class="l">Have an account? <a href="login.php">Login</a></span>
                    </div>
                </div>   
            </div>
        </form>
    </div>
</div>
</body>
</html>