function sidebar() {
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.toggle('left-0')
    sidebar.classList.toggle('w-[300px]')
}

window.onclick = function(event) {
    if (event.target.matches('.sidebar')) {
        sidebar()
    }
}

var currentLocation = window.location.href;
var navLinks = document.querySelectorAll('.sidebar a');
var navicons = document.querySelectorAll('.sidebar a i');
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

window.addEventListener('resize', () => {
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[300px]')
})
