function sidebar() {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay'); // Get the overlay

    // Toggle sidebar visibility
    sidebar.classList.toggle('left-0');
    sidebar.classList.toggle('w-[280px]');
    
    // Toggle overlay visibility
    overlay.classList.toggle('hidden'); 
}

function closeSidebar() {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay'); // Get the overlay

    // Hide the sidebar
    sidebar.classList.remove('left-0');
    sidebar.classList.remove('w-[280px]');

    // Hide the overlay
    overlay.classList.add('hidden'); 
}

// Close sidebar if user clicks outside of it (on the semi-transparent background)
window.onclick = function(event) {
    if (event.target.matches('.sidebar')) {
        sidebar();
    }
}

// Highlight the active link in the sidebar
var currentLocation = window.location.href;
var navLinks = document.querySelectorAll('.sidebar a');
navLinks.forEach(function(link) {
    if (link.href === currentLocation) {
        link.classList.add('active');
        // also add the white text to the icon inside the active link
        const icon = link.querySelector('i');
        if (icon) {
            icon.classList.add('text-white');
        }
    }
});

window.addEventListener('scroll', () => {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[280px]')
        closeSidebar();

})
window.addEventListener('resize', () => {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[280px]')
        closeSidebar();

})

// add auto reload for realtime
document.addEventListener('DOMContentLoaded', function() {
    let contactsRefreshInterval;

    function startContactsRefresh() {
        contactsRefreshInterval = setInterval(refreshContacts, 7000);
    }

    function stopContactsRefresh() {
        clearInterval(contactsRefreshInterval);
    }

    function refreshContacts() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContactsList = doc.getElementById('headerCounter');
                if (newContactsList) {
                    const currentContactsList = document.getElementById('headerCounter');
                    if (currentContactsList) {
                       currentContactsList.innerHTML = newContactsList.innerHTML;
                    }
                }
            })
            .catch(error => console.error('Error refreshing contacts:', error));
    }

    startContactsRefresh();
});