// public/js/admin-layout.js

const adminLayout = {
    toggleMobileSidebar(event) {
        if (event) event.preventDefault();
        const sidebar = document.querySelector('.mobile-sidebar');
        if (sidebar) {
            sidebar.classList.toggle('-left-[300px]');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('w-0');
            sidebar.classList.toggle('w-[300px]');
        }
    },

    setActiveLinks() {
        const currentLocation = window.location.href;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            // Reset states
            link.classList.remove('active');
            const icon = link.querySelector('i');
            if (icon) {
                icon.classList.remove('text-white');
            }

            // Set active state
            if (link.href === currentLocation) {
                link.classList.add('active');
                if (icon) {
                    icon.classList.add('text-white');
                }
            }
        });
    },

    refreshChatCounters() {
        fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newChatLinks = doc.querySelectorAll('.chat-nav-link');
                const currentChatLinks = document.querySelectorAll('.chat-nav-link');

                if (newChatLinks.length === 0 || currentChatLinks.length === 0) return;

                const newBadge = newChatLinks[0].querySelector('.chat-notification-badge');

                currentChatLinks.forEach(link => {
                    let currentBadge = link.querySelector('.chat-notification-badge');
                    if (newBadge) {
                        if (currentBadge) {
                            currentBadge.textContent = newBadge.textContent;
                        } else {
                            link.insertAdjacentHTML('beforeend', newBadge.outerHTML);
                        }
                    } else if (currentBadge) {
                        currentBadge.remove();
                    }
                });
            })
            .catch(error => console.error('Error refreshing chat counters:', error));
    },

    init() {
        this.setActiveLinks();
        setInterval(() => this.refreshChatCounters(), 7000);

        document.addEventListener('click', (event) => {
            const sidebar = document.querySelector('.mobile-sidebar');
            const toggleButton = event.target.closest('i.fa-bars');
            if (sidebar && sidebar.classList.contains('left-0') && !sidebar.contains(event.target) && !toggleButton) {
                this.toggleMobileSidebar();
            }
        });

        window.addEventListener('resize', () => {
            const sidebar = document.querySelector('.mobile-sidebar');
            if (sidebar && sidebar.classList.contains('left-0')) {
                this.toggleMobileSidebar();
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    adminLayout.init();
});