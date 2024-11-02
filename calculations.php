<?php
session_start();
include("database.php");

// Check if the session ID is set; redirect to login if not
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user ID from session
$userId = $_SESSION['id'];

// Retrieve the user type from session, or fetch from database if not set
if (isset($_SESSION['usertype'])) {
    $usertype = $_SESSION['usertype'];
} else {
    // Fetch the user role from the database to ensure correct and updated user type
    $query = "SELECT role FROM `registered-users` WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $usertype = $row['role'];
        $_SESSION['usertype'] = $usertype; // Update the session with the retrieved role
    } else {
        header("Location: login.php"); // Redirect if user ID is not found
        exit();
    }
}

// Constants for pricing
define('PRICE_PER_KM', 1000); // Price in MWK
define('PRICE_PER_DAY', 60000); // Price in MWK

// Function to calculate the total cost based on route and rental/return dates
function calculateTotalCost($pickupLocation, $dropoffLocation, $pickupDate, $dropoffDate, $conn)
{
    if (empty($pickupLocation) || empty($dropoffLocation)) {
        return 'Pickup and drop-off locations are required.';
    }

    $sql = "SELECT distance FROM distance WHERE origin = ? AND destination = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $pickupLocation, $dropoffLocation);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $distance = $row["distance"];
        $total_distance_cost = PRICE_PER_KM * $distance;

        $start_timestamp = strtotime($pickupDate);
        $end_timestamp = strtotime($dropoffDate);

        if ($start_timestamp >= $end_timestamp) {
            return 'Invalid pickup and drop-off dates.';
        }

        $difference = $end_timestamp - $start_timestamp;
        $rental_days = max(1, floor($difference / (60 * 60 * 24)));
        $extra_charge = ($rental_days > 1) ? ($rental_days - 1) * PRICE_PER_DAY : 0;

        return $total_distance_cost + $extra_charge;
    } else {
        return 'Route not found in the database.';
    }
}

function applyDiscount($total, $usertype)
{
    if ($usertype === 'Staff') {
        return $total * 0.8; // Apply 20% discount for staff
    }
    return $total; // No discount for other user types
}

// Process the form submission if POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pickupLocation = filter_var($_POST['pickup-location'] ?? '', FILTER_SANITIZE_STRING);
    $dropoffLocation = filter_var($_POST['dropoff-location'] ?? '', FILTER_SANITIZE_STRING);
    $pickupDate = $_POST['pickup-date'] ?? '';
    $dropoffDate = $_POST['dropoff-date'] ?? '';
    $numPassengers = filter_var($_POST['num-passengers'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $selectedCarId = filter_var($_POST['selected_car_id'] ?? '', FILTER_SANITIZE_STRING);
    $status = "pending";

    $total = calculateTotalCost($pickupLocation, $dropoffLocation, $pickupDate, $dropoffDate, $conn);

    if (is_string($total)) {
        echo "Error: $total<br>";
    } else {
        $finalPrice = applyDiscount($total, $usertype);
        if ($finalPrice > 0 && $userId !== null) {
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, startingLocation, destination, pickupDate, dropoffDate, num_passengers, car_id, payment, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("issssiiis", $userId, $pickupLocation, $dropoffLocation, $pickupDate, $dropoffDate, $numPassengers, $selectedCarId, $finalPrice, $status);

                if ($stmt->execute()) {
                    // Fetch the booking details to display in the modal
                    $bookingId = $stmt->insert_id; // Get the last inserted ID
                    $stmt->close();

                    // Now retrieve the booking details for the modal
                    $stmt = $conn->prepare("SELECT startingLocation, destination, num_passengers, pickupDate, dropoffDate, payment FROM bookings WHERE id = ?");
                    $stmt->bind_param("i", $bookingId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $booking_details = $result->fetch_assoc();
                    $stmt->close();

                    // Format the final price before passing to JavaScript
                    $formattedPrice = number_format($finalPrice, 2);
                    echo "<script>showModal('$formattedPrice', '" . htmlspecialchars($usertype) . "', " . json_encode($booking_details) . ");</script>";
                } else {
                    echo "Error executing statement: " . $stmt->error;
                }
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            echo "Error: Final price is zero or user ID is not set. Data not inserted into the database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <style>
        /* Styles for the modal */
        .success {
            background-color: #d9d9d9;
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        h1,
        h2 {

            text-align: center;
        }

        h1 {
            color: #4CAF50;
        }

        button {
            background-color: #490b3d;
            color: white;
            height: 30px;
            width: 200px;
            border-radius: 5px;
            border: none;
            padding: 10px 15px;
        }

        button:hover {
            background-color: white;
            color: #490b3d;
        }
    </style>
</head>

<body>
    <!-- Modal -->
    <div class="overlay" id="overlay"></div>

    <div class="success" id="modal">
        <h1>Your booking has been successfully submitted!</h1>
        <h2>Your Booking Details:</h2>
        <ul id="booking-details"></ul>
        <p id="payment" style="font-weight: bold;"></p>
        <p>Thank you for booking with us! We will be in touch shortly.</p>
        <button onclick="closeModal()">Go to manage Bookings</button>
    </div>

    <script defer>
        function showModal(payment, userType, bookingDetails) {
            var modal = document.getElementById('modal');
            var overlay = document.getElementById('overlay');
            var paymentElement = document.getElementById('payment');
            var bookingDetailsElement = document.getElementById('booking-details');

            paymentElement.innerText = 'Total cost: MWK ' + payment; // Display formatted payment

            // Clear previous booking details
            bookingDetailsElement.innerHTML = '';

            // Add booking details to the list
            bookingDetailsElement.innerHTML = `
                <li><strong>Starting Location:</strong> ${bookingDetails.startingLocation}</li>
                <li><strong>Destination:</strong> ${bookingDetails.destination}</li>
                <li><strong>Number of Passengers:</strong> ${bookingDetails.num_passengers}</li>
                <li><strong>Pickup Date:</strong> ${bookingDetails.pickupDate}</li>
                <li><strong>Dropoff Date:</strong> ${bookingDetails.dropoffDate}</li>
            `;

            modal.style.display = 'block';
            overlay.style.display = 'block';
            window.userType = userType;
        }

        function closeModal() {
            var modal = document.getElementById('modal');
            var overlay = document.getElementById('overlay');
            modal.style.display = 'none';
            overlay.style.display = 'none';
            if (window.userType === 'Guest' || window.userType === 'Staff' || window.userType === 'Student') {
                window.location.href = 'user-dashboard.php';
            }
        }

        <?php if (isset($finalPrice)) echo "showModal('$formattedPrice', '" . htmlspecialchars($usertype) . "', " . json_encode($booking_details) . ");"; ?>
    </script>
</body>

</html>