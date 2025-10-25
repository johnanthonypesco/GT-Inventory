document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Kunin ang mga importanteng elements
    // Ang content area na papalitan ng laman
    const mainContent = document.getElementById('main-content');
    
    // Lahat ng links sa sidebar na may class="nav-link"
    const navLinks = document.querySelectorAll('.nav-link');

    // ---
    // 2. Function para mag-load ng bagong page
    // ---
    const loadPage = async (url, pushState = true) => {
        
        // Maglagay ng "Loading..." state habang kumukuha ng data
        if (mainContent) {
            mainContent.innerHTML = '<div class="p-10 pt-32 text-center text-gray-500">Loading page...</div>';
        }
        
        // I-set ang active link sa sidebar
        setActiveLink(url);

        try {
            // Kunin ang HTML ng bagong page
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const html = await response.text();

            // I-parse ang HTML para makuha ang laman
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            // Hanapin ang bagong content at title
            const newContent = doc.getElementById('main-content').innerHTML;
            const newTitle = doc.querySelector('title').innerText;

            // Palitan ang content at title ng kasalukuyang page
            if (mainContent) {
                mainContent.innerHTML = newContent;
            }
            document.title = newTitle;

            // I-update ang URL sa browser (para gumana ang back/forward)
            if (pushState) {
                history.pushState({ path: url }, newTitle, url);
            }

        } catch (error) {
            console.error('Failed to load page:', error);
            // Kung mag-error ang AJAX, i-full reload na lang
            window.location.href = url;
        }
    };

    // ---
    // 3. Function para i-set ang "Active" link (Ito 'yung inayos natin)
    // ---
    const setActiveLink = (url) => {
        
        let targetPathname;
        try {
            // Kunin ang "pathname" ng target URL (e.g., /admin/dashboard)
            targetPathname = new URL(url).pathname;
        } catch (e) {
            console.error('Invalid URL:', url);
            targetPathname = '/'; // Fallback
        }

        navLinks.forEach(link => {
            let linkPathname;
            try {
                // Kunin ang "pathname" ng bawat link sa sidebar
                linkPathname = new URL(link.href).pathname;
            } catch (e) {
                linkPathname = link.getAttribute('href');
            }

            const icon = link.querySelector('i'); // Kunin ang icon sa loob
            const span = link.querySelector('span'); // Kunin ang text sa loob

            // I-compare ang pathnames
            if (linkPathname === targetPathname) {
                // Set as ACTIVE (Ito 'yung logic galing sa sidebar.js mo)
                link.classList.add('bg-red-50', 'text-red-600');
                link.classList.remove('hover:bg-gray-50', 'text-gray-700', 'md:text-gray-700');
                
                if (icon) {
                    icon.classList.add('text-red-600');
                }
                if (span) {
                    span.classList.add('text-red-600'); // Pwede mo 'tong alisin kung 'text-red-600' sa parent (link) ay sapat na
                    span.classList.remove('text-gray-700');
                }
            } else {
                // Set as INACTIVE (Ito rin 'yung logic galing sa sidebar.js mo)
                link.classList.remove('bg-red-50', 'text-red-600');
                link.classList.add('hover:bg-gray-50', 'text-gray-700', 'md:text-gray-700');
                
                if (icon) {
                    icon.classList.remove('text-red-600');
                }
                if (span) {
                    span.classList.remove('text-red-600');
                    span.classList.add('text-gray-700');
                }
            }
        });
    };

    // ---
    // 4. Idagdag ang Click Listener sa LAHAT ng nav-link
    // ---
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Huwag mag-reload kung pinindot ang link ng page kung nasaan ka na
            if (link.href === window.location.href) {
                e.preventDefault();
                return;
            }

            // Pigilan ang default browser reload
            e.preventDefault();
            
            // Tawagin ang ating loadPage function
            loadPage(link.href, true); // 'true' = i-push sa history
        });
    });

    // ---
    // 5. Handle ang Back/Forward buttons ng browser
    // ---
    window.addEventListener('popstate', () => {
        // Kunin ang URL mula sa history at i-load
        loadPage(location.href, false); // 'false' = huwag na i-push sa history
    });
    
    // ---
    // 6. I-set ang tamang active link sa unang pag-load ng page
    // ---
    setActiveLink(window.location.href);

});