<?php
session_start(); // Start session handling
if (!isset($_SESSION['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['id'];
include("database.php");
$stmt = $conn->prepare("SELECT username, role, profile_photo FROM `registered-users` WHERE id = ? and role ='admin'");
if (!$stmt) {
    // If the query preparation fails, show an error message
    die("Database query error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found with the given user ID
$user = null;
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "No user found or role mismatch";
    header("Location: login.php");
    exit();
}

// Close the prepared statement
$stmt->close();
$sql = "SELECT driver_ID, first_name, last_name, Gender, Date_of_birth, phone_number, email, `status` FROM driver_details WHERE status !='deleted'";
$result = $conn->query($sql);
if (isset($_POST['driver_id'], $_POST['first_name'], $_POST['last_name'], $_POST['gender'], $_POST['dob'], $_POST['phone_number'], $_POST['email'], $_POST['status'])) {

    // Sanitize and validate inputs
    $driverID = intval($_POST['driver_id']);
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $gender = trim($_POST['gender']);
    $dob = $_POST['dob']; // Assuming it's in a valid format, e.g., 'YYYY-MM-DD'
    $phone = trim($_POST['phone_number']);
    $email = trim($_POST['email']);
    $status = trim($_POST['status']);

    // Validate phone number format (basic validation)
    if (!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        echo "Invalid phone number format.";
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    // Prepare an SQL query to update the driver's details
    $sql = "UPDATE driver_details SET first_name = ?, last_name = ?, gender = ?, date_of_birth = ?, phone_number = ?, email = ?, status = ? WHERE driver_id = ?";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameters
        $stmt->bind_param("sssssssi", $firstName, $lastName, $gender, $dob, $phone, $email, $status, $driverID);

        // Execute the query
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "success";  // Return success response
            } else {
                echo "No changes were made. Please check if the driver exists or if the data is the same.";
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fleet.css">
    <title>Dashboard</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .modal-content {
            background-color: rgb(217, 217, 217);
            padding: 20px;
            border-radius: 5px;
            width: 1000px;
            position: relative;

        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            cursor: pointer;
            font-size: 18px;
        }

        input {
            width: 400px;
            height: 30px;
            font-size: 16px;
        }

        .delete-btn,
        .edit-btn {
            background-color: #490b3d;
            color: white;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            width: 100px;
        }

        .delete-btn {
            margin-left: 60%;
            background-color: red;
        }


        .delete-btn:hover,
        .edit-btn:hover {
            background-color: #f1f1f1;
            color: #490b3d;
            cursor: pointer;

        }
    </style>
    <script>
        function openModal(bookingId, currentStatus) {
            document.getElementById("modal").style.display = "block";
            document.getElementById("booking-id").value = bookingId;
            document.getElementById("status-select").value = currentStatus;
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function openEditModal(driver) {
            document.getElementById('edit-driver-id').value = driver.driver_ID;
            document.getElementById('edit-first-name').value = driver.first_name;
            document.getElementById('edit-last-name').value = driver.last_name;
            document.getElementById('edit-gender').value = driver.Gender;
            document.getElementById('edit-dob').value = driver.Date_of_birth;
            document.getElementById('edit-phone').value = driver.phone_number;
            document.getElementById('edit-email').value = driver.email;
            document.getElementById('edit-status').value = driver.status;
            document.getElementById('editModal').style.display = 'flex';
        }

        // Close edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Confirm edit
        function confirmEdit() {
            if (confirm("Are you sure you want to save these changes?")) {
                saveEdit();
            }
        }

        // Save changes via AJAX
        function confirmEdit() {
            // Get form data from the modal
            const driverID = document.getElementById('edit-driver-id').value;
            const firstName = document.getElementById('edit-first-name').value;
            const lastName = document.getElementById('edit-last-name').value;
            const gender = document.getElementById('edit-gender').value;
            const dob = document.getElementById('edit-dob').value;
            const phone = document.getElementById('edit-phone').value;
            const email = document.getElementById('edit-email').value;
            const status = document.getElementById('edit-status').value;

            // Validate inputs (you can enhance this validation as needed)
            if (!driverID || !firstName || !lastName || !gender || !dob || !phone || !email || !status) {
                alert("All fields are required.");
                return;
            }

            // Create an object to hold the data
            const data = {
                driver_id: driverID,
                first_name: firstName,
                last_name: lastName,
                gender: gender,
                dob: dob,
                phone_number: phone,
                email: email,
                status: status
            };

            // Send data via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_driver.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            // Prepare data to be sent
            let params = "";
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    params += `${key}=${encodeURIComponent(data[key])}&`;
                }
            }
            params = params.slice(0, -1); // Remove the trailing '&'

            // Handle the response
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = xhr.responseText;
                    if (response === "success") {
                        alert("Driver details updated successfully!");
                        closeEditModal();
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert("Error updating driver: " + response);
                    }
                }
            };

            xhr.send(params); // Send data to the server
        }

        function confirmDelete(driverID) {
            if (confirm("Are you sure you want to delete this driver?")) {
                deleteDriver(driverID);
            }
        }

        function deleteDriver(driverID) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_driver.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText === "success") {
                        alert("Driver deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error deleting driver.");
                    }
                }
            };
            xhr.send("driver_ID=" + driverID);
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="user-profile">
            <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="User Profile Photo" id="profilePhoto">
            <div class="user">
                <h3 id="userName"><?php echo htmlspecialchars($user['username']); ?></h3>
            </div>
            <form action="logout.php" method="POST" onsubmit="return confirmLogout();">
                <button type="submit" class="logout-button">Log out</button>
            </form>
        </div>
        <div class="sidebar">
            <h2>Side Panel</h2>
            <?php
            include("include/sidepanel.php");
            ?>
        </div>

        <div class="main-content">
            <h1>Drivers</h1>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='user-container'>";
                    echo "<div class='text'>" . $row["first_name"] . " " . $row["last_name"] . "<br><br><b>Gender:</b> " . $row["Gender"] . "<br><b>D.O.B:</b> " . $row["Date_of_birth"] . "<br><b>Contact:</b> " . $row["phone_number"] . "<br><b>Email:</b> " . $row["email"] . "<br><b>Status:</b> " . $row["status"] . "<br><br>";
                    echo "<button class='edit-btn' onclick='openEditModal(" . json_encode($row) . ")'>Edit</button>";
                    echo "<button class='delete-btn' onclick='confirmDelete(" . $row["driver_ID"] . ")'>Delete</button>";
                    echo "</div><hr>";
                    echo "</div>";
                }
            }
            ?>
            <div class="button-container">
                <button id="add-car-button" class="add-car-button" onclick="window.location.href='adddriver.php'" style="margin-left: 70%;">Add Driver</button>
            </div>

            <!-- Edit Modal -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close-btn" onclick="closeEditModal()">&times;</span>
                    <h2>Edit Driver</h2>
                    <form id="editForm">
                        <input type="hidden" id="edit-driver-id">
                        <label>First Name:</label>
                        <input type="text" id="edit-first-name">
                        <label>Last Name:</label>
                        <input type="text" id="edit-last-name"><br><br>
                        <label>Gender:</label>
                        <input type="text" id="edit-gender"><br><br>
                        <label>Date of Birth:</label>
                        <input type="date" id="edit-dob"><br><br>
                        <label>Phone Number:</label>
                        <input type="text" id="edit-phone">
                        <label>Email:</label>
                        <input type="email" id="edit-email"><br><br>

                        <label for="status">Driver Status:</label>
                        <select id="edit-status" name="status">
                            <option value="active">Active</option>
                            <option value="suspended">Suspended</option>
                            <option value="retired">Retired</option>
                            <option value="resigned">Resigned</option>
                            <option value="fired">Fired</option>
                        </select>

                        <button type="button" onclick="confirmEdit()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
</body>

</html>