<?php
include("./database.php");
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
// Handle profile update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $newUsername = $_POST['username'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $newPhoto = $_FILES['profile_photo']['name'];

    // Handle file upload if a new photo is provided
    if (!empty($newPhoto)) {
        $new_image_name = uniqid("IMG-", true);
        $targetDir = 'uploaded_image/' . $new_image_name;
        $targetFile = $targetDir . basename($_FILES['profile_photo']['name']);
        move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile);
    } else {
        $targetFile = $user['profile_photo']; // Keep the old photo
    }

    $updateStmt = $conn->prepare("UPDATE `registered-users` SET username = ?, password = ?, profile_photo = ? WHERE id = ?");
    $updateStmt->bind_param("sssi", $newUsername, $newPassword, $targetFile, $userId);

    if ($updateStmt->execute()) {
        echo "<script>
        alert('Profile updated successfully!');
        setTimeout(function() {
            window.location.href = 'logout.php';
        }, 1000); // Redirect after 1 seconds (1000 milliseconds)
    </script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    $updateStmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<style>
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
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<script>
    function openModal1() {
        document.getElementById('profileModal').style.display = 'block';
    }

    function closeModal1() {
        document.getElementById('profileModal').style.display = 'none';
    }
</script>

<body>
    <div class="user-profile">
        <img src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="User Profile Photo" id="profilePhoto" onclick="openModal1()" style="cursor: pointer; " title=" Click to update Username, Password and Profile image">
        <div class="user">
            <h3 id="userName"><?php echo htmlspecialchars($user['username']); ?></h3>
        </div>
        <form action="logout.php" method="POST" onsubmit="return confirmLogout();">
            <button type="submit" class="logout-button">Log out</button>
        </form>
    </div>
    <!-- Modal for editing user details -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal1()">&times;</span>
            <h2>Edit Profile</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter new password" required><br>

                <label for="profile_photo">Profile Photo:</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*"><br><br>

                <button type="submit" name="update">Update Profile</button>
            </form>
        </div>
    </div>
</body>

</html>