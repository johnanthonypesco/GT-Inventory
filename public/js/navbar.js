var currentLocation = window.location.href;
var navLinks = document.querySelectorAll('.list-none a');
var navicons = document.querySelectorAll('.list-none a i');
navLinks.forEach(function(link) {
    if (link.href === currentLocation) {
        link.classList.add('active');
    }
});
navicons.forEach(function(icon) {
    if (icon.parentElement.href === currentLocation) {
        icon.classList.add('text-white');
    }
});