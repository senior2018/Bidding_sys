<?php
function loginUser($email, $password) {
    global $conn;
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Update last login time
        $user_id = $user['id'];
        $update_query = "UPDATE users SET last_login = NOW() WHERE id = $user_id";
        mysqli_query($conn, $update_query);

        return true;
    } else {
        return false;
    }
}

function registerUser($firstname, $lastname, $email, $password, $role) {
    global $conn;
    $query = "INSERT INTO users (firstname, lastname, email, password, role, approved) VALUES ('$firstname', '$lastname', '$email', '$password', '$role', 0)";
    return mysqli_query($conn, $query);
}
?>