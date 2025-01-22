<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="user.php">Users</a></li>
        <li><a href="driver.php">Driver Management</a></li>
        <li class="dropdown">
            <a href="fleet_management.php">Fleet Management</a>
            <ul class="dropdown-menu">
                <li><a href="fleet_management.php">Fleet Overview</a></li>
                <li><a href="fleet_management.php#fuel-management-section" onclick="toggleFuelManagement()">Fuel Management</a></li>
                <li><a href="fleet_management.php#maintenance-section" onclick="toggleMaintenance()">Maintenance Records</a></li>
            </ul>
        </li>
        <li><a href="report.php">Reports</a></li>
        <li><a href="logout.php" onclick="return confirmLogout();">Log out</a></li>
    </ul>
</body>

</html>