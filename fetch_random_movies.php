<?php
include "connection.php"; // Ensure this includes your database connection details

// Fetch 3 random movies
$query = "SELECT id, name FROM movies ORDER BY RAND() LIMIT 3";
$result = $conn->query($query);

while ($movie = $result->fetch_assoc()) {
    echo "<li class='movie_shown'><a class='movie_shown' href='movie_details.php?movie_id=" . $movie['id'] . "'>" . htmlspecialchars($movie['name']) . "</a></li>";
}
?>
