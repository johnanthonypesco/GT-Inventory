document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('viewMoreModal');
    const modalDesc = document.getElementById('modalDescription');
    const closeBtn = document.getElementById('closeModalBtn');
    const searchInput = document.getElementById('searchInput');
    const tableContainer = document.getElementById('history-table');
    const loader = document.getElementById('table-loader');
    let typingTimer;
    const delay = 500; // milliseconds delay before triggering search

    // === Modal Logic ===
    function attachModalEvents() {
        document.querySelectorAll('.view-more-btn').forEach(button => {
            button.addEventListener('click', () => {
                modalDesc.textContent = button.dataset.full;
                modal.classList.remove('hidden');
            });
        });
    }

    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', e => {
        if (e.target === modal) modal.classList.add('hidden');
    });

    attachModalEvents();

    // === Live Search ===
    searchInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => performSearch(searchInput.value), delay);
    });

    // === Fetch and Update Table ===
    function performSearch(query, url = null) {
        const targetUrl = url ? new URL(url, window.location.origin) : new URL(window.location.href);
        targetUrl.searchParams.set('search', query);

        showLoader();

        fetch(targetUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            attachModalEvents();
            attachPaginationEvents();
        })
        .catch(err => console.error('AJAX error:', err))
        .finally(() => setTimeout(() => hideLoader(), 200));
    }

    // === Pagination Handling ===
    function attachPaginationEvents() {
        document.querySelectorAll('#history-table .pagination a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageUrl = e.target.closest('a').href;
                performSearch(searchInput.value, pageUrl);
            });
        });
    }

    // === Loader Functions ===
    function showLoader() {
        if (loader) loader.classList.remove('hidden');
    }

    function hideLoader() {
        if (loader) loader.classList.add('hidden');
    }

    // Initialize pagination listeners on load
    attachPaginationEvents();
});
