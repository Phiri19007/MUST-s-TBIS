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

// Updated SQL query to exclude the current user
$sql = "SELECT id, firstname, middlename, lastname, Gender, `Date-of-birth`, phone, email, `Id-number`, username, status, profile_photo, `reg-date` 
        FROM `registered-users` 
        WHERE id != $userId and status ='active'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fleet.css">
    <title>Dashboard</title>
    <style>
        /* General Table Styling */
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

        /* User Profile and List Styling */
        .user-container {
            display: flex;
            justify-content: space-between;
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .user-container img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }

        .user-container .text {
            flex-grow: 1;
        }

        .user-container .text b {
            color: #490b3d;
        }

        .user-container .buttons {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }


        .delete-btn {
            background-color: red;
            color: white;
            padding: 8px 12px;
            margin-bottom: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
            margin-top: 300px;
        }


        .delete-btn:hover {
            background-color: #f1f1f1;
            color: #490b3d;
            cursor: pointer;
        }

        .logout-button {
            background-color: #490b3d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            border: none;
        }

        .logout-button:hover {
            background-color: #fff;
            color: #490b3d;
        }
    </style>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this user?")) {
                deleteUser(id); // Pass the correct id
            }
        }

        function deleteUser(id) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_user.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Check if deletion was successful
                    if (xhr.responseText === "success") {
                        alert("User deleted successfully!");
                        location.reload();
                    } else {
                        alert("Error deleting user.");
                    }
                }
            };
            xhr.send("id=" + id); // Send the correct id
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
            <h1>Users</h1>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="user-container">';
                    echo '<img src="' . $row["profile_photo"] . '" alt="profile photo">';
                    echo '<div class="text">
                        <h3>' . $row["firstname"] . ' ' . $row["lastname"] . '</h3>
                        <p><b>Gender:</b> ' . $row["Gender"] . '</p>
                        <p><b>D.O.B:</b> ' . $row["Date-of-birth"] . '</p>
                        <p><b>Contact:</b> ' . $row["phone"] . '</p>
                        <p><b>Email:</b> ' . $row["email"] . '</p>
                        <p><b>Id Number:</b> ' . $row["Id-number"] . '</p>
                        <p><b>Username:</b> ' . $row["username"] . '</p>
                    </div>';
                    echo '<div class="buttons">
                        <button class="delete-btn" onclick="confirmDelete(' . $row["id"] . ')">Delete</button>
                    </div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No users found.</p>";
            }
            ?>
        </div>
    </div>
</body>

</html>