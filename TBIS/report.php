<?php
session_start(); // Start session handling
if (!isset($_SESSION['id'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['id'];
include("database.php");

// Booking Report
$sql_booking_report = "SELECT b.id, u.firstname, u.lastname, c.name AS car_name, b.startingLocation, b.destination, b.num_passengers, b.Pickupdate 
                       FROM bookings b 
                       JOIN `registered-users` u ON b.user_id = u.id
                       JOIN cars c ON b.car_id = c.id";
$booking_result = $conn->query($sql_booking_report);

// Fleet Management Report
$sql_fleet_report = "SELECT id, name, type, seats, fuel, fuel_usage, fuel_consumption, Status, maintenance FROM cars";
$fleet_result = $conn->query($sql_fleet_report);

// Financial Report
$sql_financial_report = "SELECT id, user_id, car_id, destination, payment FROM bookings WHERE payment <> 0 AND processed <> 0";
$financial_result = $conn->query($sql_financial_report);

// Check if the user has clicked a report link
if (isset($_GET['generate'])) {
    $reportType = $_GET['generate'];

    switch ($reportType) {
        case 'booking_report':
            generateBookingReport($booking_result);
            break;
        case 'fleet_report':
            generateFleetReport($fleet_result);
            break;
        case 'financial_report':
            generateFinancialReport($financial_result);
            break;
        default:
            echo "Invalid report type.";
    }
}

function formatDateTime($dateTime)
{
    // Assuming the format is 'YYYY-MM-DD HH:MM:SS'
    $formattedDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
    if ($formattedDateTime) {
        // Format the date and time in 'd M Y, h:i A' format (12-hour with AM/PM)
        return $formattedDateTime->format('d M Y, h:i A'); // 12-hour format with AM/PM
    }
    return $dateTime; // Return the original date if it couldn't be formatted
}

function generateBookingReport($result)
{
    $filename = "bookings_report.html";
    $file = fopen($filename, "w");

    $htmlContent = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Bookings Report</title>
        <style>
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px 12px; border: 1px solid #ccc; }
            th { background-color: #f2f2f2; color:rgb(73, 11, 61); }
            body { background-color: rgb(217,217,217); }
        </style>
    </head>
    <body>
        <h1>Bookings Report</h1>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>User Name</th>
                <th>Car Name</th>
                <th>Starting Location</th>
                <th>Destination</th>
                <th>Number of Passengers</th>
                <th>Pickup Date and Time</th>
            </tr>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Format the date and time
            $formattedDateTime = formatDateTime($row['Pickupdate']);

            $htmlContent .= "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['firstname']} {$row['lastname']}</td>
                <td>{$row['car_name']}</td>
                <td>{$row['startingLocation']}</td>
                <td>{$row['destination']}</td>
                <td>{$row['num_passengers']}</td>
                <td>{$formattedDateTime}</td>
            </tr>";
        }
    } else {
        $htmlContent .= "<tr><td colspan='7'>No data available</td></tr>";
    }

    $htmlContent .= "</table></body></html>";
    fwrite($file, $htmlContent);
    fclose($file);

    // Force the file to be downloaded
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filename);
    exit();
}

function generateFleetReport($result)
{
    $filename = "fleet_report.html";
    $file = fopen($filename, "w");

    $htmlContent = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Fleet Report</title>
        <style>
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px 12px; border: 1px solid #ccc; }
            th { background-color: #f2f2f2; color:rgb(73, 11, 61); }
            body { background-color: rgb(217,217,217); }
        </style>
    </head>
    <body>
        <h1>Fleet Report</h1>
        <table>
            <tr>
                <th>Car ID</th>
                <th>Car Name</th>
                <th>Car Type</th>
                <th>Car Capacity</th>
                <th>Fuel</th>
                <th>Fuel Usage</th>
                <th>Fuel Consumption</th>
                <th>Status</th>
                <th>Maintenance</th>
            </tr>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $htmlContent .= "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['type']}</td>
                <td>{$row['seats']}</td>
                <td>{$row['fuel']}</td>
                <td>{$row['fuel_usage']}</td>
                <td>{$row['fuel_consumption']}</td>
                <td>{$row['Status']}</td>
                <td>{$row['maintenance']}</td>
            </tr>";
        }
    } else {
        $htmlContent .= "<tr><td colspan='9'>No data available</td></tr>";
    }

    $htmlContent .= "</table></body></html>";
    fwrite($file, $htmlContent);
    fclose($file);

    // Force the file to be downloaded
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filename);
    exit();
}

function generateFinancialReport($result)
{
    $filename = "financial_report.html";
    $file = fopen($filename, "w");

    $htmlContent = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Financial Report</title>
        <style>
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 8px 12px; border: 1px solid #ccc; }
            th { background-color: #f2f2f2; color:rgb(73, 11, 61); }
            body { background-color: rgb(217,217,217); }
        </style>
    </head>
    <body>
        <h1>Financial Report</h1>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Car ID</th>
                <th>User ID</th>
                <th>Destination</th>
                <th>Payment</th>
            </tr>";

    $total_payment = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $htmlContent .= "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['car_id']}</td>
                <td>{$row['user_id']}</td>
                <td>{$row['destination']}</td>
                <td>{$row['payment']}</td>
            </tr>";

            $total_payment += $row['payment'];
        }
    } else {
        $htmlContent .= "<tr><td colspan='5'>No data available</td></tr>";
    }

    $htmlContent .= "
    <tr>
        <td colspan='4' style='text-align:right; font-weight:bold;'>Total Payment:</td>
        <td><b>{$total_payment}</b></td>
    </tr>
    </table>
    </body>
    </html>";

    fwrite($file, $htmlContent);
    fclose($file);

    // Force the file to be downloaded
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    readfile($filename);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/fleet.css">
    <title>Dashboard</title>
    <style>
        /* Custom Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            color: #2f3b52;
            margin: 0;
        }

        .header p {
            font-size: 18px;
            color: #555;
        }

        .report-card {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 8px;
        }

        .report-card h2 {
            font-size: 26px;
            margin-bottom: 15px;
            color: #2f3b52;
        }

        .report-card p {
            font-size: 18px;
            color: #555;
        }

        .report-card a {
            display: inline-block;
            margin-top: 15px;
            color: #fff;
            background-color: #3e8e41;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
        }

        .report-card a:hover {
            background-color: #2e7033;
        }
    </style>
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
            <div class="header">
                <h1>Dashboard</h1>
                <p>Welcome to the report dashboard. Here you can download various reports and manage the system.</p>
            </div>

            <!-- Report Links -->
            <div class="report-card">
                <h2>The Booking Report</h2>
                <p>Click the link below to download the bookings report. The report contains details of bookings made by the users. <br><b>The file is in HTML format.</b></p>
                <p><a href="?generate=booking_report">Click here to download the Booking Report</a></p>
            </div>

            <div class="report-card">
                <h2>The Fleet Management Report</h2>
                <p>The link below will provide you with the fleet management report. This report contains car details, fuel usage, and maintenance information. <br><b>The report is in HTML format.</b></p>
                <p><a href="?generate=fleet_report">Click here to download the Fleet Report</a></p>
            </div>

            <div class="report-card">
                <h2>The Financial Report</h2>
                <p>The link below will provide you with the financial report. This report contains payment details for external bookings made by users. <br><b>The report is in HTML format.</b></p>
                <p><a href="?generate=financial_report">Click here to download the Financial Report</a></p>
            </div>
        </div>
    </div>
</body>

</html>