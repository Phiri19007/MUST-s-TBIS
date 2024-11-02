<?php
session_start();
include("database.php");

// Handle login logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING);

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("SELECT id, username, password, role FROM `registered-users` WHERE BINARY username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user with that username exists
    if ($result->num_rows === 1) {
        $userData = $result->fetch_assoc();
        $hashed_password = $userData['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Regenerate session ID
            session_regenerate_id(true);
            // Store user information in session
            $_SESSION['id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['role'] = $userData['role'];

            // Check user role and redirect accordingly
            if ($userData['role'] === 'admin') {
                header("Location: registration.php");
            } else {
                // Check for a record in the booking table for the user
                $userId = $userData['id'];
                $query = "SELECT COUNT(*) as bookingCount FROM bookings WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $bookingResult = $stmt->get_result();
                $bookingData = $bookingResult->fetch_assoc();

                if ($bookingData['bookingCount'] > 0) {
                    header("Location: user-dashboard.php");
                } else {
                    header("Location: booking_page.php");
                }
            }
            exit(); // Ensure no further code is executed
        } else {
            $_SESSION['error_message'] = "Invalid username or password.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid username or password.";
    }

    // Close the statement
    $stmt->close();

    // Redirect back to the login page to avoid resubmission
    header("Location: " . htmlspecialchars($_SERVER["PHP_SELF"]));
    exit();
}
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']); // Clear the message after displaying it

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUST's TBIS</title>
    <link rel="stylesheet" href="style.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('popupMessage');
            if (popup) {
                popup.style.display = 'block';
                setTimeout(function() {
                    popup.style.display = 'none';
                }, 3000); // Hide after 3 seconds
            }
            // Other existing JavaScript code
        });
    </script>
    <style>
        .home-icon {
            margin: 0 10px;
            vertical-align: middle;
            cursor: pointer;
            transition: transform 0.3s;
            color: #f1b814;
        }

        .home-icon:hover {
            transform: scale(3.1);
        }

        #popupMessage {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
</head>

<body>
    <div class="background-image">
        <header>
            <nav>
                <div class="title">
                    <h3><a href="index.php">MUST's TBIS</a></h3>
                </div>
            </nav>
        </header>
    </div>
    <div class="overlay"></div>
    <div class="content">
        <div>
            <p class="name-web">Malawi University of Science and Technology’s <span>Transportation Booking Information System</span></p>
        </div>
        <div>
            <?php
            // Display the message only if it's set
            if (!empty($message)) {
                echo "<div id='popupMessage'>$message</div>";
            }
            ?>
            <form id="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" autocomplete="off">
                <h4>Sign in</h4>
                <input type="text" name="username" placeholder="username" required class="inputbox"><br>
                <div class="input-password">
                    <input type="password" name="password" id="password" placeholder="password" maxlength="60" required class="inputbox">
                    <img src="image/eye-close.png" class="eye-close-login" id="togglePassword">
                    <br>
                </div>
                <input type="submit" value="Login" class="buttonlogin"><br>
            </form>
            <p>Don’t have an account? Create an account, <br><a href="registration.php" class="regLink">click here to register</a></p>
            <p style="position: absolute; bottom: 10px; left: 10px;">
                <a href="index.php" class="regLink">
                    <img src="image/home_icon.png" alt="Link To Home" title="Back to Home" class="home-icon">
                </a>
            </p>
        </div>
    </div>
</body>

</html>