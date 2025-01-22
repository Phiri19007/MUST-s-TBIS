<?php
session_start();
include("database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input sanitization
    $firstname = filter_input(INPUT_POST, "fname", FILTER_SANITIZE_SPECIAL_CHARS);
    $middlename = filter_input(INPUT_POST, "mname", FILTER_SANITIZE_SPECIAL_CHARS);
    $lastname = filter_input(INPUT_POST, "lname", FILTER_SANITIZE_SPECIAL_CHARS);
    $gender = filter_input(INPUT_POST, "Gender", FILTER_SANITIZE_SPECIAL_CHARS);
    $date_of_birth = filter_input(INPUT_POST, "Date-of-birth", FILTER_SANITIZE_SPECIAL_CHARS);
    $phone = filter_input(INPUT_POST, "phone-number", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, "Role", FILTER_SANITIZE_SPECIAL_CHARS);
    $id = $role === 'Guest' ? null : filter_input(INPUT_POST, "ID", FILTER_SANITIZE_SPECIAL_CHARS); // Skip ID for Guests
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $confirm_password = filter_input(INPUT_POST, "confirmpassword", FILTER_SANITIZE_SPECIAL_CHARS);
    $status = "active";

    // Store form data in session to persist it across page reloads
    $_SESSION['form_data'] = [
        'fname' => $firstname,
        'mname' => $middlename,
        'lname' => $lastname,
        'Gender' => $gender,
        'Date-of-birth' => $date_of_birth,
        'phone-number' => $phone,
        'email' => $email,
        'Role' => $role,
        'ID' => $id,
        'username' => $username
    ];

    // Validation checks (username, phone, id, email checks)
    if (isUsernameTaken($conn, $username)) {
        $_SESSION['message'] = "Username is already taken.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isPhoneTaken($conn, $phone)) {
        $_SESSION['message'] = "Phone number is already taken.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isIDTaken($conn, $id) && $role !== 'Guest') {
        $_SESSION['message'] = "ID is already taken.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isEmailTaken($conn, $email)) {
        $_SESSION['message'] = "Email is already taken.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Password confirmation check
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Password hashing
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Handle image upload
    if (isset($_FILES['userimage']) && $_FILES['userimage']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $max_size = 8 * 1024 * 1024; // 8MB max size

        if (in_array(pathinfo($_FILES['userimage']['name'], PATHINFO_EXTENSION), $allowed_extensions) && $_FILES['userimage']['size'] <= $max_size) {
            $img_ex = pathinfo($_FILES['userimage']['name'], PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $new_image_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
            $image_path_upload = 'uploaded_image/' . $new_image_name;
            move_uploaded_file($_FILES['userimage']['tmp_name'], $image_path_upload);
        } else {
            $_SESSION['message'] = "Invalid image format or size.";
            $_SESSION['status'] = 'error';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $_SESSION['message'] = "Profile image is required.";
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Insert user data into database (exclude ID if role is Guest)
    if ($role === 'Guest') {
        $stmt = $conn->prepare("INSERT INTO `registered-users` (firstname, middlename, lastname, Gender, `Date-of-birth`, phone, email, role, username, status, password, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $firstname, $middlename, $lastname, $gender, $date_of_birth, $phone, $email, $role, $username, $status, $hash, $image_path_upload);
    } else {
        $stmt = $conn->prepare("INSERT INTO `registered-users` (firstname, middlename, lastname, Gender, `Date-of-birth`, phone, email, role, `Id-number`, username, status, password, profile_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $firstname, $middlename, $lastname, $gender, $date_of_birth, $phone, $email, $role, $id, $username, $status, $hash, $image_path_upload);
    }

    if ($stmt->execute()) {
        $_SESSION['registration_message'] = "You have successfully registered! Please log in.";
        $_SESSION['status'] = 'success';

        // Clear form data session after redirecting to avoid resubmission
        unset($_SESSION['form_data']);

        // Redirect to login page
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['message'] = "Registration failed: " . $stmt->error;
        $_SESSION['status'] = 'error';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $stmt->close();
}


// Function to check if the username is taken
function isUsernameTaken($conn, $username)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `registered-users` WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Function to check if the phone number is taken
function isPhoneTaken($conn, $phone)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `registered-users` WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}
// Function to check if the id is taken
function isIDTaken($conn, $id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `registered-users` WHERE `Id-number` = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

// Function to check if the email is taken
function isEmailTaken($conn, $email)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM `registered-users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUST's TBIS</title>
    <link rel="stylesheet" href="index.css">
    <style>
        #popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f44336;
            color: white;
            padding: 20px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
            width: 250px;
            height: 50px;
            text-align: center;
            font-size: 20px;
        }

        #popup.success {
            background-color: #4CAF50;
        }

        input[type="file"] {
            margin: 10px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('myForm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confpassword');
            const messageElement = document.getElementById('message'); // Renamed variable
            const emailInput = document.getElementById('email-input');
            const roleInputs = document.querySelectorAll('input[name="Role"]');
            const idInputContainer = document.getElementById('idInputContainer');
            const useridLabel = document.getElementById('useridLabel');
            const popup = document.getElementById('popup');
            const message = "<?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?>";
            const status = "<?php echo isset($_SESSION['status']) ? $_SESSION['status'] : ''; ?>";

            if (message) {
                popup.innerText = message;
                popup.className = status; // Apply the success or error class
                popup.style.display = 'block'; // Show the popup

                // Hide the popup after 5 seconds
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 5000);
            }

            // Form submission validation
            form.addEventListener('submit', function(event) {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    messageElement.textContent = 'Passwords do not match.'; // Use messageElement here
                    messageElement.style.color = 'red';
                    event.preventDefault(); // Prevent form submission
                    return;
                }

                // Clear message if passwords match
                messageElement.textContent = ''; // Clear message
            });

            // Toggle password visibility
            document.getElementById('togglePassword').addEventListener('click', function() {
                togglePasswordVisibility(passwordInput, this);
            });

            document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
                togglePasswordVisibility(confirmPasswordInput, this);
            });

            function togglePasswordVisibility(input, toggleButton) {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                toggleButton.src = type === 'password' ? 'image/eye-close.png' : 'image/eye-open.png';
            }


            function updateEmailPattern(selectedRole) {
                if (selectedRole === 'Student' || selectedRole === 'Staff') {
                    emailInput.pattern = "^[a-zA-Z0-9._%+-]+@must\\.ac\\.mw$"; // Pattern for must.ac.mw
                    emailInput.placeholder = "Enter Your Organization Email in the right Format";
                    emailInput.title = "The pattern must be example@must.ac.mw";
                    console.log(selectedRole);
                } else {
                    emailInput.pattern = "[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$"; // No specific pattern for Guest
                    emailInput.placeholder = "Enter email";
                    emailInput.title = "The pattern must be example@gmail.com";
                    console.log(selectedRole);
                }
            }

            // Function to handle changes in role selection
            function updateFormBasedOnRole() {
                const selectedRole = Array.from(roleInputs).find(input => input.checked).value;
                updateEmailPattern(selectedRole);
                checkRole(selectedRole);
            }

            // Function to toggle visibility of ID input fields based on role
            function checkRole(selectedRole) {
                if (selectedRole === 'Guest') {
                    idInputContainer.style.display = 'none'; // Hide ID input container for Guest
                } else {
                    idInputContainer.style.display = 'block'; // Show ID input container for Student or Staff
                    if (selectedRole === 'Staff') {
                        useridLabel.textContent = 'Employee Number:';
                    } else {
                        useridLabel.textContent = 'User ID:';
                    }
                }
            }

            // Initially update form based on selected role
            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateFormBasedOnRole(); // Update form elements when role changes
                });
            });

            // Call the function to initialize the email pattern based on pre-selected role
            updateFormBasedOnRole();

            function checkRole(selectedRole) {
                if (selectedRole === 'Guest') {
                    idInputContainer.style.display = 'none'; // Hide ID input container
                } else {
                    idInputContainer.style.display = 'block'; // Show ID input container
                    if (selectedRole === 'Staff') {

                        idInputContainer.innerHTML = getDepartmentSelect(); // Replace input with select
                    } else {

                        idInputContainer.innerHTML = getUserIdInput(); // Replace select with input
                    }
                }
            }

            function getDepartmentSelect() {
                return ` 
        <label id="useridLabel" style="margin-right: 8px; margin-top: 8px; " class="useridlabel">Employee Number:</label>
        <input type="text" name="ID" id="idInput" title ="ID field" class="userid" style="margin-left: -3px; width: 62%;" placeholder="employee number" required maxlength="10">
        `;
            }

            function getUserIdInput() {
                return `
        <label id="useridLabel" style="margin-right: 8px;" class="useridlabel">Reg No:</label>
        <input type="text" name="ID" id="idInput" title ="ID field" class="userid" style="margin-left: -3px;" placeholder="e.g., BIT-027-22" required maxlength="10">
        `;
            }
            roleInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateEmailPattern(this.value);
                });
            });

            // Event listeners for role changes
            roleInputs.forEach(input => {
                input.addEventListener('change', updateFormBasedOnRole);
            });

            // Initial check on page load
            updateFormBasedOnRole();
        });
    </script>

</head>

<body>
    <header>
        <nav>
            <div class="title">
                <h3><a href="index.php" title="Home">MUST's TBIS</a></h3>
            </div>
        </nav>
    </header>
    <div class="registration-form">
        <div id="popup" style="display:none;"></div>
        <form id="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <h1>Registration form for members</h1>
            <div class="form-elements">
                <label class="labelname">First Name</label><label class="labelname">Middle Name</label><label class="labelname">Last Name</label><br>
                <input type="text" name="fname" placeholder="First Name" maxlength="30" required class="reginput" pattern="[A-Za-z]+" title="Please enter letters only." value="<?php echo isset($_SESSION['form_data']['fname']) ? htmlspecialchars($_SESSION['form_data']['fname']) : ''; ?>">
                <input type="text" name="mname" placeholder="Middle Name" maxlength="30" class="reginput" pattern="[A-Za-z]+" title="Please enter letters only." value="<?php echo isset($_SESSION['form_data']['mname']) ? htmlspecialchars($_SESSION['form_data']['mname']) : ''; ?>">
                <input type="text" name="lname" placeholder="Last Name" maxlength="30" required class="reginput" pattern="[A-Za-z]+" title="Please enter letters only." value="<?php echo isset($_SESSION['form_data']['lname']) ? htmlspecialchars($_SESSION['form_data']['lname']) : ''; ?>"><br>
                <label class="formlabel">Gender</label>
                <input type="radio" name="Gender" value="male" required class="radioinput" <?php if (isset($_SESSION['form_data']['Gender']) && $_SESSION['form_data']['Gender'] == 'male') echo 'checked'; ?>><label>Male</label>
                <input type="radio" name="Gender" value="female" required class="radioinput" <?php if (isset($_SESSION['form_data']['Gender']) && $_SESSION['form_data']['Gender'] == 'female') echo 'checked'; ?>><label>Female</label><br>
                <label class="datelabel">Date of Birth</label>
                <input type="date" name="Date-of-birth" required class="reginput" min="1924-12-12" max="2013-01-01" value="<?php echo isset($_SESSION['form_data']['Date-of-birth']) ? htmlspecialchars($_SESSION['form_data']['Date-of-birth']) : ''; ?>">
                <label class="datelabel">Phone Number</label><input type="tel" name="phone-number" pattern="^(0[1-9][0-9]{8}|(\+265)[1-9][0-9]{8})$" placeholder="Enter Malawi phone number" title="The format for Malawi is e.g +265995089639 or 0995089639" class="reginput" maxlength="15" minlength="10" value="<?php echo isset($_SESSION['form_data']['phone-number']) ? htmlspecialchars($_SESSION['form_data']['phone-number']) : ''; ?>"><br>
                <label class="formlabel">Role in the Organization</label><br>
                <div class="radioinput2">
                    <input type="radio" name="Role" value="Student" required class="radioinput"
                        <?php if (isset($_SESSION['form_data']['Role']) && $_SESSION['form_data']['Role'] == 'Student') echo 'checked'; ?>
                        click="updateEmailPattern(selectedRole)">
                    <label>Student</label>

                    <input type="radio" name="Role" value="Staff" required class="radioinput"
                        <?php if (isset($_SESSION['form_data']['Role']) && $_SESSION['form_data']['Role'] == 'Staff') echo 'checked'; ?>
                        click="updateEmailPattern(selectedRole)">
                    <label>Staff</label>

                    <input type="radio" name="Role" value="Guest" required class="radioinput"
                        <?php if (isset($_SESSION['form_data']['Role']) && $_SESSION['form_data']['Role'] == 'Guest') echo 'checked'; ?>
                        click="updateEmailPattern(selectedRole)">
                    <label>Guest</label><br>
                </div>
                <div class="id-container" id="idInputContainer" style="display: flex; align-items: center;">
                    <label id="useridLabel" style="margin-right: 10px;" class="useridlabel">Reg No:</label>
                    <input type="text" name="ID" id="idInput" class="userid" title="ID field"
                        placeholder="e.g., BIT-027-22" value="<?php echo isset($_SESSION['form_data']['ID']) ? htmlspecialchars($_SESSION['form_data']['ID']) : ''; ?>"><br>
                </div>
                <div class="enterinforuser">
                    <label class="labeluser1" style="  margin-left: -40px;">Email</label>
                    <input type="email" name="email" id="email-input" placeholder="Email" maxlength="100" class="user"
                        title="The pattern must be example@must.ac.mw" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required><br>

                    <label class="labeluser1">Username</label>
                    <input type="text" name="username" placeholder="Username" maxlength="60" required class="user" value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>"><br>
                    <label class="labeluser1">Password</label>
                    <div class="input-password">
                        <input type="password" name="password" id="password" placeholder="Password" maxlength="60" required class="user1">
                        <img src="image/eye-close.png" class="eye-close" id="togglePassword"><br>
                        <label>Confirm Password</label>
                        <input type="password" name="confirmpassword" id="confpassword" placeholder="Confirm Password" maxlength="60" required class="user1">
                        <img src="image/eye-close.png" class="eye-close" id="toggleConfirmPassword">
                    </div>
                    <div id="message" style="color: red;"></div>
                </div>
            </div>
            <div class="col">
                <label for="carimage">Profile Image</label>
                <input id="file" type="file" name="userimage" required> <span>Allowed file: .jpg, .jpeg, .png and size must be less than 8 MB</span>
            </div>
            <div class="send-button">
                <div class="clear">
                    <input type="reset" value="Clear" class="clear-button">
                </div>
                <div class="Submit">
                    <input type="submit" value="Submit" class="submit-button">
                </div>
            </div>
        </form>
    </div>
</body>

</html>