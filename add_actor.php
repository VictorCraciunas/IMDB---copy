<?php
// Include your database connection file
include "connection.php";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve actor details from the form
    $actorName = $_POST['actor_name'];
    $yearBorn = $_POST['year_born'];

    // Validate the data (add more validation if needed)

    // Insert the actor into the database
    $insertActorQuery = "INSERT INTO actors (name, year_born) VALUES ('$actorName', $yearBorn)";
    $result = $conn->query($insertActorQuery);

    // Check for errors
    if (!$result) {
        die("Error adding actor: " . $conn->error);
    }

    // Optionally, you can redirect the user to another page after successful addition
    header("Location: add_actor.php");
    exit();
}

// If not submitted or there are validation errors, continue displaying the form
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Actor</title>
</head>
<body>

<h2>Add Actor</h2>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="actor_name">Actor Name:</label>
    <input type="text" id="actor_name" name="actor_name" required>

    <label for="year_born">Year of Birth:</label>
    <input type="number" id="year_born" name="year_born" required>

    <button type="submit">Add Actor</button>
</form>

<!-- Add other content or navigation links as needed -->

</body>
</html>
