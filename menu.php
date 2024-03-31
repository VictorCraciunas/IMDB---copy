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

// Fetch top 3 highest-rated movies
$topRatedMoviesQuery = "SELECT movies.id, movies.name, AVG(reviews.rating) AS average_rating
                        FROM movies
                        JOIN reviews ON movies.id = reviews.movie_id
                        GROUP BY movies.id
                        ORDER BY average_rating DESC
                        LIMIT 3";
$topRatedMoviesResult = $conn->query($topRatedMoviesQuery);

// Fetch top 3 fan favorite movies
$fansFavoriteQuery = "SELECT movies.id, movies.name, COUNT(favourite_movies.movie_id) AS fav_count
                      FROM movies
                      JOIN favourite_movies ON movies.id = favourite_movies.movie_id
                      GROUP BY movies.id
                      ORDER BY fav_count DESC
                      LIMIT 3";
$fansFavoriteResult = $conn->query($fansFavoriteQuery);

// Fetch 3 random movies for the 'Featured Today' section
$featuredTodayQuery = "SELECT id, name FROM movies ORDER BY RAND() LIMIT 3";
$featuredTodayResult = $conn->query($featuredTodayQuery);
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

<body class="body" >

<div class="container d-flex justify-content-center mt-100">
    <div class="d-flex justify-content-around " style="width: 1000px">
        <div class="">
            <h1 class="yellow">What to watch</h1>
            <!-- Top Rated Section -->
            <h3 class="mt-50">Top Rated Movies</h3>
            <ul>
                <?php while ($movie = $topRatedMoviesResult->fetch_assoc()): ?>
                    <li class="movie_shown"><a class="movie_shown" href="movie_details.php?movie_id=<?php echo $movie['id']; ?>">
                            <?php echo htmlspecialchars($movie['name']); ?> - Rating: <?php echo round($movie['average_rating'], 1); ?>
                        </a></li>
                <?php endwhile; ?>
            </ul>

            <!-- Fans' Favorite Section -->
            <h3 class="mt-50">Fans' Favorite</h3>
            <ul>
                <?php while ($movie = $fansFavoriteResult->fetch_assoc()): ?>
                    <li class="movie_shown"><a class="movie_shown" href="movie_details.php?movie_id=<?php echo $movie['id']; ?>">
                            <?php echo htmlspecialchars($movie['name']); ?> - Favorites Count: <?php echo $movie['fav_count']; ?>
                        </a></li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="">
            <h1 class="yellow ">Featured Today</h1>
            <ul class="mt-50" id="featuredTodayList">
                <?php while ($movie = $featuredTodayResult->fetch_assoc()): ?>
                    <li class="movie_shown"><a class="movie_shown" href="movie_details.php?movie_id=<?php echo $movie['id']; ?>">
                            <?php echo htmlspecialchars($movie['name']); ?>
                        </a></li>
                <?php endwhile; ?>
            </ul>
            <button id="refreshFeatured"> > </button>
        </div>
    </div>
</div>

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

<!-- Featured Today Section -->





<!-- Fetch random movies when the user pressed the arrow -->
<script>
    document.getElementById('refreshFeatured').addEventListener('click', function() {
        var featuredList = document.getElementById('featuredTodayList');

        // Apply sliding animation
        featuredList.classList.add('sliding');

        // Fetch new movies after the sliding animation completes
        setTimeout(function() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_random_movies.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Update the list and reset the sliding effect
                    featuredList.innerHTML = xhr.responseText;
                    featuredList.classList.remove('sliding');
                } else {
                    console.error('Error fetching new movies');
                }
            };
            xhr.send();
        }, 500); // This timeout duration should match the CSS transition duration
    });
</script>

</body>
</html>
