<?php
session_start();
include("database.php");

$bookingId = $_GET['id'];  // Get the booking ID from the request parameters

// SQL query to get booking details (including processed value)
$query = "SELECT id, Status, processed FROM bookings WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $bookingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the booking details
    $booking = $result->fetch_assoc();
    echo json_encode(['success' => true, 'booking' => $booking]);  // Return booking details in JSON format
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
}

$stmt->close();
$conn->close();
