// Add New Product Modal
function showAddNewProductModal() {
const modal = document.getElementById('addnewproductmodal');
const btn = document.getElementById('addnewproductbtn');
const closeBtn = document.getElementById('closeaddnewproductmodal');

btn.addEventListener('click', () => modal.classList.remove('hidden'));
closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
}

// View All Products Modal
function showViewAllProductsModal() {
const modal = document.getElementById('viewallproductsmodal');
const btn = document.getElementById('viewallproductsbtn');
const closeBtn = document.getElementById('closeviewallproductsmodal');

btn.addEventListener('click', () => modal.classList.remove('hidden'));
closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
}

// Add Stock Modal
function showAddStockModal() {
const modal = document.getElementById('addstockmodal');
const closeBtn = document.getElementById('closeaddstockmodal');
const title = document.getElementById('add-stock-title');
const productIdInput = document.getElementById('selected-product-id');

document.querySelectorAll('.add-stock-btn').forEach(button => {
    button.addEventListener('click', function () {
    const row = this.closest('tr');
    const productName = `${row.dataset.product} ${row.dataset.strength} ${row.dataset.form}`;
    const productId = row.dataset.productId;

    title.textContent = `Add Stock - ${productName}`;
    productIdInput.value = productId;

    modal.classList.remove('hidden');
    });
});

closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
}

// Edit Product Modal
function showEditProductModal() {
const modal = document.getElementById('editproductmodal');
const closeBtn = document.getElementById('closeeditproductmodal');

const brandInput = document.getElementById('edit-brand');
const productInput = document.getElementById('edit-product');
const formInput = document.getElementById('edit-form');
const strengthInput = document.getElementById('edit-strength');
const productIdInput = document.getElementById('edit-product-id');

document.querySelectorAll('.edit-product-btn').forEach(button => {
    button.addEventListener('click', function () {
    const row = this.closest('tr');

    brandInput.value = row.dataset.brand;
    productInput.value = row.dataset.product;
    formInput.value = row.dataset.form;
    strengthInput.value = row.dataset.strength;
    productIdInput.value = row.dataset.productId;

    modal.classList.remove('hidden');
    });
});

closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
}

// Edit Stock Modal
function showEditStockModal() {
const modal = document.getElementById('editstockmodal');
const closeBtn = document.getElementById('closeeditstockmodal');
const title = document.getElementById('edit-stock-title');
const productDisplay = document.getElementById('edit-stock-product');
const stockIdInput = document.getElementById('edit-stock-id');
const batchInput = document.getElementById('edit-batchnumber');
const quantityInput = document.getElementById('edit-quantity');
const expiryInput = document.getElementById('edit-expiry');

document.querySelectorAll('.edit-stock-btn').forEach(button => {
    button.addEventListener('click', function () {
    const row = this.closest('tr');
    const productName = `${row.dataset.product} ${row.dataset.strength} ${row.dataset.form} (${row.dataset.brand})`;
    const batch = row.dataset.batch;
    const quantity = row.dataset.quantity;
    const expiry = row.dataset.expiry;
    const stockId = row.dataset.stockId;

    title.textContent = `Edit Stock - ${batch}`;
    productDisplay.textContent = productName;
    stockIdInput.value = stockId;
    batchInput.value = batch;
    quantityInput.value = quantity;
    expiryInput.value = expiry;

    modal.classList.remove('hidden');
    });
});

closeBtn.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.add('hidden'); });
}


// Initialize everything
document.addEventListener('DOMContentLoaded', () => {
showAddNewProductModal();
showViewAllProductsModal();
showAddStockModal();
showEditProductModal();
showEditStockModal();
});