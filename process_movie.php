<?php
include "connection.php"; // Include your database connection file

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_movie"])) {
    $genreId = $_POST["genre"];
    $movieName = $_POST["movie_name"];
    $year = $_POST["year"];
    $directorId = $_POST["director"];
    $customGenre = $_POST["custom_genre"];
    $customDirector = $_POST["custom_director"];

    // Check if custom genre is selected
    if ($genreId == "other" && !empty($customGenre)) {
        // Insert the custom genre into the genres table
        $insertGenreQuery = "INSERT INTO genres (name) VALUES ('$customGenre')";
        $result = $conn->query($insertGenreQuery);

        if ($result === TRUE) {
            // Get the ID of the newly inserted genre
            $genreId = $conn->insert_id;
        } else {
            echo "Error adding custom genre: " . $conn->error;
            $conn->close();
            exit();
        }
    }

    // Check if custom director is selected
    if ($directorId == "other" && !empty($customDirector)) {
        // Insert the custom director into the directors table
        $insertDirectorQuery = "INSERT INTO directors (name) VALUES ('$customDirector')";
        $result = $conn->query($insertDirectorQuery);

        if ($result === TRUE) {
            // Get the ID of the newly inserted director
            $directorId = $conn->insert_id;
        } else {
            echo "Error adding custom director: " . $conn->error;
            $conn->close();
            exit();
        }
    }

    // Insert the movie into the movies table
    $insertMovieQuery = "INSERT INTO movies (name, year, genre_id, director_id) 
                        VALUES ('$movieName', $year, $genreId, $directorId)";

    if ($conn->query($insertMovieQuery) === TRUE) {
        echo "Movie added successfully";
    } else {
        echo "Error adding movie: " . $conn->error;
    }

    $conn->close();
}
?>
