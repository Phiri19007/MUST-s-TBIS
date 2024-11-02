<?php
include("database.php");

if (isset($_POST["submit"])) {
    $image = $_FILES["carimage"];
    $car_Name = $_POST["car_Name"];
    $type = $_POST["type"];
    $license = $_POST["license"];
    $seat = $_POST["seat"];
    $fuel = $_POST["fuel"];
    $price = 60000;
    $statu = "active";

    $imageName = $image["name"];
    $imageTname = $image["tmp_name"];
    $imageSize = $image["size"];
    $imageError = $image["error"];

    if ($imageError === 0) {
        if ($imageSize > 8000000) {
            $em = "Your file is too large!";
            header("Location: addcar.php?error=$em");
            exit();
        } else {
            $img_ex = pathinfo($imageName, PATHINFO_EXTENSION);
            $img_ex_lc = strtolower($img_ex);
            $allowed = array("jpg", "jpeg", "png");
            if (in_array($img_ex_lc, $allowed)) {
                $new_image_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                $image_path_upload = 'uploaded_image/' . $new_image_name;

                if (move_uploaded_file($imageTname, $image_path_upload)) {
                    // Prepared statement to prevent SQL injection
                    $stmt = $conn->prepare("INSERT INTO cars (image, name, type, license, seats, fuel, price, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssssis", $new_image_name, $car_Name, $type, $license, $seat, $fuel, $price, $statu);

                    if ($stmt->execute()) {
                        header("Location: car.php");
                        exit();
                    } else {
                        $em = "Database error: " . $stmt->error;
                        header("Location: addcar.php?error=$em");
                        exit();
                    }
                } else {
                    $em = "Failed to upload image.";
                    header("Location: addcar.php?error=$em");
                    exit();
                }
            } else {
                $em = "You cannot upload this type of file";
                header("Location: addcar.php?error=$em");
                exit();
            }
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Car Info</title>
    <link rel="stylesheet" href="Forms.css">
    <script>
        function populate(type, fuel) {
            var s1 = document.getElementById(type);
            var s4 = document.getElementById(fuel);


            s4.innerHTML = "";
            var optionArray3 = ["", "petrol|Petrol", "diesel|Diesel", "electricity|Electricity", "hybrid|Hybrid"];
            for (var i = 0; i < optionArray3.length; i++) {
                var pair = optionArray3[i].split("|");
                var newOption = document.createElement("option");
                newOption.value = pair[0];
                newOption.innerHTML = pair[1];
                s4.appendChild(newOption);
            }
        }
    </script>
</head>

<body>
    <div class="menu-container">
        <div class="hamburger" id="hamburger">
            <div class="menu-bar"></div>
            <div class="menu-bar"></div>
            <div class="menu-bar"></div>
        </div>
        <nav class="nav" id="nav">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="">Log out</a></li>
            </ul>
        </nav>
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="content">
            <div class="step-content active" id="content1">
                <p style="text-align: center;">Note: allowed file for uploading: .jpg, .jpeg, .png and size must be less than 8 MB</p><br>
                <h2>Car Information</h2>
                <div class="row">
                    <div class="col">
                        <label for="car-Name">Make Model</label>
                        <input type="text" id="car-Name" name="car_Name" required placeholder="make-model">
                    </div>
                    <div class="col">
                        <label for="type">Car Classification</label>
                        <select id="type" name="type" required onchange="populate('type', 'fuel')">
                            <option value="">--select class of car---</option>
                            <option value="bus">BUS</option>
                            <option value="sedan">Sedan</option>
                            <option value="pickup">Pickup</option>
                            <option value="minbus">Mini-bus</option>
                            <option value="suv">SUV</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="license">License Class</label>
                        <select id="license" name="license" required>
                            <option value="">--select Licence Class---</option>
                            <option value="Class-B">Class B</option>
                            <option value="Class-C">Class C</option>
                            <option value="Class-E">Class E</option>
                        </select>
                    </div>
                    <div class="col">
                        <label for="seat">Total Seats</label>
                        <input type="number" min="3" name="seat" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="fuel">Fuel Type</label>
                        <select id="fuel" name="fuel" required></select>
                    </div>
                    <div class="col">
                        <label for="carimage">Image</label><br>
                        <input id="file" type="file" name="carimage" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <button type="reset" class="buttons">Clear</button>
                <button name="submit" type="submit" class="buttons" style="margin-left:60%">Submit</button>
            </div>
        </div>
    </form>
    <script src="scriptf.js"></script>
</body>

</html>