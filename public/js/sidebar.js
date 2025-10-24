document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const desktopCollapseBtn = document.getElementById('desktop-collapse-btn');
    const contentWrapper = document.getElementById('content-wrapper');
    const header = document.querySelector('header');
    const navLinks = document.querySelectorAll('.nav-text');
    const navIcons = document.querySelectorAll('.nav-icon');
    const navLinkElements = document.querySelectorAll('.nav-link');
    const currentUrl = window.location.pathname;

    const openSidebar = () => {
        sidebar.classList.remove('translate-x-[-100%]');
        overlay.classList.remove('hidden');
    };
    const closeSidebar = () => {
        sidebar.classList.add('translate-x-[-100%]');
        overlay.classList.add('hidden');
    };

    mobileMenuBtn?.addEventListener('click', () => {
        if (sidebar.classList.contains('translate-x-[-100%]')) {
            openSidebar();
        } else {
            closeSidebar();
        }
    });

    overlay?.addEventListener('click', closeSidebar);

    desktopCollapseBtn?.addEventListener('click', () => {
        const isCollapsed = sidebar.classList.toggle('lg:w-20');
        sidebar.classList.toggle('lg:w-64', !isCollapsed);
        contentWrapper?.classList.toggle('lg:ml-20', isCollapsed);
        contentWrapper?.classList.toggle('lg:ml-64', !isCollapsed);
        header?.classList.toggle('lg:left-20', isCollapsed);
        header?.classList.toggle('lg:left-64', !isCollapsed);
        navLinks.forEach(link => link.classList.toggle('lg:hidden', isCollapsed));
        navIcons.forEach(icon => icon.classList.toggle('lg:mx-auto', isCollapsed));
        const icon = desktopCollapseBtn.querySelector('i');
        icon.classList.toggle('fa-chevron-left', !isCollapsed);
        icon.classList.toggle('fa-chevron-right', isCollapsed);
    });

    navLinkElements.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (!linkHref || linkHref === '#') return;

        let linkPath = linkHref;
        try {
            linkPath = new URL(linkHref, window.location.origin).pathname;
        } catch (e) {}

        const isActive = linkPath === currentUrl || (currentUrl.startsWith(linkPath) && linkPath !== '/');

        if (isActive) {
            link.classList.add('bg-red-50', 'text-red-600');
            link.classList.remove('hover:bg-gray-50');
            link.querySelector('i')?.classList.add('text-red-600');
            link.querySelector('span')?.classList.add('text-red-600');
        } else {
            link.classList.remove('bg-red-50', 'text-red-600');
            link.classList.add('hover:bg-gray-50');
            link.querySelector('i')?.classList.remove('text-red-600');
            link.querySelector('span')?.classList.remove('text-red-600');
            link.querySelector('span')?.classList.add('text-gray-700');
        }
    });
});
