<?php
session_start();
include "connection.php";
include "config.php";

// Check if the user is already logged in, redirect to the welcome page
if (isset($_SESSION["user_id"])) {
    header("Location: menu.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the username is unique
    $checkUsernameQuery = "SELECT id FROM users WHERE username = '$username'";
    $checkUsernameResult = $conn->query($checkUsernameQuery);

    if ($checkUsernameResult->num_rows > 0) {
        $registrationError = "Username is already taken. Please choose another one.";
    } else {
        // Hash the password before storing it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $insertUserQuery = "INSERT INTO users (username, password) VALUES ('$username', '$hashedPassword')";
        if ($conn->query($insertUserQuery) === TRUE) {
            // Set success message and redirect to login page
            $_SESSION["registrationSuccess"] = "Account created successfully. You can now log in.";
            header("Location: login.php");
            exit();
        } else {
            $registrationError = "Error during registration: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <nav class="navbar" >
        <button class="Logo" onclick="window.location.href='menu.php'">
            Home
        </button>
        <ul>
            <li><a href="add_movie.php">Add Movie</a></li>
            <li><a href="add_review.php">Add Review</a></li>
            <li><a href="view_reviews.php">View Reviews</a></li>
            <li><a href="view_movies.php">View Movies</a></li>
        </ul>
    </nav>
</head>
<body class="body">
<h2>Sign Up</h2>

<!-- Display error message if there is one -->
<?php if (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<!-- Signup form -->
<form method="post" action="">
    <label for="username">Username:</label>
    <input type="text" name="username" required>

    <br>

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <br>

    <button type="submit">Sign Up</button>
</form>

<!-- Log In link -->
<p>Already have an account? <a href="login.php">Log In</a></p>
</body>
</html>
