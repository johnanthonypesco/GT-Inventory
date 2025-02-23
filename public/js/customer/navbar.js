var currentLocation = window.location.href;
var navLinks = document.querySelectorAll("nav a");
navLinks.forEach(function (link) {
    if (link.href === currentLocation) {
        link.classList.add("active");
    }
});