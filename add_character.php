<?php
// Include your database connection file
include "connection.php";
session_start(); // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the movie ID is set
    if (!isset($_POST['movie_id'])) {
        echo "Error: Movie ID not set.";
        exit();
    }

    // Get the form data
    $movieId = $_POST['movie_id'];
    $characterName = $_POST['character_name'];
    $role = $_POST['role'];

    // Get the actor selection type
    $actorSelectionType = $_POST['actor_selection'];

    if ($actorSelectionType === 'existing') {
        $actorId = $_POST['actor'];
    } elseif ($actorSelectionType === 'other') {
        $actorName = $_POST['new_actor_name'];
        $yearBorn = $_POST['year_born'];

        $insertActorQuery = "INSERT INTO actors (name, year_born) VALUES ('$actorName', '$yearBorn')";
        if ($conn->query($insertActorQuery) === TRUE) {
            $actorId = $conn->insert_id;
        } else {
            echo "Error inserting actor: " . $conn->error;
            exit();
        }
    }

    $insertCharacterQuery = "INSERT INTO characters (name, role, movie_id, actor_id) VALUES ('$characterName', '$role', $movieId, $actorId)";
    if ($conn->query($insertCharacterQuery) === TRUE) {
        header("Location: movie_details.php?movie_id=$movieId");
        exit();
    } else {
        echo "Error inserting character: " . $conn->error;
        exit();
    }
}
?>
