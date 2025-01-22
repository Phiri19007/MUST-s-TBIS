<?php
include("database.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MUST's TBIS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="index.css">
    <script>
        // Show the button when scrolling down
        window.onscroll = function() {
            const button = document.getElementById("scrollToTopBtn");
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                button.style.display = "block";
            } else {
                button.style.display = "none";
            }
        };

        // Scroll to the top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            }); // Smooth scroll to top
        }
    </script>
</head>

<body>
    <main>
        <div class="hero-section-car">
            <div>
                <header>
                    <nav>
                        <div class="title">
                            <h3><a href="index.php" title="Home">MUST's TBIS</a> </h3>
                        </div>
                        <div class="lists">
                            <div class="dropdown">
                                <img src="image/menu_icon.png" alt="menu">
                                <div class="dropdown-content">
                                    <a href="index.php">Home</a>
                                    <a href="car.php" class="active">Our car</a>
                                    <a href="login.php">Sign in</a>
                                </div>
                            </div>

                        </div>
                    </nav>
                </header>
            </div>

        </div>
        <button id="scrollToTopBtn" onclick="scrollToTop()" title="Go to Top"><i class="fas fa-arrow-up"></i></button>

        <div class="container">
            <div class="cars">
                <h1 class="heading">Latest Deals</h1>
                <div class="box-container">
                    <?php
                    $select_cars = mysqli_query($conn, "SELECT name, type, seats, price, image FROM `cars` WHERE Status !='deleted'");
                    if (mysqli_num_rows($select_cars) > 0) {
                        while ($fetch = mysqli_fetch_assoc($select_cars)) {
                    ?>
                            <div class="car-box">
                                <img src="uploaded_image/<?php echo $fetch['image']; ?>" alt="Car Image" class="car-image" style="width:100%" />
                                <div class="car-content">
                                    <h3 class="name"><?php echo $fetch['name']; ?></h3>
                                    <p>Type: <?php echo $fetch['type']; ?> &nbsp;&nbsp; Total Seats: <?php echo $fetch['seats']; ?></p>
                                    <div class="price">The price per day: MK <?php echo number_format($fetch['price'], 2); ?></div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p>No cars available.</p>";
                    }
                    ?>
                </div>

            </div>
        </div>
    </main>
    <footer>
        <div class="footer-details">
            <div class="footer-title">
                <h3><a href="index.php" title="Home">MUST's TBIS</a> </h3>
            </div>
            <div class="footer-quicks">
                <h4>Quick Links</h4>
                <a href="index.php">Home</a><br>
                <a href="index.php#about">About us</a><br>
                <a href="car.php">Our Cars</a><br>
                <a href="login.php">Sign in</a><br>
            </div>

        </div>
        <hr>
        <h5>&copy 2024 MUST's TBIS</h5>
    </footer>

</body>

</html>