<?php
include("database.php");

// Check if num_passengers is set in the request
if (isset($_POST['num_passengers'])) {
    $numPassengers = (int)$_POST['num_passengers'];

    // First, we need to get the current bookings for each car
    $queryBookings = "
        SELECT car_id, SUM(num_passengers) AS total_passengers
        FROM bookings
        GROUP BY car_id
    ";
    $resultBookings = $conn->query($queryBookings);

    // Create an associative array to store total passengers for each car
    $passengerCounts = [];
    while ($row = $resultBookings->fetch_assoc()) {
        $passengerCounts[$row['car_id']] = (int)$row['total_passengers'];
    }

    // Query to fetch available cars based on the number of passengers and active status
    $queryCars = "SELECT id, image, seats FROM cars WHERE Status = 'active'";
    $stmtCars = $conn->prepare($queryCars);
    $stmtCars->execute();
    $resultCars = $stmtCars->get_result();

    if ($resultCars->num_rows > 0) {
        while ($car = $resultCars->fetch_assoc()) {
            // Calculate remaining seats
            $totalBooked = isset($passengerCounts[$car['id']]) ? $passengerCounts[$car['id']] : 0;
            $remainingSeats = $car['seats'] - $totalBooked;

            // Check if there are enough remaining seats
            if ($remainingSeats >= $numPassengers) {
                echo '<div class="car-card" onclick="selectCar(' . $car['id'] . ', event)">';
                echo '<img src="uploaded_image/' . htmlspecialchars($car['image']) . '" alt="Car" />';
                echo '<p>Seats: ' . htmlspecialchars($remainingSeats) . '</p>'; // Display remaining seats
                echo '</div>';
            }
        }
    } else {
        echo '<p>No available cars for the selected number of passengers.</p>';
    }
} else {
    echo '<p>No passenger number provided.</p>';
}

$conn->close();
