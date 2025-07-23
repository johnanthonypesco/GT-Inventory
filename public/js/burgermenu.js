function sidebar() {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.toggle('left-0')
    sidebar.classList.toggle('w-[280px]')
}

function closeSidebar() {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[280px]')
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

window.addEventListener('scroll', () => {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[280px]')
})
window.addEventListener('resize', () => {
    event.preventDefault();
    const sidebar = document.querySelector('.sidebar')
    sidebar.classList.remove('left-0')
    sidebar.classList.remove('w-[280px]')
})

// add auto reload for realtime
document.addEventListener('DOMContentLoaded', function() {
    let contactsRefreshInterval;

    // Start contacts refresh interval
    function startContactsRefresh() {
        contactsRefreshInterval = setInterval(refreshContacts, 7000); // Refresh every 6 seconds
    }

    // Stop contacts refresh interval
    function stopContactsRefresh() {
        clearInterval(contactsRefreshInterval);
    }

    // Function to refresh contacts list
    function refreshContacts() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContactsList = doc.getElementById('headerCounter');
                if (newContactsList) {
                    const currentContactsList = document.getElementById('headerCounter');
                    currentContactsList.innerHTML = newContactsList.innerHTML;
                }
            })
            .catch(error => console.error('Error refreshing contacts:', error));
    }

    // Start the refresh interval when the page loads
    startContactsRefresh();
});
