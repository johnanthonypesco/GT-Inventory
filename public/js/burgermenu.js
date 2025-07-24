function sidebar() {
    // Note: I removed event.preventDefault() as it's not needed for a div/icon click
    // and can cause issues if you reuse this function on an actual link.
    const sidebar = document.querySelector('.sidebar');
    // It's better to toggle from -left-full for a full slide-in effect
    sidebar.classList.toggle('-left-full');
    sidebar.classList.toggle('left-0');
    // This ensures width is applied only when opening
    sidebar.classList.toggle('w-[280px]');
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

// ETO YUNG BINAGO PARA SA AUTO-CLOSE
window.addEventListener('resize', () => {
    // lg breakpoint in Tailwind is 1024px. If window is larger, hide mobile sidebar.
    if (window.innerWidth >= 1024) {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.remove('left-0');
        sidebar.classList.add('-left-full');
        sidebar.classList.remove('w-[280px]');
    }
});


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