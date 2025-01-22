<?php
include("database.php"); // Include your database connection file

// Ensure user ID is set in the session
session_start();
if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['id']; // Use session ID to get the logged-in user's ID

// Prepare SQL query to count bookings for the specific user
$sql = "SELECT COUNT(*) as total FROM bookings WHERE user_id = ?"; // Adjust table name if needed
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle preparation error
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database query preparation failed.']);
    exit();
}

$stmt->bind_param("i", $userId); // Bind user ID as an integer
$stmt->execute();
$result = $stmt->get_result();

$totalBookings = 0;

// Fetch the result
if ($result && $row = $result->fetch_assoc()) {
    $totalBookings = $row['total'];
}

// Output the total bookings as JSON
header('Content-Type: application/json');
echo json_encode(['total' => $totalBookings]);

$stmt->close(); // Close the statement
$conn->close(); // Close the database connection
