<?php
// Include database connection (you can modify this as per your project structure)
include("database.php");

// Ensure all necessary fields are received
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
} else {
    echo "All fields are required.";
}
