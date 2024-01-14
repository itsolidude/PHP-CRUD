<?php
include 'db_connect.php';

// Handle the registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if email already exists
    $checkEmail = $conn->query("SELECT * FROM Users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.location = 'register.php';</script>";
    } else {
        $sql = "INSERT INTO Users (email, password) VALUES ('$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('New account created successfully.'); window.location = 'login.php';</script>";
        } else {
            echo "<script>alert('Error: " . addslashes($conn->error) . "'); window.location = 'register.php';</script>";
        }
    }

    $conn->close();
    exit; // Stop the script
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>

<body>
    <header>
        <?php include 'navbar.php'; ?>
    </header>
    <form action="register.php" method="post">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="password" id="password2" name="password2" required><br>

        <button type="submit">Register</button>
    </form>


    <script>
    // Function to display or hide the error message
    function displayError(inputElement, message) {
        let errorElement = inputElement.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('div');
            errorElement.classList.add('error-message');
            errorElement.style.color = 'red';
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
        errorElement.textContent = message;
        errorElement.style.display = message ? 'block' : 'none';
    }

    // Function to validate Password Confirmation
    function validateConfirmPassword() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password2').value;
        if (password !== confirmPassword) {
            return 'Passwords do not match';
        }
        return '';
    }

    // Event listener for password confirmation validation
    document.addEventListener("DOMContentLoaded", function() {
        const passwordElement = document.getElementById('password');
        const confirmPasswordElement = document.getElementById('password2');

        confirmPasswordElement.addEventListener('input', function() {
            const error = validateConfirmPassword();
            displayError(confirmPasswordElement, error);
        });

        // Form submission validation
        document.querySelector('form').addEventListener('submit', function(event) {
            const error = validateConfirmPassword();
            if (error) {
                displayError(confirmPasswordElement, error);
                event.preventDefault();
            }
        });
    });
</script>

</body>

</html>