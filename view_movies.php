<?php
session_start(); // Start the session
include "connection.php"; // Include your database connection file
include "config.php"; // Include your configuration file

// Check if the user is logged in
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
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

// Fetch genres
$genresQuery = "SELECT * FROM genres";
$genresResult = $conn->query($genresQuery);
$genres = $genresResult->fetch_all(MYSQLI_ASSOC);

// Fetch favorite movies for the logged-in user
$favoriteMovies = [];
if ($userId !== null) {
    $favoritesQuery = "SELECT movie_id FROM favourite_movies WHERE user_id = $userId";
    $favoritesResult = $conn->query($favoritesQuery);
    while ($row = $favoritesResult->fetch_assoc()) {
        $favoriteMovies[] = $row['movie_id'];
    }
}

// Check for sorting preference and genre
$sortOrder = isset($_GET['sort']) ? $_GET['sort'] : 'best_to_worst';
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : 'all';

// Fetch movies from the movies table ordered by average rating and filtered by genre
$moviesQuery = "SELECT movies.*, AVG(reviews.rating) as average_rating 
                FROM movies 
                LEFT JOIN reviews ON movies.id = reviews.movie_id ";

if ($selectedGenre !== 'all') {
    $moviesQuery .= "WHERE movies.genre_id = $selectedGenre ";
}

$moviesQuery .= "GROUP BY movies.id ";

if ($sortOrder === 'best_to_worst') {
    $moviesQuery .= "ORDER BY average_rating DESC, movies.name";
} else {
    $moviesQuery .= "ORDER BY average_rating, movies.name";
}

$moviesResult = $conn->query($moviesQuery);
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

    <?php if (isset($_SESSION["user_id"])): ?>
        <form method="post" action="">
            <button class="logout-button" type="submit" name="logout">Logout</button>
        </form>
    <?php endif; ?>

    <!-- Display account box -->
    <button class="account-box" onclick="window.location.href='account.php'">
        <?php echo htmlspecialchars($username); ?>
    </button>

</head>

<body class="body">
    <div class="container">

    <h2>View Movies</h2>

    <!-- Sorting and Genre Dropdowns -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="best_to_worst" <?php echo $sortOrder == 'best_to_worst' ? 'selected' : ''; ?>>Best to Worst</option>
            <option value="worst_to_best" <?php echo $sortOrder == 'worst_to_best' ? 'selected' : ''; ?>>Worst to Best</option>
        </select>

        <label for="genre">Genre:</label>
        <select name="genre" id="genre">
            <option value="all" <?php echo $selectedGenre == 'all' ? 'selected' : ''; ?>>All</option>
            <?php foreach ($genres as $genre): ?>
                <option value="<?php echo $genre['id']; ?>" <?php echo $selectedGenre == $genre['id'] ? 'selected' : ''; ?>>
                    <?php echo $genre['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Filter</button>
    </form>

    <ul>
        <?php while ($movie = $moviesResult->fetch_assoc()): ?>
            <li class="movie_shown" style="margin-top:15px">
                <a class="movie_shown" href="movie_details.php?movie_id=<?php echo $movie['id']; ?>"><?php echo $movie['name']; ?></a>
                - Average Rating: <?php echo round($movie['average_rating'], 1); ?>
                <div class="container" data-movie-id="<?php echo $movie['id']; ?>">
                    <label class="container">
                        <?php if ($userId !== null && in_array($movie['id'], $favoriteMovies)): ?>
                            <!-- If the movie is in favorites, show an empty star (or nothing) -->
                        <?php else: ?>
                            <input type="checkbox">
                            <svg height="24px" id="Layer_1" version="1.2" viewBox="0 0 24 24" width="24px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g><g><path d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path></g></g>
                    </svg>
                        <?php endif; ?>
                    </label>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var stars = document.querySelectorAll('.container');
            stars.forEach(function (star) {
                if (!star.hasAttribute('data-listener')) {
                    star.setAttribute('data-listener', 'true');
                    star.addEventListener('click', function () {
                        var userId = <?php echo json_encode($userId); ?>;
                        if (userId === null) {
                            alert('Please log in to add movies to favorites.');
                            return;
                        }
                        if (star.getAttribute('data-processing') === 'true') {
                            return;
                        }
                        star.setAttribute('data-processing', 'true');
                        var movieId = star.getAttribute('data-movie-id');
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', 'add_to_favorites.php', true);
                        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === 4) {
                                star.removeAttribute('data-processing');
                                if (xhr.status === 200) {
                                    console.log(xhr.responseText);
                                    // You can add UI update logic here if needed
                                } else {
                                    console.error('Error:', xhr.status, xhr.statusText);
                                }
                            }
                        };
                        xhr.send('movie_id=' + movieId);
                    });
                }
            });
        });
    </script>

    </div>
</body>
</html>
