<?php
session_start(); // Start session handling
if (!isset($_SESSION['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['id'];
include("database.php");

// Fetch all cars
$select_cars = $conn->query("SELECT id, name, type, seats, price, image, fuel_consumption FROM cars WHERE Status !='deleted'");

// Fetch fuel data
$fuel_query = "SELECT name, fuel_usage, image FROM cars";
$fuel_data = $conn->query($fuel_query);

// Fetch maintenance data
$maintenance_query = "SELECT name, Status, image, distance, maintenance FROM cars WHERE Status !='deleted'";
$maintenance_data = $conn->query($maintenance_query);

// Handle update request
if (isset($_POST['update'])) {
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $seats = $_POST['seats'];
    $price = $_POST['price'];
    $fuel_consumption = $_POST['fuel_consumption'];

    $update_stmt = $conn->prepare("UPDATE cars SET name=?, type=?, seats=?, price=?, fuel_consumption=? WHERE id=?");
    if ($update_stmt) {
        $update_stmt->bind_param("ssdddi", $name, $type, $seats, $price, $fuel_consumption, $edit_id);
        if (!$update_stmt->execute()) {
            echo "Error updating record: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Error preparing update statement: " . $conn->error;
    }
    header("Location: fleet_management.php");
    exit;
}
// Handle car delete
if (isset($_POST['delete_id'])) {
    $carId = intval($_POST['delete_id']);
    $status = "deleted";

    // Begin a transaction to ensure consistency
    $conn->begin_transaction();

    try {
        // Update bookings
        $deleteBookingsQuery = $conn->prepare("UPDATE bookings SET Status = ? WHERE car_id = ?");
        $deleteBookingsQuery->bind_param("si", $status, $carId);
        $deleteBookingsQuery->execute();

        // Update car
        $deleteCarQuery = $conn->prepare("UPDATE cars SET Status = ? WHERE id = ?");
        $deleteCarQuery->bind_param("si", $status, $carId);
        $deleteCarQuery->execute();

        $conn->commit(); // Commit the transaction
        header("Location: fleet_management.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback(); // Rollback the transaction on error
        die("Error deleting car: " . $e->getMessage());
    }
}



$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/fleet.css">
    <style>

    </style>
    <script></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Check the fragment in the URL (after the `#`)
            const section = window.location.hash;

            // If the URL contains '#fuel-management-section', show the fuel section
            if (section === '#fuel-management-section') {
                toggleFuelManagement();
            }
            // If the URL contains '#maintenance-section', show the maintenance section
            else if (section === '#maintenance-section') {
                toggleMaintenance();
            }


        });

        function confirmDelete() {
            return confirm("Are you sure you want to delete this car? ");
        }

        function openModal(carId) {
            document.getElementById("modal-" + carId).style.display = "block";
        }

        function closeModal(carId) {
            document.getElementById("modal-" + carId).style.display = "none";
        }

        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function toggleFuelManagement() {
            const fuelSection = document.getElementById('fuel-management-section');
            const carContainer = document.getElementById('car-container');
            const maintenanceSection = document.getElementById('maintenance-section');
            const addCarButton = document.getElementById('add-car-button');
            const maintenanceButton = document.getElementById('maintenance-button');

            if (fuelSection.style.display === 'none' || fuelSection.style.display === '') {
                fuelSection.style.display = 'block';
                carContainer.style.display = 'none';
                maintenanceSection.style.display = 'none';
                addCarButton.style.display = 'none';
                maintenanceButton.style.display = 'none';
            } else {
                fuelSection.style.display = 'none';
                carContainer.style.display = 'block';
                maintenanceSection.style.display = 'none';
                addCarButton.style.display = 'inline';
                maintenanceButton.style.display = 'inline';
            }
        }

        function toggleMaintenance() {
            const maintenanceSection = document.getElementById('maintenance-section');
            const carContainer = document.getElementById('car-container');
            const fuelSection = document.getElementById('fuel-management-section');
            const addCarButton = document.getElementById('add-car-button');
            const maintenanceButton = document.getElementById('maintenance-button');

            if (maintenanceSection.style.display === 'none' || maintenanceSection.style.display === '') {
                maintenanceSection.style.display = 'block';
                carContainer.style.display = 'none';
                fuelSection.style.display = 'none';
                addCarButton.style.display = 'none';
                maintenanceButton.style.display = 'none';
            } else {
                maintenanceSection.style.display = 'none';
                carContainer.style.display = 'block';
                fuelSection.style.display = 'none';
                addCarButton.style.display = 'inline';
                maintenanceButton.style.display = 'inline';
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <?php
        include("include/profile.php");
        ?>
        <div class="sidebar">
            <h2>Side Panel</h2>
            <?php
            include("include/sidepanel.php");
            ?>
        </div>

        <div class="main-content">
            <h1>Fleet Management</h1>

            <div class="car-container" id="car-container">
                <div class="cars">
                    <div class="box-container">
                        <?php
                        if ($select_cars->num_rows > 0) {
                            while ($fetch = $select_cars->fetch_assoc()) {
                        ?>
                                <div class="car-box">
                                    <img src="uploaded_image/<?php echo htmlspecialchars($fetch['image']); ?>" alt="Car Image" class="car-image" style="width:100%" />
                                    <div class="car-content">
                                        <h3 class="name"><?php echo htmlspecialchars($fetch['name']); ?></h3>
                                        <p>Type: <?php echo htmlspecialchars($fetch['type']); ?> &nbsp; Seats: <?php echo htmlspecialchars($fetch['seats']); ?></p>
                                        <p class="price">MK <?php echo number_format($fetch['price'], 2); ?> per day</p>
                                    </div>
                                    <button onclick="openModal(<?php echo $fetch['id']; ?>)" class="edit-button">Edit</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="delete_id" value="<?php echo $fetch['id']; ?>">
                                        <button type="submit" class="delete-button">Delete</button>
                                    </form>
                                </div>

                                <!-- Edit Modal -->
                                <div id="modal-<?php echo $fetch['id']; ?>" class="modal">
                                    <div class="modal-content">
                                        <span class="close" onclick="closeModal(<?php echo $fetch['id']; ?>)">&times;</span>
                                        <h2>Edit Car</h2>
                                        <form method="POST">
                                            <input type="hidden" name="edit_id" value="<?php echo $fetch['id']; ?>">
                                            <label for="name">Car Name:</label>
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($fetch['name']); ?>" required><br>

                                            <label for="type">Type:</label>
                                            <input type="text" name="type" value="<?php echo htmlspecialchars($fetch['type']); ?>" required><br>

                                            <label for="seats">Seats:</label>
                                            <input type="number" name="seats" value="<?php echo htmlspecialchars($fetch['seats']); ?>" required><br>

                                            <label for="price">Price:</label>
                                            <input type="number" name="price" value="<?php echo htmlspecialchars($fetch['price']); ?>" step="0.01" required><br>

                                            <label for="fuel_consumption">Fuel Consumption (per kilometer):</label>
                                            <input type="number" min="0" name="fuel_consumption" value="<?php echo htmlspecialchars($fetch['fuel_consumption']); ?>" step="0.01" required><br>

                                            <button type="submit" name="update">Update Car</button>
                                        </form>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo "<p>No cars available.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <button id="add-car-button" class="add-car-button" onclick="window.location.href='addcar.php'" style="margin-left: 70%;">Add Car</button>
            </div>


            <!-- Fuel Management Section -->
            <div class="fuel-management-section" id="fuel-management-section" style="display:none;">
                <h2>Fuel Management Records</h2>
                <?php
                if ($fuel_data->num_rows > 0) {
                    while ($row = $fuel_data->fetch_assoc()) {
                        echo '<div class="fuel-item">';
                        echo '<img src="uploaded_image/' . htmlspecialchars($row['image']) . '" alt="Car Image">';
                        echo '<div>';
                        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
                        echo '<p>Fuel Used: ' . htmlspecialchars($row['fuel_usage']) . ' L/km</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No fuel data available.</p>';
                }
                ?>
                <button class="back-button" onclick="toggleFuelManagement()">Back</button>
            </div>

            <!-- Maintenance Section -->
            <div class="maintenance-section" id="maintenance-section" style="display:none;">
                <h2>Maintenance Records</h2>
                <button class="back-button" onclick="toggleMaintenance()">Back</button>
                <?php
                if ($maintenance_data->num_rows > 0) {
                    while ($row = $maintenance_data->fetch_assoc()) {
                        echo '<div class="maintenance-item">';
                        echo '<img src="uploaded_image/' . htmlspecialchars($row['image']) . '" alt="Car Image">';
                        echo '<div>';
                        echo '<h4>' . htmlspecialchars($row['name']) . '</h4>';
                        echo '<p>Status: ' . htmlspecialchars($row['Status']) . '</p>';
                        echo '<p>Maintenance: ' . htmlspecialchars($row['maintenance']) . '</p>';
                        echo '<p>Distance Covered: ' . htmlspecialchars($row['distance']) . ' km</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No maintenance records available.</p>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>