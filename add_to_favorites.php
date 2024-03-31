<?php
session_start(); // Start the session

include "connection.php"; // Include your database connection file

// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    $userId = $_SESSION["user_id"];
    $movieId = $_POST["movie_id"];

    // Add the movie to the favourite_movies table
    $addFavoriteQuery = "INSERT INTO favourite_movies (user_id, movie_id) VALUES ('$userId', '$movieId')";
    $conn->query($addFavoriteQuery);

    // Handle the response, you can send a success message or handle errors
    echo "Movie added to favorites successfully";
} else {
    // Handle the case when the user is not logged in
    echo "User not logged in";
}
$conn->close();
?>
