// Add an event listener to fetch locations once the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function() {
    // Immediately show the staff popup before loading other content
    showStaffPopup();
});

// Function to show the staff popup
function showStaffPopup() {
    // Automatically set userChoice to false if the user role is guest
    const userChoice =
        userRole === "Guest" ?
        false :
        confirm(
            "Choose booking type:\nOK for Internal Booking, Cancel for External Booking"
        );

    if (!userChoice) {
        setExternalBooking();
    }

    // After the user responds or automatically selects, proceed to fetch locations
    fetchLocations();
}

// Function to fetch the pickup and dropoff locations
function fetchLocations() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "fetch_pickup_location.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                try {
                    const locations = JSON.parse(xhr.responseText);
                    if (locations) {
                        document.getElementById("pickup-location").value =
                            locations.origin || "";
                        const dropoffSelect = document.getElementById("dropoff-location");
                        dropoffSelect.innerHTML = "";
                        dropoffSelect.appendChild(
                            new Option("Select a Dropoff Location", "")
                        );
                        locations.destinations.forEach((destination) => {
                            dropoffSelect.appendChild(new Option(destination, destination));
                        });
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                }
            } else {
                console.error("Error fetching locations, status: ", xhr.status);
            }
        }
    };
    xhr.send();
}

// Function to clear the form fields
function clearForm() {
    document.getElementById("pickup-date").value = "";
    document.getElementById("dropoff-location").value = ""; // Reset dropdown
    document.getElementById("dropoff-date").value = "";
    document.getElementById("num-passengers").value = "";
}

let selectedCarId = null; // Variable to store the selected car ID

function selectCar(carId, event) {
    selectedCarId = carId; // Store the selected car ID
    console.log("Selected Car ID: " + selectedCarId); // Debugging: log the selected car ID

    // Optionally, you can visually highlight the selected car card
    const carCards = document.querySelectorAll(".car-card");
    carCards.forEach((card) => card.classList.remove("selected")); // Remove 'selected' class from all cards
    event.currentTarget.classList.add("selected"); // Add 'selected' class to the clicked card
}

// Track the current page
let currentStep = 1;
const totalPages = 3; // Total number of pages

function nextPage() {
    const currentPage = document.getElementById("page" + currentStep);
    const nextPage = document.getElementById("page" + (currentStep + 1));

    if (currentStep === 1) {
        // Validate inputs for the first page
        if (validatePage(currentStep)) {
            currentPage.classList.add("hidden");
            nextPage.classList.remove("hidden");
            document
                .getElementById("step" + currentStep)
                .classList.remove("active-step");
            document
                .getElementById("step" + (currentStep + 1))
                .classList.add("active-step");
            currentStep++;

            // Call fetchAvailableCars only when moving to page 2
            if (currentStep === 2) {
                fetchAvailableCars();
            }

            // Update progress bar
            updateProgressBar(currentStep);
        } else if (document.getElementById("error-message-" + currentStep)) {
            document.getElementById("error-message-" + currentStep).style.display =
                "block";
        }
    } else if (currentStep === 2) {
        // Check if a car is selected before proceeding to the next page
        if (!selectedCarId) {
            alert("Please select a car before proceeding.");
        } else {
            currentPage.classList.add("hidden");
            nextPage.classList.remove("hidden");
            document
                .getElementById("step" + currentStep)
                .classList.remove("active-step");
            document
                .getElementById("step" + (currentStep + 1))
                .classList.add("active-step");
            currentStep++;

            // Update progress bar
            updateProgressBar(currentStep);
        }
    }

    // Proceed only if a car is selected for subsequent pages
    if (selectedCarId) {
        document.getElementById("selected-car-id").value = selectedCarId; // Store selectedCarId in a hidden input
        console.log("Proceeding to the next page with Car ID: " + selectedCarId);
    }
}

// Function to validate the form on the current page
function validatePage(step) {
    let valid = true;
    if (step === 1) {
        const fields = [
            "pickup-location",
            "dropoff-location",
            "pickup-date",
            "dropoff-date",
            "num-passengers",
        ];
        fields.forEach((id) => {
            const element = document.getElementById(id);
            if (!element.value) {
                valid = false;
            }
        });
    }
    return valid;
}

function updateProgressBar(currentStep) {
    for (let i = 1; i <= totalPages; i++) {
        const step = document.getElementById(`step${i}`);
        step.classList.remove("active-step", "inactive-step");
        step.classList.add(i <= currentStep ? "active-step" : "inactive-step");
    }
}

function setExternalBooking() {
    document.querySelector("h2 span").textContent = "External";
    document.getElementById("booking-form").action = "calculations.php";
}

function fetchAvailableCars() {
    const numPassengers = document.getElementById("num-passengers").value;
    console.log("Number of Passengers: " + numPassengers); // Debugging: log the number of passengers

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "fetch_cars.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            document.getElementById("available-cars").innerHTML = xhr.responseText;
        }
    };
    xhr.send("num_passengers=" + encodeURIComponent(numPassengers)); // Use encodeURIComponent for safety
}