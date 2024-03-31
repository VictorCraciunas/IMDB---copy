<?php
// Include your database connection file
include "connection.php";
include "config.php";
session_start(); // Start the session

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$userId) {
    // Redirect to login page or handle the case when the user is not logged in
    header("Location: login.php");
    exit();
}
else{
    $username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Login";
}

// Handle movie removal from favorites
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_movie_id'])) {
    $removeMovieId = $_POST['remove_movie_id'];

    // Remove the movie from user's favorites
    $removeFavoriteMovieQuery = "DELETE FROM favourite_movies WHERE user_id = $userId AND movie_id = $removeMovieId";
    $conn->query($removeFavoriteMovieQuery);
}

// Fetch user's favorite movies
$userFavoriteMoviesQuery = "SELECT movies.* FROM favourite_movies
                            JOIN movies ON favourite_movies.movie_id = movies.id
                            WHERE favourite_movies.user_id = $userId";
$userFavoriteMoviesResult = $conn->query($userFavoriteMoviesQuery);

// Fetch user's reviews
$userReviewsQuery = "SELECT reviews.*, movies.name AS movie_name FROM reviews
                    JOIN movies ON reviews.movie_id = movies.id
                    WHERE reviews.user_id = $userId";
$userReviewsResult = $conn->query($userReviewsQuery);
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

<h2 class="yellow">User Account</h2>

<!-- Display user's favorite movies -->
<h3 class="yellow">User's Favorite Movies</h3>
<ul>
    <?php while ($favoriteMovie = $userFavoriteMoviesResult->fetch_assoc()): ?>
        <li>
            <?php echo $favoriteMovie['name']; ?>
            <form method="post" action="">
                <input type="hidden" name="remove_movie_id" value="<?php echo $favoriteMovie['id']; ?>">
                <button type="submit">Remove from Favorites</button>
            </form>
        </li>
    <?php endwhile; ?>
</ul>

<!-- Display user's reviews -->
<h3 class="yellow">User Reviews</h3>
<ul>
    <?php while ($review = $userReviewsResult->fetch_assoc()): ?>
        <li>
            Movie: <?php echo $review['movie_name']; ?><br>
            Rating: <?php echo $review['rating']; ?><br>
            Comments: <?php echo $review['comments']; ?>
        </li>
    <?php endwhile; ?>
</ul>

<!-- Add other sections as needed -->



</body>
</html>
