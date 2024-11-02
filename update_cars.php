<?php
include("database.php");

// Fetch bookings with confirmed status that have not been processed
$sql = "SELECT id, car_id, startingLocation, destination FROM bookings WHERE status = 'confirmed' AND processed = 0";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Initialize an array to track updated car IDs
    $updatedCars = [];

    while ($row = $result->fetch_assoc()) {
        $bookingId = $row['id'];
        $car_id = $row['car_id'];
        $startingLocation = $row['startingLocation'];
        $destination = $row['destination'];

        // Get distance between startingLocation and destination from the distance table
        $distanceSql = "SELECT distance FROM distance WHERE origin = ? AND destination = ?";
        $distanceStmt = $conn->prepare($distanceSql);
        $distanceStmt->bind_param("ss", $startingLocation, $destination);
        $distanceStmt->execute();
        $distanceResult = $distanceStmt->get_result();

        if ($distanceResult->num_rows > 0) {
            $distanceRow = $distanceResult->fetch_assoc();
            $tripDistance = $distanceRow['distance'];

            // Get car's current distance, type, status, and maintenance from the cars table
            $carSql = "SELECT distance, type, status, maintenance FROM cars WHERE id = ?";
            $carStmt = $conn->prepare($carSql);
            $carStmt->bind_param("i", $car_id);
            $carStmt->execute();
            $carResult = $carStmt->get_result();

            if ($carResult->num_rows > 0) {
                $carRow = $carResult->fetch_assoc();
                $carDistance = $carRow['distance'];
                $carType = $carRow['type'];
                $currentStatus = $carRow['status'];
                $currentMaintenance = $carRow['maintenance'];

                // Calculate new total distance
                $totalDistance = $tripDistance + $carDistance;

                // Set maintenance thresholds
                $threshold = ($carType === 'bus') ? 2000 : 3000;

                // Determine new status and maintenance based on total distance
                $newStatus = ($totalDistance < $threshold) ? 'active' : 'not active';
                $newMaintenance = ($totalDistance < $threshold) ? 'good' : 'due';

                // Check if updates are needed
                if ($totalDistance != $carDistance || $newStatus != $currentStatus || $newMaintenance != $currentMaintenance) {
                    // Update car's distance, status, and maintenance in the cars table
                    $updateSql = "UPDATE cars SET distance = ?, status = ?, maintenance = ? WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("issi", $totalDistance, $newStatus, $newMaintenance, $car_id);

                    if ($updateStmt->execute()) {
                        echo "Car ID $car_id updated successfully.<br>";
                        // Mark the car ID as updated
                        $updatedCars[] = $car_id;
                    } else {
                        echo "Error updating car ID $car_id: " . $conn->error . "<br>";
                    }
                } else {
                    echo "No updates needed for Car ID $car_id.<br>";
                }
            }
        }

        // Mark the booking as processed
        $markProcessedSql = "UPDATE bookings SET processed = 1 WHERE id = ?";
        $markProcessedStmt = $conn->prepare($markProcessedSql);
        $markProcessedStmt->bind_param("i", $bookingId);
        $markProcessedStmt->execute();
    }

    // If any cars were updated, we can log them
    if (!empty($updatedCars)) {
        echo "Updated cars: " . implode(", ", $updatedCars);
    }
} else {
    echo "No confirmed bookings found.";
}

$conn->close();
