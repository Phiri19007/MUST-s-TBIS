<?php
include("database.php");  // Include your database connection

// Check if the booking ID and status are set
if (isset($_POST['booking_id']) && isset($_POST['status'])) {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    // SQL query to update the status
    $sql = "UPDATE bookings SET Status = ? WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }

    // Bind the parameters
    $stmt->bind_param("si", $status, $booking_id);  // "si" means string and integer

    // Execute the query
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "error";
}
