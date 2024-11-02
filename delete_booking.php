<?php
include("database.php"); // Include your database connection file

$id = $_POST['id'];

$sql = "DELETE FROM bookings WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$success = $stmt->execute();

header('Content-Type: application/json');
echo json_encode(['success' => $success]);

$stmt->close();
$conn->close();
