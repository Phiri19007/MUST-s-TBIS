<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

include("database.php");  // Include the database connection

// Set the header to return a JSON response
header('Content-Type: application/json');

// Get the user ID from the session
$userId = $_SESSION['id'];

// Prepare the SQL query to fetch user information (username, role, profile photo)
$stmt = $conn->prepare("SELECT username, role, profile_photo FROM `registered-users` WHERE id = ?");
if (!$stmt) {
    // If the query preparation fails, send an error message
    echo json_encode(['error' => 'Database query error: ' . $conn->error]);
    exit();
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found with the given user ID
if ($result->num_rows === 1) {
    // Fetch the user data
    $user = $result->fetch_assoc();

    // Check if the user role is 'admin'
    if ($user['role'] === 'admin') {
        // If the role is 'admin', redirect to login page and stop further execution
        header("Location: login.php");
        exit(); // Make sure no further code is executed
    }

    // Add session ID to the user data before sending it as JSON
    $user['session_id'] = session_id();

    // Send back the user data as JSON response
    echo json_encode($user);
} else {
    // If no user is found with this ID, return an error message
    echo json_encode(['error' => 'User not found']);
    exit(); // Stop further execution
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
