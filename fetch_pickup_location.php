<?php
include("database.php");

// Query to fetch the origin value and all destination values
$query = "SELECT origin, destination FROM distance"; // Get all origins and destinations
$result = $conn->query($query);

$destinations = [];
$origin = null;

if ($result->num_rows > 0) {
    // Loop through each row and add the destination to the array
    while ($row = $result->fetch_assoc()) {
        // Assume there's only one origin; you can modify this logic as per your needs
        if (!$origin) {
            $origin = $row['origin']; // Set the first origin found
        }
        $destinations[] = $row['destination']; // Add each destination
    }
    echo json_encode(['origin' => $origin, 'destinations' => $destinations]); // Return origin and destinations as JSON
} else {
    echo json_encode(['origin' => null, 'destinations' => []]); // No result found
}
