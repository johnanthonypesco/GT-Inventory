document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('viewMoreModal');
    const modalDesc = document.getElementById('modalDescription');
    const closeBtn = document.getElementById('closeModalBtn');
    const searchInput = document.querySelector('input[placeholder="Search logs..."]');
    const tableContainer = document.getElementById('history-table');
    let typingTimer;
    const delay = 500; // 0.5 seconds

    // Modal behavior
    function attachModalEvents() {
        document.querySelectorAll('.view-more-btn').forEach(button => {
            button.addEventListener('click', () => {
                modalDesc.textContent = button.dataset.full;
                modal.classList.remove('hidden');
            });
        });
    }

    closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });

    attachModalEvents();

    // Live search
    searchInput.addEventListener('input', () => {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => performSearch(searchInput.value), delay);
    });

    function performSearch(query) {
        const url = new URL(window.location.href);
        url.searchParams.set('search', query);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
            attachModalEvents(); // Reattach modal buttons after DOM update
        })
        .catch(err => console.error('Search error:', err));
    }
});
