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
        <div class="hero-section">
            <div>
                <header>
                    <nav>
                        <div class="title">
                            <h3><a href="index.php" title="Home">MUST's TBIS</a> </h3>
                        </div>
                        <div class="lists">
                            <a href="login.php"><button>BOOK NOW</button></a>
                            <div class="dropdown">
                                <img src="image/menu_icon.png" alt="menu">
                                <div class="dropdown-content">
                                    <a href="index.php" class="active">Home</a>
                                    <a href="car.php">Our car</a>
                                    <a href="#about">About us</a>
                                    <a href="login.php">Sign in</a>
                                </div>
                            </div>

                        </div>
                    </nav>
                </header>
            </div>
            <p class="glowing-text">MUST Transportation Booking Information System<br>

            </p>

        </div>
        <button id="scrollToTopBtn" onclick="scrollToTop()" title="Go to Top"><i class="fas fa-arrow-up"></i></button>
        <div class="Booking-button" id="booking">
            <div class="booking">
                <div class="box">
                    <p class="infobooking">
                        Book for organization use
                    </p>
                    <a href="login.php"><button>Book now</button></a>
                </div>

            </div>
            <div class="booking">
                <div class="box">
                    <p class="infobooking">
                        Book for external activities
                    </p>
                    <a href="login.php"><button>Book now</button></a>
                </div>

            </div>

        </div>
        <hr>
        <div class="Booking-button">
            <div class="image-container" title="image of students on trip which used one of our bus">

            </div>
            <div class="about-us" id="about">
                <div class="about-us-info">
                    <h4>About</h4>
                    <P>The Transportation Booking Information System is a platform designed to facilitate easy booking and scheduling of transportation services. Whether you're a student or staff member or guest, our system ensures seamless transportation arrangements
                        to your desired locations.
                    </P>
                </div>
            </div>
        </div>
        <div>
            <h2 class="title-sandp">Services and Prices</h2>
        </div>
        <div class="serive-price">

            <div class="service">
                <div>
                    <p>
                    <h4 class="heading-service-price-box">What we offer</h4>
                    <ul>
                        <li>Transportation for MUST students and staff</li>
                        <li> External hiring for non-MUST entities</li>
                        <li>Multi-day trip arrangements</li>
                    </ul>

                    </p>
                </div>
            </div>
            <div class="prices">
                <p>
                <h4 class="heading-service-price-box">Prices</h4>
                <ul>
                    <li>Internal Trips (MUST students and staff): Free of charge</li>
                    <li>External Hiring (Non-MUST entities): K1,000 per kilometer</li>
                    <li>Multi-day Trips (more than one day): K60,000 per day</li>
                    <li>20% discount for staff members booking for non-MUST activities</li>
                </ul>

                </p>

            </div>

        </div>
        <div>
            <h2>Location</h2>
        </div>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3837.1351838865526!2d35.21427157388799!3d-15.901987184753496!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x18d9cad291cec51f%3A0xc98b4ac16beed81b!2sMalawi%20University%20of%20Science%20and%20Technology!5e0!3m2!1sen!2smw!4v1728422242953!5m2!1sen!2smw"
                width="80%" height="300" style="border:0;  border-radius: 5px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
                <a href="#about">About us</a><br>
                <a href="car.php">Our Cars</a><br>
                <a href="login.php">Sign in</a><br>
            </div>

        </div>
        <hr>
        <h5>&copy 2024 MUST's TBIS</h5>
    </footer>

</body>

</html>