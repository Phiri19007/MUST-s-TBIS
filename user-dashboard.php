<?php
session_start();
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
} else {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #d3d3d3;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            position: relative;
            z-index: 2;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .container {
            display: flex;
            height: calc(100vh - 50px);
        }

        .sidebar {
            width: 160px;
            background-color: #d3d3d3;
            color: white;
            padding: 15px;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }

        .sidebar h2 {
            color: #490b3d;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 20px 0;
        }

        .sidebar ul li a {
            text-decoration: none;
            color: #490b3d;
            display: block;
            padding: 10px;
        }

        .sidebar ul li a:hover {
            background-color: white;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            overflow-y: auto;
        }

        .card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px 0;
            background-color: white;
        }

        .profile-photo {
            margin: 20px 0;
            display: flex;
            align-items: center;
        }

        .profile-photo img {
            width: 200px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .hidden {
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .delete-button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <div class="title">
                <h3>MUST's TBIS</h3>
            </div>
            <div class="lists">
                <div class="dropdown">
                    <img src="image/menu_icon.png" alt="menu" aria-haspopup="true">
                    <div class="dropdown-content">
                        <a href="user-dashboard.php">Dashboard</a>
                        <a href="logout.php" onclick="return confirmLogout();">Log Out</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="sidebar">
            <h2>Side Panel</h2>
            <ul>
                <li><a href="user-dashboard.php">Dashboard</a></li>
                <li><a href="#" id="viewBookings">View Bookings</a></li>
                <li><a href="Booking_page.php" id="makeBooking">Make Booking</a></li>
                <li><a href="logout.php" onclick="return confirmLogout();">Log Out</a></li>
            </ul>
        </div>

        <div class="main-content">
            <h1>Dashboard</h1>
            <div class="profile-photo">
                <img src="path/to/default_photo.jpg" alt="User Profile Photo" id="profilePhoto">
                <div>
                    <h3 id="userName">User Name</h3>
                    <span id="userRole">User Role</span>
                </div>
            </div>
            <p>Welcome to the <span id="userRoleText">User Role</span> dashboard! Manage your bookings efficiently.</p>
            <div class="card" id="bookingCard">
                <h3>Total Bookings</h3>
                <p id="totalBookings">0</p>
            </div>
            <div id="bookingTable" class="hidden">
                <h3>Booking Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Starting Location</th>
                            <th>Destination</th>
                            <th>Number of Passengers</th>
                            <th>Pickup Date</th>
                            <th>Dropoff Date</th>
                            <th>Total cost</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bookingData"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let bookings = [];
        let editIndex = -1;

        async function init() {
            try {
                await fetchUserData();
                await fetchTotalBookings();
            } catch (error) {
                console.error('Initialization error:', error);
                alert('Failed to load data. Please refresh the page.');
            }
        }

        async function fetchTotalBookings() {
            try {
                const response = await fetch('get_total_bookings.php');
                const data = await response.json();
                const totalBookings = data.total || 0;
                document.getElementById("totalBookings").textContent = totalBookings;
                return totalBookings;
            } catch (error) {
                console.error('Error fetching total bookings:', error);
                alert('Error fetching total bookings. Please try again later.');
                return 0;
            }
        }

        async function fetchUserData() {
            try {
                const response = await fetch('fetch_user.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                document.getElementById("profilePhoto").src = data.profile_photo || 'path/to/default_photo.jpg';
                document.getElementById("userName").textContent = data.username || 'Guest User';
                document.getElementById("userRole").textContent = data.role || 'Guest';
                document.getElementById("userRoleText").textContent = data.role || 'Guest';
            } catch (error) {
                console.error('Error fetching user data:', error);
                alert('Error fetching user data. Please try again later.');
            }
        }

        document.getElementById("viewBookings").addEventListener("click", async function(event) {
            event.preventDefault();
            const bookingCard = document.getElementById("bookingCard");
            const bookingTable = document.getElementById("bookingTable");
            bookingTable.classList.toggle("hidden");
            bookingCard.classList.toggle("hidden");

            if (!bookingTable.classList.contains("hidden")) {
                await populateTable();
            }
        });
        async function populateTable() {
            try {
                const response = await fetch('fetch_bookings.php');
                bookings = await response.json();
                const bookingData = document.getElementById("bookingData");
                bookingData.innerHTML = '';
                bookings.forEach((booking, index) => {
                    const formattedPayment = new Intl.NumberFormat('en-MW', {
                        style: 'currency',
                        currency: 'MWK'
                    }).format(booking.payment); // Format the payment amount

                    const row = `<tr>
                <td>${booking.startingLocation}</td>
                <td>${booking.destination}</td>
                <td>${booking.num_passengers}</td>
                <td>${new Date(booking.Pickupdate).toLocaleString()}</td>
                <td>${new Date(booking.dropoffdate).toLocaleString()}</td>
                <td>${formattedPayment}</td> <!-- Use formatted payment here -->
                <td>${booking.status}</td>
                <td>
                    <button class="delete-button" data-index="${index}">Delete</button>
                </td>
            </tr>`;
                    bookingData.innerHTML += row;
                });

                document.querySelectorAll('.delete-button').forEach(button => {
                    button.addEventListener('click', deleteBooking);
                });
            } catch (error) {
                console.error('Error fetching bookings:', error);
                alert('Error fetching bookings. Please try again later.');
            }
        }


        function deleteBooking(event) {
            const index = parseInt(event.target.getAttribute('data-index'));
            const bookingRow = event.target.closest("tr");
            const statusCell = bookingRow.querySelector("td:nth-child(7)"); // Adjust if status column is in a different position
            const bookingStatus = statusCell.textContent.trim();

            // Check if the status is "confirmed"
            if (bookingStatus.toLowerCase() === "confirmed") {
                alert("This booking is confirmed and cannot be deleted.");
                return; // Exit the function to prevent deletion
            }

            const bookingId = bookings[index].id;
            const confirmDelete = confirm("Are you sure you want to delete this booking?");

            if (confirmDelete) {
                fetch('delete_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'id=' + bookingId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            bookings.splice(index, 1);
                            populateTable();
                            fetchTotalBookings();
                        } else {
                            alert('Error deleting booking. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting booking:', error);
                        alert('Error deleting booking. Please try again later.');
                    });
            }
        }


        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }

        document.getElementById("makeBooking").addEventListener("click", async function(event) {
            event.preventDefault();
            const totalBookings = await fetchTotalBookings();

            if (totalBookings >= 3) {
                alert("You can only book up to 3 trips.");
            } else {
                window.location.href = "booking_page.php";
            }
        });

        init();
    </script>
</body>

</html>