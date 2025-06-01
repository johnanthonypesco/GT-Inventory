const hamburger = document.getElementById('hamburger');
    const nav = document.getElementById('nav');

    hamburger.addEventListener('click', () => {
        nav.classList.toggle('hidden');
        hamburger.classList.toggle('fa-x');
    });

//modal for viewallproducts
const viewAllProductsButton = document.getElementById('viewallproducts');
const modal = document.getElementById('productsmodal');
const closeModalButton = document.getElementById('closeproductsmodal');
viewAllProductsButton.addEventListener('click', () => {
    modal.classList.remove('hidden');
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

            // Highlight active button
            buttons.forEach(btn => btn.classList.remove('text-[#084876]', 'border-b', 'border-[#084876]'));
            buttons.forEach(btn => btn.classList.add('text-gray-600'));
            button.classList.remove('text-gray-600');
            button.classList.add('text-[#084876]', 'border-b', 'border-[#084876]');

            // Show/hide products
            products.forEach(product => {
                const type = product.getAttribute('data-type');
                if (filter === 'all' || type === filter) {
                    product.style.display = 'flex';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    });
});
//