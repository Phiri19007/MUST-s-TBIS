<?php
session_start();
include("database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (isset($_SESSION['id'])) {
        $userId = $_SESSION['id'];
        $payment = 0; // Initialize payment variable (you might want to calculate this based on car type and duration)
        $status = "pending";
        $errorMessage = "";

        // Get and sanitize form data
        $pickupLocation = filter_var($_POST['pickup-location'] ?? '', FILTER_SANITIZE_STRING);
        $dropoffLocation = filter_var($_POST['dropoff-location'] ?? '', FILTER_SANITIZE_STRING);
        $pickupDate = $_POST['pickup-date'] ?? '';
        $dropoffDate = $_POST['dropoff-date'] ?? '';
        $numPassengers = filter_var($_POST['num-passengers'] ?? '', FILTER_SANITIZE_NUMBER_INT);
        $selectedCarId = filter_var($_POST['selected_car_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);  // Assuming car ID is an integer

        // Validate required fields
        if (empty($pickupLocation) || empty($dropoffLocation) || empty($numPassengers) || empty($pickupDate) || empty($dropoffDate)) {
            $errorMessage = "All fields must be filled.";
        }

        // Validate dates
        if (empty($errorMessage)) {
            // Ensure the dates are valid and pickup is not later than dropoff
            if (!strtotime($pickupDate) || !strtotime($dropoffDate)) {
                $errorMessage = "Invalid date format.";
            } elseif (strtotime($pickupDate) > strtotime($dropoffDate)) {
                $errorMessage = "Pickup date cannot be later than dropoff date.";
            }
        }

        // If there's an error, store the message in session and redirect back to the form
        if (!empty($errorMessage)) {
            $_SESSION['error_message'] = $errorMessage;
            header("Location: booking_success.php");  // Redirect to booking form page with error
            exit();
        }

        // If no error, proceed to insert booking
        if (empty($errorMessage)) {
            // Prepare the SQL query to insert the booking
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, startingLocation, destination, pickupDate, dropoffDate, num_passengers, car_id, payment, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Check for SQL statement preparation errors
            if (!$stmt) {
                $_SESSION['error_message'] = "Failed to prepare booking statement.";
                header("Location: booking_success.php");
                exit();
            }

            // Bind parameters (correcting types)
            $stmt->bind_param("issssiiis", $userId, $pickupLocation, $dropoffLocation, $pickupDate, $dropoffDate, $numPassengers, $selectedCarId, $payment, $status);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to a success page
                $_SESSION['success_message'] = "Your booking was successfully submitted!";
                header("Location: booking_success.php");
                exit();
            } else {
                // Handle the error
                $_SESSION['error_message'] = "An error occurred while processing your booking. Please try again later.";
                header("Location: booking_success.php");
                exit();
            }

            // Close the statement
            $stmt->close();
        }
    } else {
        $_SESSION['message'] = "You must log in to book.";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: booking_page.php"); // Redirect back to the form if accessed improperly
    exit();
}

// Close the database connection
$conn->close();
