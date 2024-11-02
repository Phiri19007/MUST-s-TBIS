<?php
session_start();
include("database.php");

if (!isset($_SESSION['id'])) {
    // User is not logged in
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['id'];
$stmt = $conn->prepare("SELECT username, role, profile_photo FROM `registered-users` WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
