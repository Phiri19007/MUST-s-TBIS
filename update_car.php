<?php
include("database.php");

// Function to update car distances and statuses only if changes are needed
function updateCarStatus($conn)
{
    // Query to get all bookings with valid trips and distances
    $sql = "SELECT b.car_id, d.distance 
            FROM bookings b 
            JOIN distance d ON b.Trip = d.routename 
            WHERE b.car_id IS NOT NULL";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $carId = $row['car_id'];
            $tripDistance = $row['distance'];

            // Check if the trip distance is valid
            if (is_null($tripDistance) || $tripDistance <= 0) {
                error_log("Car ID $carId has no valid distance value. Skipping update.");
                continue; // Skip this iteration if distance is not valid
            }

            // Get the current distance, maintenance, and status for the car
            $carSql = "SELECT distance, maintenance, status FROM cars WHERE id = ?";
            $carStmt = $conn->prepare($carSql);
            $carStmt->bind_param("i", $carId);
            $carStmt->execute();
            $carResult = $carStmt->get_result();

            if ($carResult->num_rows > 0) {
                $carRow = $carResult->fetch_assoc();
                $currentDistance = $carRow['distance'];
                $currentMaintenance = $carRow['maintenance'];
                $currentStatus = $carRow['status'];

                // Calculate the new distance and determine maintenance and status based on thresholds
                $newDistance = $currentDistance + $tripDistance;
                $newMaintenance = ($newDistance > 3000 || ($newDistance > 2000 && $currentMaintenance === 'due')) ? 'due' : 'not due';
                $newStatus = ($newDistance > 3000 || ($newDistance > 2000 && $currentStatus === 'not active')) ? 'not active' : 'active';

                // Check if there are actual changes before updating the database
                if ($newDistance != $currentDistance || $newMaintenance != $currentMaintenance || $newStatus != $currentStatus) {
                    // Update the car's distance, maintenance, and status
                    $updateSql = "UPDATE cars 
                                  SET distance = ?, 
                                      maintenance = ?, 
                                      status = ? 
                                  WHERE id = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("issi", $newDistance, $newMaintenance, $newStatus, $carId);
                    $updateStmt->execute();

                    echo "Car ID $carId updated successfully with new distance $newDistance, status $newStatus, and maintenance $newMaintenance.<br>";
                } else {
                    echo "No changes needed for Car ID $carId.<br>";
                }
            }
        }
    } else {
        echo "No confirmed bookings found.";
    }
}

// Call the update function
updateCarStatus($conn);

// Close the connection
$conn->close();
