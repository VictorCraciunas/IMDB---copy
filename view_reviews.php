<?php
session_start(); // Start the session

include "connection.php"; // Include your database connection file
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

// Fetch distinct usernames from the users table
$userQuery = "SELECT DISTINCT username FROM users";
$userResult = $conn->query($userQuery);

// Fetch distinct movies from the movies table
$movieQuery = "SELECT DISTINCT name FROM movies";
$movieResult = $conn->query($movieQuery);

// Fetch distinct genres from the genres table
$genreQuery = "SELECT DISTINCT name FROM genres";
$genreResult = $conn->query($genreQuery);

// Fetch distinct directors from the directors table
$directorQuery = "SELECT DISTINCT name FROM directors";
$directorResult = $conn->query($directorQuery);

// Set default values
$selectedUser = isset($_POST["selected_user"]) ? $_POST["selected_user"] : "all";
$selectedMovie = isset($_POST["selected_movie"]) ? $_POST["selected_movie"] : "all";
$selectedGenre = isset($_POST["selected_genre"]) ? $_POST["selected_genre"] : "all";
$selectedDirector = isset($_POST["selected_director"]) ? $_POST["selected_director"] : "all";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch reviews based on selected filters
    $reviewsQuery = "SELECT users.username, reviews.rating, reviews.comments, movies.name AS movie_name, genres.name AS genre_name, directors.name AS director_name
                     FROM reviews
                     INNER JOIN users ON reviews.user_id = users.id
                     INNER JOIN movies ON reviews.movie_id = movies.id
                     INNER JOIN genres ON movies.genre_id = genres.id
                     INNER JOIN directors ON movies.director_id = directors.id
                     WHERE ('$selectedUser' = 'all' OR users.username = '$selectedUser')
                       AND ('$selectedMovie' = 'all' OR movies.name = '$selectedMovie')
                       AND ('$selectedGenre' = 'all' OR genres.name = '$selectedGenre')
                       AND ('$selectedDirector' = 'all' OR directors.name = '$selectedDirector')";

    $reviewsResult = $conn->query($reviewsQuery);
}

$conn->close();
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

<!-- Logout form (displayed only if the user is logged in) -->
<?php if (isset($_SESSION["user_id"])): ?>
    <form method="post" action="">
        <button class="logout-button" type="submit" name="logout">Logout</button>
    </form>
<?php endif; ?>

<!-- Display account box -->

<button class="account-box" onclick="window.location.href='account.php'">
    <?php echo htmlspecialchars($username); ?>
</button>
<h2>View Reviews</h2>

<!-- Display form with dropdown lists for usernames, movies, genres, and directors -->
<form method="post" action="">
    <label for="selected_user">Select User:</label>
    <select name="selected_user" required>
        <option value="all" <?php echo $selectedUser === 'all' ? 'selected' : ''; ?>>All Users</option>
        <?php
        while ($row = $userResult->fetch_assoc()) {
            echo "<option value='" . $row['username'] . "' " . ($selectedUser === $row['username'] ? 'selected' : '') . ">" . $row['username'] . "</option>";
        }
        ?>
    </select>

    <label for="selected_movie">Select Movie:</label>
    <select name="selected_movie" required>
        <option value="all" <?php echo $selectedMovie === 'all' ? 'selected' : ''; ?>>All Movies</option>
        <?php
        while ($row = $movieResult->fetch_assoc()) {
            echo "<option value='" . $row['name'] . "' " . ($selectedMovie === $row['name'] ? 'selected' : '') . ">" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <label for="selected_genre">Select Genre:</label>
    <select name="selected_genre" required>
        <option value="all" <?php echo $selectedGenre === 'all' ? 'selected' : ''; ?>>All Genres</option>
        <?php
        while ($row = $genreResult->fetch_assoc()) {
            echo "<option value='" . $row['name'] . "' " . ($selectedGenre === $row['name'] ? 'selected' : '') . ">" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <label for="selected_director">Select Director:</label>
    <select name="selected_director" required>
        <option value="all" <?php echo $selectedDirector === 'all' ? 'selected' : ''; ?>>All Directors</option>
        <?php
        while ($row = $directorResult->fetch_assoc()) {
            echo "<option value='" . $row['name'] . "' " . ($selectedDirector === $row['name'] ? 'selected' : '') . ">" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <button type="submit">Show Reviews</button>
</form>



<!-- Display reviews for the selected filters, if any -->
<?php if (isset($reviewsResult) && $reviewsResult->num_rows > 0): ?>
    <h3>Reviews for <?php echo $selectedUser === 'all' ? 'All Users' : $selectedUser; ?></h3>
    <ul>
        <?php while ($review = $reviewsResult->fetch_assoc()): ?>
            <li>
                <strong>User:</strong> <?php echo $review['username']; ?><br>
                <strong>Movie:</strong> <?php echo $review['movie_name']; ?><br>
                <strong>Rating:</strong> <?php echo $review['rating']; ?><br>
                <strong>Comments:</strong> <?php echo $review['comments']; ?><br>
                <strong>Genre:</strong> <?php echo $review['genre_name']; ?><br>
                <strong>Director:</strong> <?php echo $review['director_name']; ?><br>
            </li>
        <?php endwhile; ?>
    </ul>
<?php endif; ?>

</body>
</html>
