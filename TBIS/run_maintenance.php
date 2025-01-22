<?php
session_start();
include('database.php');

if (isset($_POST['action']) && $_POST['action'] === 'run') {
    try {
        // Begin transaction for safety
        $conn->autocommit(FALSE);
        $updatedCars = [];

        // Fetch bookings with confirmed status, unprocessed, and PickupDate <= current date
        $sql = "
            SELECT b.id AS booking_id, b.car_id, b.startingLocation, b.destination, 
                   b.Status, b.Pickupdate
            FROM bookings b
            WHERE b.Status = 'confirmed'
              AND b.processed = 0
              AND b.Pickupdate <= CURDATE()";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $bookingId = $row['booking_id'];
                $carId = $row['car_id'];
                $startingLocation = $row['startingLocation'];
                $destination = $row['destination'];

                // Fetch distance between locations
                $distanceStmt = $conn->prepare("
                    SELECT distance FROM distance 
                    WHERE origin = ? AND destination = ?");
                $distanceStmt->bind_param("ss", $startingLocation, $destination);
                $distanceStmt->execute();
                $distanceResult = $distanceStmt->get_result();

                if ($distanceResult->num_rows === 0) {
                    throw new Exception("Distance data missing for $startingLocation to $destination.");
                }
                $tripDistance = $distanceResult->fetch_assoc()['distance'];

                // Fetch car details
                $carStmt = $conn->prepare("
                    SELECT distance, type, status, maintenance, fuel_consumption, fuel_usage 
                    FROM cars WHERE id = ?");
                $carStmt->bind_param("i", $carId);
                $carStmt->execute();
                $carResult = $carStmt->get_result();

                if ($carResult->num_rows === 0) {
                    throw new Exception("Car ID $carId not found.");
                }
                $car = $carResult->fetch_assoc();

                // Calculate total distance and maintenance thresholds
                $totalDistance = $car['distance'] + (2 * $tripDistance);
                $maintenanceThreshold = ($car['type'] === 'bus') ? 2000 : 3000;
                $newStatus = ($totalDistance < $maintenanceThreshold) ? 'active' : 'not active';
                $newMaintenance = ($totalDistance < $maintenanceThreshold) ? 'good' : 'due';

                // Update car details if necessary
                if (
                    $totalDistance !== $car['distance'] ||
                    $newStatus !== $car['status'] ||
                    $newMaintenance !== $car['maintenance']
                ) {
                    $updateCarStmt = $conn->prepare("
                        UPDATE cars 
                        SET distance = ?, status = ?, maintenance = ? 
                        WHERE id = ?");
                    $updateCarStmt->bind_param("issi", $totalDistance, $newStatus, $newMaintenance, $carId);
                    $updateCarStmt->execute();
                    $updatedCars[] = $carId;
                }

                // Calculate and update fuel usage
                $totalFuelUsage = $car['fuel_consumption'] * (2 * $tripDistance);
                if ($totalFuelUsage !== $car['fuel_usage']) {
                    $updateFuelStmt = $conn->prepare("
                        UPDATE cars 
                        SET fuel_usage = ? WHERE id = ?");
                    $updateFuelStmt->bind_param("di", $totalFuelUsage, $carId);
                    $updateFuelStmt->execute();
                }

                // Mark the booking as processed
                $markProcessedStmt = $conn->prepare("
                    UPDATE bookings 
                    SET processed = 1 
                    WHERE id = ?");
                $markProcessedStmt->bind_param("i", $bookingId);
                $markProcessedStmt->execute();
            }

            // Commit transaction
            $conn->commit();
            echo json_encode('Tasks completed successfully.');
        } else {
            echo json_encode('No bookings to process.');
        }
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();
        error_log("Error processing bookings: " . $e->getMessage());
        echo json_encode('An unexpected error occurred.');
    } finally {
        // Close the connection
        $conn->close();
    }
} else {
    echo json_encode('Invalid request.');
}
