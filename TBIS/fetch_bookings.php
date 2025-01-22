<?php
session_start();
include("database.php");

if (!isset($_SESSION['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['id'];
$sql = "SELECT id, startingLocation, destination, num_passengers, Pickupdate, dropoffdate, payment,Status FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($bookings);

    $stmt->close();
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database query failed.']);
}

$conn->close();
