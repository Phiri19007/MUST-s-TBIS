<?php
session_start();
include("database.php");
// Display error message if it exists
if (isset($_SESSION['error_message'])) {
    echo "<p style='color: red;'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);  // Clear the error message after displaying
}

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    // Fetch the user role and details from the database
    $query = "SELECT role, firstname, lastname, phone, email FROM `registered-users` WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $userRole = htmlspecialchars($row['role'] ?? '');
        $firstName = htmlspecialchars($row['firstname'] ?? '');
        $lastName = htmlspecialchars($row['lastname'] ?? '');
        $phoneNumber = htmlspecialchars($row['phone'] ?? '');
        $email = htmlspecialchars($row['email'] ?? '');

        // Set the booking form action based on user role
        $formAction = ($userRole === "Guest") ? "calculations.php" : "INTprocess_booking.php";
    } else {
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Form</title>
    <link rel="stylesheet" href="css/booking.css">
    <style>
        body {
            color: #490b3d;
        }

        .car-card {
            cursor: pointer;
            background-color: whitesmoke;
            border-radius: 5px;
            margin-right: 5px;
        }

        .car-card img {
            margin: 2px;
        }

        .car-card.selected {
            border: 2px solid #007BFF;
            background-color: rgba(0, 123, 255, 0.1);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the current date and time
            const now = new Date();
            const yyyy = now.getFullYear();
            let mm = now.getMonth() + 1; // Months are 0-based
            let dd = now.getDate();
            let hh = now.getHours();
            let min = now.getMinutes();

            // Ensure that single digit months, days, hours, and minutes are padded with a leading zero
            if (mm < 10) mm = '0' + mm;
            if (dd < 10) dd = '0' + dd;
            if (hh < 10) hh = '0' + hh;
            if (min < 10) min = '0' + min;

            // Format the date and time as yyyy-mm-ddThh:mm
            const formattedDateTime = `${yyyy}-${mm}-${dd}T${hh}:${min}`;

            // Set the 'min' attribute for both pickup and dropoff datetime inputs
            document.getElementById("pickup-date").setAttribute("min", formattedDateTime);
            document.getElementById("dropoff-date").setAttribute("min", formattedDateTime);
        });
    </script>

<body>
    <div class="progress-bar">
        <div id="step1" class="step active-step">1</div>
        <div class="step-line"></div>
        <div id="step2" class="step inactive-step">2</div>
        <div class="step-line"></div>
        <div id="step3" class="step inactive-step">3</div>
    </div>
    <form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data" id="booking-form">
        <!-- Page 1 -->
        <div class="form-container" id="page1">
            <span>Note: please filing in all the details before clicking next </span><br>
            <h2><span id="bookingType"><?php echo ($userRole === "Guest") ? "External" : "Internal"; ?></span> Booking Details</h2>
            <!-- Form elements for pickup, drop-off, and passenger count -->


            <label for="pickup-location">Pickup Location</label><br>
            <div class="form-group">
                <input type="text" id="pickup-location" name="pickup-location" placeholder="Pickup location" required readonly />
                <label for="pickup-date">Pickup Date and Time
                    <input type="datetime-local" id="pickup-date" name="pickup-date" required max="2025-01-30T23:59" />
                </label>
            </div>
            <label for="dropoff-location">Dropoff Location</label><br>
            <div class="form-group">
                <select id="dropoff-location" name="dropoff-location" required>
                    <option value="">Select a Dropoff Location</option>
                    <!-- Options will be populated here by JavaScript -->
                </select>
                <label for="dropoff-date">Return Date and Time
                    <input type="datetime-local" id="dropoff-date" name="dropoff-date" required max="2025-01-30T23:59" />
                </label>
            </div>
            <label for="num-passengers"></label>Number of Passengers<br>
            <div class="form-group">
                <input type="number" min="1" max="66" maxlength="2" id="num-passengers" name="num-passengers" placeholder="Number of passengers" required />
            </div>
            <div class="buttons">
                <button type="button" class="clear-btn" onclick="clearForm()">Clear</button>
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <div class="form-container hidden" id="page2">
            <p>Note: select a car card </p>
            <h2>Available Cars</h2>
            <div class="form-group" id="available-cars"></div>
            <input type="hidden" id="selected-car-id" name="selected_car_id" required>
            <div class="buttons">
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <!-- Page 3: Personal Details with Pre-filled Values -->
        <div class="form-container hidden" id="page3">
            <h2>Personal Details</h2>
            <div class="form-group">
                <input type="text" id="first-name" disabled placeholder="First name" value="<?php echo $firstName; ?>">
                <input type="text" id="last-name" disabled placeholder="Last name" value="<?php echo $lastName; ?>">
            </div>
            <div class="form-group">
                <input type="tel" id="phone-number" disabled placeholder="Phone number" value="<?php echo $phoneNumber; ?>">
                <input type="email" id="email" disabled placeholder="Email" value="<?php echo $email; ?>">
            </div>
            <input type="hidden" name="user-id" value="<?php echo $userId; ?>">
            <input type="checkbox" id="confirm" name="confirm" required>
            <label for="confirm">Select to confirm that the information provided is true, otherwise refresh to restart the booking process</label>
            <div class="buttons">
                <button type="submit" class="next-btn">Submit</button>
            </div>
        </div>
    </form>

    <script>
        const userRole = "<?php echo htmlspecialchars($userRole); ?>"; // Sanitize PHP variable output
    </script>

    <script src="bookingform.js" defer></script>
</body>

</html>