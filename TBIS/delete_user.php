<?php
// delete_user.php
include("database.php");
// Get the id from POST request
if (isset($_POST['id'])) {
    $id = intval($_POST['id']); // Convert id to an integer to prevent SQL injection

    // Prepare the delete statement
    $status = "removed";
    $sql = "UPDATE `registered-users` SET status = ?  WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $id); // Bind data

    if ($stmt->execute()) {
        echo "success"; // Send back success message
    } else {
        echo "error"; // Send back error message
    }

    $stmt->close(); // Close the statement
}

// Close the database connection
$conn->close();
