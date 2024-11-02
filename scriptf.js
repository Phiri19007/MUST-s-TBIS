// Select the hamburger icon and navigation menu
const hamburger = document.getElementById("hamburger");
const nav = document.getElementById("nav");

// Add click event listener to the hamburger icon
hamburger.addEventListener("click", () => {
    // Toggle the 'show' class on the navigation menu
    nav.classList.toggle("show");
});