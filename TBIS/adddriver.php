<?php
session_start(); // Start session handling
if (!isset($_SESSION['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['id'];
include("database.php");

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phoneNumber = $_POST['phone-number'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $status = "Active";

    // Emergency Contact
    $emergencyFirstName = $_POST['e-firstName'];
    $emergencyLastName = $_POST['e-lastName'];
    $emergencyGender = $_POST['e-Gender'];
    $emergencyPhoneNumber = $_POST['e-phone-number'];
    $emergencyEmail = $_POST['e-email'];
    $relationship = $_POST['relationship'];

    // Driver identification and credential
    $licencenumber = $_POST['licence-number'];
    $licenceCategory = $_POST['vehicle-code'];
    $yearsOfExperience = $_POST['license'];

    // Driving and incident history
    $previousAccident = $_POST['previous-accidents'];
    $dateOfAccident = $_POST['accident-date'];
    $previousSuspension = $_POST['number-suspensions'];
    $reasonForSuspension = $_POST['suspension-reason-input'];
    $guiltyOfTrafficOffense = $_POST['traffic-offenses'];
    $details = $_POST['offense-details-input'];

    // Work schedule
    $sunday = isset($_POST['off-sunday']) ? 1 : 0;
    $saturday = isset($_POST['off-saturday']) ? 1 : 0;

    // Start transaction to ensure all data is inserted correctly
    $conn->begin_transaction();

    try {
        // Insert into driver_details table
        $stmt = $conn->prepare("INSERT INTO driver_details (first_name, last_name, gender, date_of_birth, phone_number, email, post_address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $firstName, $lastName, $gender, $dob, $phoneNumber, $email, $address);
        $stmt->execute();
        $driverId = $stmt->insert_id; // Get the last inserted driver_id

        // Insert into emergency_contact table
        $stmt = $conn->prepare("INSERT INTO emergency_contact (driver_id, first_name, last_name, gender, relationship, phone_number, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $driverId, $emergencyFirstName, $emergencyLastName, $emergencyGender, $relationship, $emergencyPhoneNumber, $emergencyEmail);
        $stmt->execute();

        // Insert into driver_identification_credentials table
        $stmt = $conn->prepare("INSERT INTO driver_identification_credentials (driver_id, license_number, license_category, experience) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $driverId, $licencenumber, $licenceCategory, $yearsOfExperience);
        $stmt->execute();

        // Insert into driver_incident_history table
        $stmt = $conn->prepare("INSERT INTO driver_incident_history (driver_id, previous_accidents, accident_date, previous_suspensions, suspension_reason, guilty_of_traffic_offense, offense_details) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $driverId, $previousAccident, $dateOfAccident, $previousSuspension, $reasonForSuspension, $guiltyOfTrafficOffense, $details);
        $stmt->execute();

        // Insert into work_schedule table
        $stmt = $conn->prepare("INSERT INTO work_schedule (driver_id, off_sunday, off_saturday) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $driverId, $sunday, $saturday);
        $stmt->execute();

        // File upload handling for license
        if (isset($_FILES['license']) && $_FILES['license']['error'] == 0) {
            $fileTmpPath = $_FILES['license']['tmp_name'];
            $fileName = time() . "_" . $_FILES['license']['name'];
            $fileSize = $_FILES['license']['size'];
            $fileType = $_FILES['license']['type'];

            // Ensure valid file type (image or PDF)
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = 'uploaded_files/';
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Insert file record into the database
                    $stmt = $conn->prepare("INSERT INTO uploaded_files (driver_id, file_name, file_type, file_size, file_path) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("issis", $driverId, $fileName, $fileType, $fileSize, $destPath);
                    $stmt->execute();
                } else {
                    echo 'There was an error uploading the file.';
                }
            } else {
                echo 'Invalid file type. Only PDF and image files are allowed.';
            }
        }

        // Commit transaction if all queries succeed
        $conn->commit();

        // Set session variable to indicate form was successfully submitted
        $_SESSION['form_success'] = true;

        // Redirect to prevent form resubmission
        header("Location: driver.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if any query fails
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Driver Management</title>
    <link rel="stylesheet" href="css/driver.css" />
</head>

<body>
    <div class="progress-bar">
        <div id="step1" class="step active-step">1</div>
        <div class="step-line"></div>
        <div id="step2" class="step inactive-step">2</div>
        <div class="step-line"></div>
        <div id="step3" class="step inactive-step">3</div>
        <div class="step-line"></div>
        <div id="step4" class="step inactive-step">4</div>
        <div class="step-line"></div>
        <div id="step5" class="step inactive-step">5</div>
    </div>

    <form action="adddriver.php" method="POST" enctype="multipart/form-data" id="driver-form">

        <!-- Page 1: Personal Information -->
        <div class="form-container" id="page1">
            <h2>Personal Information</h2>
            <div class="row">
                <div class="col">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div class="col">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
            </div>
            <div class="Gender row">
                <div class="col">
                    <label>Gender</label><br>
                    <input type="radio" id="male" name="gender" value="male"> Male
                    <input type="radio" id="female" name="gender" value="female"> Female
                </div>
                <div class="col">
                    <label for="DOB">Date of Birth</label>
                    <input type="date" id="DOB" name="dob" max="2006-09-01" required>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="phone-number">Phone Number</label>
                    <input type="tel" id="phone-number" name="phone-number" pattern="0[1289]{1}[0-9]{8}" required>
                </div>
                <div class="col">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="address">Address</label><br>
                    <textarea id="address" name="address" placeholder="Enter place of residence" re></textarea>
                </div>
            </div>
            <div class="buttons">
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <!-- Page 2: Emergency Contact -->
        <div class="form-container hidden" id="page2">
            <h2>Emergency Contact</h2>

            <div class="row">
                <div class="col">
                    <label for="e-firstName">First Name</label>
                    <input type="text" id="e-firstName" name="e-firstName">
                </div>
                <div class="col">
                    <label for="e-lastName">Last Name</label>
                    <input type="text" id="e-lastName" name="e-lastName">
                </div>
            </div>
            <div class="Gender row">
                <div class="col"><label for="e-Gender">Gender:</label><br><br>
                    <input type="radio" id="e-male" name="e-Gender" value="male"> Male
                    <input type="radio" id="e-female" name="e-Gender" value="female"> Female
                </div>
                <div class="col">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="e-phone-number">Phone Number</label>
                    <input type="tel" id="e-phone-number" name="e-phone-number" pattern="0[1289]{1}[0-9]{8}">
                </div>
                <div class="col">
                    <label for="e-email">Email</label>
                    <input type="email" id="e-email" name="e-email">
                </div>
            </div><br>
            <div class="row">
                <div class="col">
                    <label for="relationship">Relationship</label>
                    <select id="relationship" name="relationship">
                        <option value="" disabled selected hidden>Specify type of relationship</option>
                        <option value="spouse">Spouse</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="child">Child</option>
                        <option value="grandparent">Grandparent</option>
                        <option value="guardian">Guardian</option>
                        <option value="friend">Friend</option>
                        <option value="neighbor">Neighbor</option>
                        <option value="colleague">Colleague</option>
                        <option value="other_relative">Other Relative</option>
                        <option value="other">Other</option>
                    </select><br>
                </div>
            </div>

            <div class="buttons">
                <button type="button" class="back-btn" onclick="previousPage()">Back</button>
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <!-- Page 3: Driver Identification & Credentials-->
        <div class="form-container hidden" id="page3">
            <h2>Driver Identification & Credentials</h2>
            <br>

            <div class="row">
                <div class="col">
                    <label for="firstName">Licence Number</label>
                    <input type="number" id=" licence-number" name="licence-number" maxlength="14" minlength="14" pattern="\d{14}" required>
                </div>

                <div class="col">
                    <label for="lastName">Licence Category</label>
                    <select id="vehicle-code" name="vehicle-code">
                        <option value="" disabled selected hidden>Specify type of licence</option>
                        <option value="small">B</option>
                        <option value="small+trailer">EB</option>
                        <option value="medium">C1</option>
                        <option value="medium+trailer">EC1</option>
                        <option value="heavy">C</option>
                        <option value="heavy+trailer">EC</option>

                    </select><br>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col">
                    <label for="Experience">Years of Experience</label>
                    <input type="number" min="0" id="licence" name="license">
                </div>
                <div class="col">
                    <label>Copy of Licence</label> <br>
                    <br><label for="fileselect" style="cursor: pointer;" id="lab"></label>
                    <input type="file" id="fileselect" name="license" accept=".pdf, image/*"><br>
                    <p><B>NOTE:</B> Please upload a <u><i>PDF</i></u> or an <u><i>image</i></u> file.</p>

                </div>
            </div>
            <div class="buttons">
                <button type="button" class="back-btn" onclick="previousPage()">Back</button>
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <!--Page 4: Driving & Incident History -->
        <div class="form-container hidden" id="page4">
            <h2>Driving & Incident History</h2>

            <div class="row">
                <div class="col">
                    <label for="previous-accidents">Previous Accidents:</label>
                    <select id="previous-accidents" name="previous-accidents" required>
                        <option value="" disabled selected hidden></option>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select><br><br>
                </div>
                <div class="col">
                    <div id="accident-details" style="display:none;">
                        <label for="accident-date">Date of Accident:</label>
                        <input type="date" id="accident-date" name="accident-date"><br><br>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="number-suspensions">Previous Suspensions:</label>
                    <select id="number-suspensions" name="number-suspensions" required>
                        <option value="" disabled selected hidden></option>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select><br><br>
                </div>

                <div class="col">
                    <div id="suspension-reason" style="display:none;">
                        <label for="suspension-reason-input">Reason for Suspension:</label>
                        <textarea id="suspension-reason-input" name="suspension-reason-input" rows="2" cols="50"></textarea><br><br>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="traffic-offenses">Guilty of Traffic Offenses?</label>
                    <select id="traffic-offenses" name="traffic-offenses" required>
                        <option value="" disabled selected hidden></option>
                        <option value="no">No</option>
                        <option value="yes">Yes</option>
                    </select><br><br>
                </div>

                <div class="cols">
                    <div id="offense-details" style="display:none;">
                        <label for="offense-details-input">Details:</label>
                        <textarea id="offense-details-input" name="offense-details-input" rows="4" cols="50"></textarea><br><br>
                    </div>
                </div>
            </div>
            <div class="buttons">
                <button type="button" class="back-btn" onclick="previousPage()">Back</button>
                <button type="button" class="next-btn" onclick="nextPage()">Next</button>
            </div>
        </div>

        <!--Page 5: Work Schedule-->
        <div class="form-container hidden" id="page5">
            <h2>Work Schedule</h2>
            <p>Select additional days and specify hours if the driver is available beyond the regular work schedule.</p>
            <br>
            <div class="row">
                <div class="col">
                    <!--<label for="days-off">Work days</label>-->
                    <br>
                    <div class="days">
                        <input type="checkbox" id="off-sunday" name="off" onclick="toggleTimeInputs('off-sunday', 'Sunday')">
                        <label for="off-sunday">Sunday</label>
                        <br>

                        <br>
                        <input type="checkbox" id="off-saturday" name="off" onclick="toggleTimeInputs('off-saturday', 'Saturday')">
                        <label for="off-saturday">Saturday</label>
                    </div>
                </div>
                <div class="col" id="time-inputs-container">
                    <!-- Time inputs will be dynamically added here -->
                </div>
            </div>
            <div class="buttons">
                <button type="button" class="back-btn" onclick="previousPage()">Back</button>
                <input type="submit" class="next-btn" value="Submit">
            </div>
        </div>
    </form>

    <script src="driver.js"></script>
</body>

</html>