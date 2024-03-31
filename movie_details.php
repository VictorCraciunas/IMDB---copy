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

$movieId = $_GET['movie_id']; // Assume $movieId is the movie ID retrieved from the URL

// Fetch movie details along with average rating from the database
$movieQuery = "SELECT movies.*, genres.name AS genre_name, directors.name AS director_name, 
                      AVG(reviews.rating) AS average_rating
               FROM movies
               JOIN genres ON movies.genre_id = genres.id
               JOIN directors ON movies.director_id = directors.id
               LEFT JOIN reviews ON movies.id = reviews.movie_id
               WHERE movies.id = $movieId
               GROUP BY movies.id";
$movieResult = $conn->query($movieQuery);
$movie = $movieResult->fetch_assoc();

// Fetch characters for the movie
$charactersQuery = "SELECT characters.*, actors.name AS actor_name FROM characters
                     JOIN actors ON characters.actor_id = actors.id
                     WHERE characters.movie_id = $movieId";
$charactersResult = $conn->query($charactersQuery);

// Fetch actors for the dropdown
$actorsQuery = "SELECT id, name FROM actors";
$actorsResult = $conn->query($actorsQuery);
mysqli_data_seek($actorsResult, 0); // Reset the internal pointer of the result set back to the beginning

// Fetch related movies from the same genre
$relatedMoviesQuery = "SELECT id, name FROM movies WHERE genre_id = " . $movie['genre_id'] . " AND id != $movieId";
$relatedMoviesResult = $conn->query($relatedMoviesQuery);

// Fetch reviews for the movie
$reviewsQuery = "SELECT reviews.*, username AS user_name 
                 FROM reviews 
                 JOIN users ON reviews.user_id = users.id 
                 WHERE reviews.movie_id = $movieId";
$reviewsResult = $conn->query($reviewsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['name']; ?> Details</title>
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

<h2 class="yellow"><?php echo $movie['name']; ?> Details</h2>
<p>Release Year: <?php echo $movie['year']; ?></p>
<p>Genre: <?php echo $movie['genre_name']; ?></p>
<p>Director: <?php echo $movie['director_name']; ?></p>
<p>Average Rating: <?php echo round($movie['average_rating'], 1); ?></p> <!-- Display average rating -->

<h3>Cast</h3>
<ul>
    <?php while ($character = $charactersResult->fetch_assoc()): ?>
        <li> <?php echo $character['actor_name']; ?> - Character: <?php echo $character['name'];  ?> (Role: <?php echo $character['role']; ?> )</li>
    <?php endwhile; ?>
</ul>

<!-- Form to add a character -->
<form method="post" action="add_character.php">
    <label for="character_name">Character Name:</label>
    <input type="text" id="character_name" name="character_name" required>

    <label for="role">Role:</label>
    <input type="text" id="role" name="role" required>

    <label for="actor">Actor:</label>
    <select id="actor" name="actor" required>
        <?php while ($actor = $actorsResult->fetch_assoc()): ?>
            <option value="<?php echo $actor['id']; ?>"><?php echo $actor['name']; ?></option>
        <?php endwhile; ?>
        <option value="other">Other</option>
    </select>
    <input type="hidden" id="actor_selection" name="actor_selection" value="existing">

    <div id="newActorContainer" style="display: none;">
        <label for="new_actor_name">New Actor Name:</label>
        <input type="text" id="new_actor_name" name="new_actor_name">
        <label for="year_born">Year of Birth:</label>
        <input type="number" id="year_born" name="year_born">
    </div>

    <input type="hidden" name="movie_id" value="<?php echo $movieId; ?>">

    <button type="submit">Add Character</button>
</form>

<!-- JavaScript to toggle visibility of new actor details -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var actorSelect = document.getElementById('actor');
        var newActorContainer = document.getElementById('newActorContainer');
        var actorSelectionField = document.getElementById('actor_selection');

        actorSelect.addEventListener('change', function () {
            if (actorSelect.value === 'other') {
                newActorContainer.style.display = 'block';
                actorSelectionField.value = 'other';
            } else {
                newActorContainer.style.display = 'none';
                actorSelectionField.value = 'existing';
            }
        });
    });
</script>

<!-- Button to Show/Hide Reviews -->
<button id="toggleReviews">See User Reviews</button>

<!-- Reviews Section (Initially Hidden) -->
<div id="reviewsSection" style="display: none;">
    <h3>User Reviews</h3>
    <ul>
        <?php while ($review = $reviewsResult->fetch_assoc()): ?>
            <li>
                <p>User: <?php echo htmlspecialchars($review['user_name']); ?></p>
                <p>Rating: <?php echo $review['rating']; ?>/5</p>
                <p>Comment: <?php echo htmlspecialchars($review['comments']); ?></p>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<!-- JavaScript to Toggle Reviews Visibility -->
<script>
    document.getElementById('toggleReviews').addEventListener('click', function() {
        var reviewsSection = document.getElementById('reviewsSection');
        if (reviewsSection.style.display === 'none') {
            reviewsSection.style.display = 'block';
        } else {
            reviewsSection.style.display = 'none';
        }
    });
</script>

<!-- Related Movies Section -->
<h3>Related Movies</h3>
<ul>
    <?php while ($relatedMovie = $relatedMoviesResult->fetch_assoc()): ?>
        <li><a href="movie_details.php?movie_id=<?php echo $relatedMovie['id']; ?>"><?php echo $relatedMovie['name']; ?></a></li>
    <?php endwhile; ?>
</ul>


<button onclick="window.location.href='add_review.php'">Add a Review</button>

</body>
</html>
