<?php
include("database.php");

// Check if num_passengers is set in the request
if (isset($_POST['num_passengers'])) {
    $numPassengers = (int)$_POST['num_passengers'];

    // Query to fetch available cars based on the number of passengers and active status
    $queryCars = "SELECT id, image, seats FROM cars WHERE seats >= ? AND Status = 'active'";
    $stmtCars = $conn->prepare($queryCars);
    $stmtCars->bind_param("i", $numPassengers);
    $stmtCars->execute();
    $resultCars = $stmtCars->get_result();

    if ($resultCars->num_rows > 0) {
        while ($car = $resultCars->fetch_assoc()) {
            echo '<div class="car-card" onclick="selectCar(' . $car['id'] . ', event)">';
            echo '<img src="uploaded_image/' . htmlspecialchars($car['image']) . '" alt="Car" />';
            echo '<p>Seats: ' . htmlspecialchars($car['seats']) . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No available cars for the selected number of passengers.</p>';
    }
} else {
    echo '<p>No passenger number provided.</p>';
}

$conn->close();
