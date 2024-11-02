<?php
session_start();
include("database.php");

// Check if user_id is set in the session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch booking details from the database
$stmt = $conn->prepare("SELECT startingLocation, destination, num_passengers, pickupDate, dropoffDate, payment FROM bookings WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$booking_details = [];
if ($result->num_rows > 0) {
    $booking_details = $result->fetch_assoc();
} else {
    $_SESSION['message'] = "No booking details found.";
    header("Location: user-dashboard.php");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .success {
            background-color: #d9d9d9;
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1,
        h2 {

            text-align: center;
        }

        h1 {
            color: #4CAF50;
        }

        ul {
            list-style-type: none;
            padding: 0;
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
    <div class="success">
        <h1>Your booking has been successfully submitted!</h1>
        <h2>Your Booking Details:</h2>
        <ul>
            <li><strong>Starting Location:</strong> <?php echo htmlspecialchars($booking_details['startingLocation']); ?></li>
            <li><strong>Destination:</strong> <?php echo htmlspecialchars($booking_details['destination']); ?></li>
            <li><strong>Number of Passengers:</strong> <?php echo htmlspecialchars($booking_details['num_passengers']); ?></li>
            <li><strong>Pickup Date:</strong> <?php echo htmlspecialchars($booking_details['pickupDate']); ?></li>
            <li><strong>Dropoff Date:</strong> <?php echo htmlspecialchars($booking_details['dropoffDate']); ?></li>
            <li><strong>Total Cost:</strong> MWK <?php echo number_format(htmlspecialchars($booking_details['payment']), 2); ?></li>
        </ul>
        <p>Thank you for booking with us! We will be in touch shortly.</p>
        <a href="user-dashboard.php"><button>Go to Manage Bookings</button></a>
    </div>
</body>

</html>