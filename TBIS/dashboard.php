<?php
session_start(); // Start session handling
if (!isset($_SESSION['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['id'];

include("database.php");

// Updated SQL query to only include users who have a corresponding booking
$sql_user_booking_details = "
    SELECT u.id, u.firstname, u.lastname, u.phone, u.email, 
           b.id AS booking_id, b.startingLocation, 
           b.Pickupdate, b.dropoffdate, b.num_passengers, b.Status
    FROM `registered-users` u
    INNER JOIN bookings b ON u.id = b.user_id"; // INNER JOIN to ensure matching records

$personal_booking_result = $conn->query($sql_user_booking_details);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fleet.css">
    <title>Dashboard</title>
    <style>
        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
            color: #490b3d;
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background-color: #490b3d;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: whitesmoke;
            color: black;
            cursor: pointer;
        }

        /* Loading Spinner */
        #loadingSpinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            background-color: rgba(0, 0, 0, 0.7);
            padding: 20px;
            color: white;
            border-radius: 5px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                showInfoModal();
            }, 500);
        });

        function showInfoModal() {
            document.getElementById("infoModal").style.display = "block";
        }

        function closeInfoModal() {
            document.getElementById("infoModal").style.display = "none";
        }

        function openModal(bookingId, currentStatus) {
            document.getElementById("modal").style.display = "block";
            document.getElementById("booking-id").value = bookingId;
            document.getElementById("status-select").value = currentStatus;
        }

        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        function saveStatus() {
            const bookingId = document.getElementById("booking-id").value;
            const status = document.getElementById("status-select").value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        alert("Booking status updated successfully!");
                        closeModal();
                        location.reload();
                    } else {
                        alert("Error updating status: " + xhr.responseText);
                    }
                }
            };

            xhr.send(`booking_id=${bookingId}&status=${status}`);
        }

        function runMaintenance() {
            console.log("AJAX request triggered");
            $("#loadingSpinner").show(); // Show loading spinner
            $.ajax({
                url: "run_maintenance.php",
                type: "POST",
                data: {
                    action: 'run'
                },
                success: function(response) {
                    alert(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + error);
                    alert("There was an error running the maintenance tasks.");
                },
                complete: function() {
                    $("#loadingSpinner").hide(); // Hide loading spinner
                }
            });
        }
    </script>
</head>

<body>
    <div class="container">
        <?php include("include/profile.php"); ?>
        <div class="sidebar">
            <h2>Side Panel</h2>
            <?php include("include/sidepanel.php"); ?>
            <button onclick="runMaintenance()">Run Maintenance</button>
        </div>

        <div class="main-content">
            <h1>Booking and Scheduling</h1>

            <?php
            // Check if there are any bookings to display
            if ($personal_booking_result->num_rows > 0) {
                echo "<table>";
                echo "<tr>
                        <th>Booking ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Pickup Location</th>
                        <th>Pickup DateTime</th>
                        <th>Dropoff DateTime</th>
                        <th>Number of Passengers</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>";

                // Loop through each booking and display the details
                while ($row = $personal_booking_result->fetch_assoc()) {
                    // Format the pickup and dropoff date with AM/PM
                    $formatted_pickup_date = date('Y-m-d h:i A', strtotime($row['Pickupdate']));
                    $formatted_dropoff_date = date('Y-m-d h:i A', strtotime($row['dropoffdate']));

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["booking_id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["firstname"]) . " " . htmlspecialchars($row["lastname"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["startingLocation"]) . "</td>";
                    echo "<td>" . $formatted_pickup_date . "</td>";
                    echo "<td>" . $formatted_dropoff_date . "</td>";
                    echo "<td>" . htmlspecialchars($row["num_passengers"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
                    echo "<td><button onclick='openModal(" . htmlspecialchars($row["booking_id"]) . ", \"" . htmlspecialchars($row["Status"]) . "\")'>Edit Status</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "No bookings found.";
            }
            ?>

            <!-- Information Modal -->
            <div id="infoModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeInfoModal()">&times;</span>
                    <h2>Important Information</h2>
                    <p>Please click the "Run Maintenance" button to update the fleet information.</p>
                    <button onclick="closeInfoModal()">OK</button>
                </div>
            </div>

            <!-- Edit Status Modal -->
            <div id="modal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeModal()">&times;</span>
                    <h2>Edit Booking Status</h2>
                    <form>
                        <input type="hidden" id="booking-id">
                        <label for="status-select">Select Status:</label>
                        <select id="status-select">
                            <option value="Pending">Pending</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                        <br><br>
                        <button type="button" onclick="saveStatus()">Save</button>
                    </form>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner">Loading...</div>
        </div>
    </div>
</body>

</html>