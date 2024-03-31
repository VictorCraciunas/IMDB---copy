<?php
session_start(); // Start the session
include "connection.php"; // Include your database connection file
include "config.php";

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
else{
    $username = isset($_SESSION["username"]) ? $_SESSION["username"] : "Login";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION["user_id"];
    $movieId = $_POST["movie"];
    $rating = $_POST["value-radio"]; // Get the rating from the selected star
    $comment = $_POST["comment"];

    // Convert the rating from value-1, value-2, etc., to a numeric value
    $numericRating = intval(str_replace('value-', '', $rating));

    // Insert the review into the reviews table
    $insertReviewQuery = "INSERT INTO reviews (user_id, movie_id, rating, comments) 
                          VALUES ($userId, $movieId, $numericRating, '$comment')";

    if ($conn->query($insertReviewQuery) === TRUE) {
        echo "Review added successfully";
    } else {
        echo "Error: " . $insertReviewQuery . "<br>" . $conn->error;
    }
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
<h2>Add Review</h2>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="movie">Select Movie:</label>
    <select name="movie" required>
        <?php
        $movieQuery = "SELECT id, name FROM movies";
        $movieResult = $conn->query($movieQuery);
        while ($row = $movieResult->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        ?>
    </select>

    <br>

    <!-- Star rating system -->
    <label for="rating">Rating:</label>
    <div class="radio-input">
        <input class="star s1" type="radio" id="value-5" name="value-radio" value="value-5" />
        <input class="star s2" type="radio" id="value-4" name="value-radio" value="value-4" />
        <input class="star s3" type="radio" id="value-3" name="value-radio" value="value-3" />
        <input class="star s4" type="radio" id="value-2" name="value-radio" value="value-2" />
        <input class="star s5" type="radio" id="value-1" name="value-radio" value="value-1" />
    </div>

    <br>

    <label for="comment">Comment:</label>
    <textarea name="comment" rows="2" cols="50" required></textarea>

    <br>

    <button type="submit">Add Review</button>
</form>


</body>
</html>
