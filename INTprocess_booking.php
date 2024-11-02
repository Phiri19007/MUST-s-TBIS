<?php
session_start();
include("database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $payment = 0; // Initialize payment variable
        $status = "pending";

        // Get and sanitize form data
        $pickupLocation = filter_var($_POST['pickup-location'] ?? '', FILTER_SANITIZE_STRING);
        $dropoffLocation = filter_var($_POST['dropoff-location'] ?? '', FILTER_SANITIZE_STRING);
        $pickupDate = $_POST['pickup-date'] ?? '';
        $dropoffDate = $_POST['dropoff-date'] ?? '';
        $numPassengers = filter_var($_POST['num-passengers'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $selectedCarId = filter_var($_POST['selected_car_id'] ?? '', FILTER_SANITIZE_STRING);

        // Validate dates
        if (!strtotime($pickupDate) || !strtotime($dropoffDate)) {
            echo "Invalid date format.";
            exit();
        }

        // Prepare the SQL query to insert the booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, startingLocation, destination, pickupDate, dropoffDate, num_passengers, car_id, payment, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check for SQL statement preparation errors
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo "Failed to prepare booking statement.";
            exit();
        }

        // Bind parameters (correcting types)
        $stmt->bind_param("issssiiis", $userId, $pickupLocation, $dropoffLocation, $pickupDate, $dropoffDate, $numPassengers, $selectedCarId, $payment, $status);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to a success page
            header("Location: booking_success.php");
            exit();
        } else {
            // Handle the error
            error_log("Booking Error: " . $stmt->error);
            echo "An error occurred while processing your booking. Please try again later.";
        }

        // Close the statement
        $stmt->close();
    } else {
        $_SESSION['message'] = "You must log in to book.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: booking_form.php"); // Redirect back to the form if accessed improperly
    exit();
}

// Close the database connection
$conn->close();
