<?php
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["driver_ID"])) {
    // Get the driver_ID from the POST request
    $driver_ID = $_POST["driver_ID"];

    // Include the database connection

    $status = "deleted";

    // SQL query to delete the driver from the driver_details table
    $sql = "UPDATE driver_details SET status = ? WHERE driver_ID = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // If there is an error preparing the statement, output the error
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Bind the parameter (driver_ID) to the SQL query
    $stmt->bind_param("si", $status, $driver_ID);

    // Execute the statement
    if ($stmt->execute()) {
        echo "success"; // Return success if the deletion is successful
    } else {
        // If there is an error executing the query, output the error message
        echo "Error executing statement: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
} else {
    // If the POST data is not set correctly, return an error message
    echo "error";
}
