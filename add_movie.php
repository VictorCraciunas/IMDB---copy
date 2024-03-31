<?php
session_start(); // Start the session
include "connection.php";
include "config.php";


// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
// Logout logic
if (isset($_POST["logout"])) {
// Destroy the session
session_destroy();

// Unset all session variables
$_SESSION = array();

// Set the username to "Guest" after logging out
$username = "Login";
} else {
// Get the username if available
$username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Login";
}
} else {
// If the user is not logged in, set a default username
$username = "Login";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<!-- Display account box -->
<button class="account-box" onclick="window.location.href='account.php'">
    <?php echo htmlspecialchars($username); ?>
</button>
<!-- Logout form (displayed only if the user is logged in) -->
<?php if (isset($_SESSION["user_id"])): ?>
    <form method="post" action="">
        <button class="logout-button" type="submit" name="logout">Logout</button>
    </form>
<?php endif; ?>

<!-- Display a form for adding a movie -->
<h3>Add Movie</h3>
<form method="post" action="process_movie.php">
    <label for="genre">Select Genre:</label>
    <select name="genre" id="genre" required>
        <!-- Fetch and display genres from the database -->
        <?php
        include "connection.php"; // Include your database connection file

        $genreQuery = "SELECT id, name FROM genres";
        $genreResult = $conn->query($genreQuery);

        while ($row = $genreResult->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
        <option value="other">Other</option>
    </select>

    <br>

    <div class="custom-field" id="customGenreField">
        <label for="custom_genre">Custom Genre:</label>
        <input type="text" name="custom_genre">
    </div>

    <br>

    <label for="movie_name">Movie Name:</label>
    <input type="text" name="movie_name" required>

    <br>

    <label for="year">Year:</label>
    <input type="number" name="year" required>

    <br>

    <label for="director">Select Director:</label>
    <select name="director" id="director" required>
        <!-- Fetch and display directors from the database -->
        <?php
        $directorQuery = "SELECT id, name FROM directors";
        $directorResult = $conn->query($directorQuery);

        while ($row = $directorResult->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
        <option value="other">Other</option>
    </select>

    <br>

    <div class="custom-field" id="customDirectorField">
        <label for="custom_director">Custom Director:</label>
        <input type="text" name="custom_director">
    </div>

    <br>

    <button type="submit" name="add_movie">Add Movie</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Hide custom genre and director fields initially
        var customGenreField = document.getElementById("customGenreField");
        customGenreField.style.display = "none";

        var customDirectorField = document.getElementById("customDirectorField");
        customDirectorField.style.display = "none";

        // Toggle visibility of custom genre field when "Other" is selected
        document.getElementById("genre").addEventListener("change", function () {
            customGenreField.style.display = this.value === "other" ? "block" : "none";
        });

        // Toggle visibility of custom director field when "Other" is selected
        document.getElementById("director").addEventListener("change", function () {
            customDirectorField.style.display = this.value === "other" ? "block" : "none";
        });
    });
</script>

</body>
</html>
