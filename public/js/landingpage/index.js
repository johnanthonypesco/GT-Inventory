    const hamburger = document.getElementById('hamburger');
    const nav = document.getElementById('nav');
    const viewAllProductsButton = document.getElementById('viewallproducts');
    const modal = document.getElementById('productsmodal');
    const closeModalButton = document.getElementById('closeproductsmodal');

    let selectedFilter = 'all'; 

    hamburger.addEventListener('click', () => {
        nav.classList.toggle('hidden');
        hamburger.classList.toggle('fa-x');
    });

    viewAllProductsButton.addEventListener('click', () => {
        modal.classList.remove('hidden');
        applyFilter(selectedFilter, '#productsmodal');
    });

    closeModalButton.addEventListener('click', () => {
        modal.classList.add('hidden');
    });

    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.filter-btn');
        const products = document.querySelectorAll('.product-card');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.getAttribute('data-filter');
                selectedFilter = filter; 

                const parent = button.closest('#productsmodal') ? '#productsmodal' : 'body';
                applyFilter(filter, parent);
            });
        });
    });

    function applyFilter(filter, contextSelector) {
        const context = document.querySelector(contextSelector);
        const buttons = context.querySelectorAll('.filter-btn');
        const products = context.querySelectorAll('.product-card');

        buttons.forEach(btn => {
            btn.classList.remove('text-[#084876]', 'border-b', 'border-[#084876]');
            btn.classList.add('text-gray-600');
        });

        const activeBtn = context.querySelector(`.filter-btn[data-filter="${filter}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600');
            activeBtn.classList.add('text-[#084876]', 'border-b', 'border-[#084876]');
        }

        products.forEach(product => {
            const type = product.getAttribute('data-filter') || product.getAttribute('data-type'); 
            if (filter === 'all' || type === filter) {
                product.style.display = 'flex';
            } else {
                product.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('nav a');
    const sections = document.querySelectorAll('section');

    function setActiveLink() {
        let currentSectionId = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 100; 
            const sectionHeight = section.offsetHeight;

            if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
                currentSectionId = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('text-[#0097D3]', 'border-b-[2px]', 'border-[#084876]');
            if (link.getAttribute('href') === `#${currentSectionId}`) {
                link.classList.add('text-[#0097D3]', 'border-b-[2px]', 'border-[#084876]');
            }
        });
    }

    window.addEventListener('scroll', setActiveLink);

    setActiveLink();
});
