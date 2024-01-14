<?php
include 'db_connect.php';

// Handle the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // Password from form

    // Query to check if the email exists in the database
    $sql = "SELECT user_id, password FROM Users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, start a new session
            session_start();
            $_SESSION['user_id'] = $row['user_id'];
            // Redirect to another page or show success message
            echo "<script>alert('Login successful!'); window.location = '../index.php';</script>";
        } else {
            // Password is incorrect
            echo "<script>alert('Invalid password.'); window.location = 'login.php';</script>";
        }
    } else {
        // Email does not exist
        echo "<script>alert('Email does not exist.'); window.location = 'login.php';</script>";
    }

    $conn->close();
}
?>

<!--HTML-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>

<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <form action="login.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
</body>

</html>