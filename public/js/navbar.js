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
        icon.classList.add('text-black/80');
    }
});


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
                    const newContactsList = doc.getElementById('navBarCounter');
                    if (newContactsList) {
                        const currentContactsList = document.getElementById('navBarCounter');
                        currentContactsList.innerHTML = newContactsList.innerHTML;
                    }
                })
                .catch(error => console.error('Error refreshing contacts:', error));
        }
    
        // Start the refresh interval when the page loads
        startContactsRefresh();
    });