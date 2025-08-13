<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMPOIMS OCR</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('image/Logolandingpage.png') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
            @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
        .entry-container { transition: all 0.2s ease-in-out; }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .hidden { display: none; }
        .progress-bar-container { width: 100%; background-color: #e5e7eb; border-radius: 9999px; overflow: hidden; margin-top: 1rem; }
        .progress-bar { height: 1.25rem; background-color: #3b82f6; width: 0%; border-radius: 9999px; transition: width 0.3s ease-in-out; text-align: center; color: white; font-size: 0.875rem; line-height: 1.25rem; font-weight: 500; }
        .progress-bar-processing { background-size: 2rem 2rem; background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent); animation: progress-bar-stripes 1s linear infinite; }
        @keyframes progress-bar-stripes { from { background-position: 2rem 0; } to { background-position: 0 0; } }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="container mx-auto px-4 py-4 sm:py-8 max-w-5xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-blue-700">RMPOIMS</h1>
                <p class="text-gray-600 text-base sm:text-lg"><strong>Optical Character Recognition (OCR)</strong></p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                <a href="javascript:history.back()" class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">

                    <i class="fas fa-arrow-left mr-2"></i>
                    <strong>Back to Inventory</strong>
                </a>
            </div>

        </div>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-center">
                <img src="{{ asset('image/Logowname.png') }}" alt="RMPOIMS Logo" class="w-[130px] sm:w-[150px] mx-auto mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-white">Upload & Process Pharmacy Receipts</h2>
                <p class="text-blue-200 text-sm mt-1">Scan physical receipts to automatically extract and manage product inventory.</p>
            </div>
            <div class="p-4 sm:p-6">
                <div id="uploadSection">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 sm:p-8 text-center mb-6 bg-gray-50 hover:border-blue-500 transition-colors duration-200">
                        <i class="fas fa-camera text-4xl sm:text-5xl text-blue-600 mb-4"></i>
                        <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">Capture or Select Receipt Image</h3>
                        <p class="text-gray-500 text-sm mb-5">Supported formats: JPG, PNG. Max size: 4MB.</p>
                        <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col items-center">
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/jpeg, image/png" required class="hidden">
                            <label for="receipt_image" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 sm:px-7 sm:py-3.5 rounded-lg shadow-md font-medium transition duration-150 ease-in-out flex items-center text-base sm:text-lg">
                                <i class="fas fa-upload mr-3"></i> Select Image
                            </label>
                            <span id="fileNameDisplay" class="text-sm text-gray-600 mt-4 font-medium"></span>
                            <button type="submit" id="uploadBtn" class="mt-6 w-full max-w-sm bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition duration-150 ease-in-out flex items-center justify-center text-lg hidden">
                                <i class="fas fa-magic mr-3"></i> Process Receipt
                            </button>
                        </form>
                    </div>
                </div>
                <div id="reviewSection" class="hidden">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2 sm:mb-0 flex items-center">
                            <i class="fas fa-clipboard-check mr-2 text-blue-600"></i> Review & Confirm Data
                        </h3>
                        <span id="itemCountDisplay" class="bg-blue-100 text-blue-700 text-sm font-semibold px-3 py-1 rounded-full">0 items detected</span>
                    </div>
                    <form id="saveForm" method="POST" action="{{ route('save.inventory') }}">
                        @csrf
                        <div id="dynamicFormContainer" class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar"></div>
                        <div class="mt-6 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <button type="submit" id="confirmSaveBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-150 ease-in-out flex items-center justify-center text-lg">
                                <i class="fas fa-save mr-3"></i> Confirm & Save
                            </button>
                            <button type="button" id="startOverBtn" class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
                                <i class="fas fa-redo mr-2"></i> <strong>Start Over</strong>
                            </button>
                            <button type="button" id="addManualItemBtn" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
                                <i class="fas fa-plus-circle mr-2"></i> Add Item
                            </button>
                            <button
                                type="button"
                                id="exportDocxBtn"
                                class="w-full sm:w-auto bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
                                <i class="fas fa-file-word mr-2"></i> Export DOCX
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="mt-8 sm:mt-10 text-center text-gray-500 text-sm">
            <p>RMPOIMS &copy; {{ date('Y') }} | Developed with care for your inventory needs.</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        // === Global Variables ===
        const LOCAL_STORAGE_KEY = 'ocrExtractedData';
        let products = []; // Used for "Save" and for rendering the initial form
        let allLocations = ['Baguio', 'Tarlac', 'Nueva Ecija', 'Pampanga', 'Pangasinan', 'Manila'];
        let selectedLocation = 'Nueva Ecija';
        let validationErrors = {};

        // === Utility Functions ===
        function saveToLocalStorage(data) { localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data)); }
        function loadFromLocalStorage() { const data = localStorage.getItem(LOCAL_STORAGE_KEY); return data ? JSON.parse(data) : null; }
        function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute("content"); }
        function escapeHtml(str) {
            if (typeof str !== 'string') return str;
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

        // DOM Elements
        const uploadSection = document.getElementById('uploadSection');
        const reviewSection = document.getElementById('reviewSection');
        const uploadForm = document.getElementById('uploadForm');
        const receiptImageInput = document.getElementById('receipt_image');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const uploadBtn = document.getElementById('uploadBtn');
        const saveForm = document.getElementById('saveForm');
        const dynamicFormContainer = document.getElementById('dynamicFormContainer');
        const itemCountDisplay = document.getElementById('itemCountDisplay');
        const startOverBtn = document.getElementById('startOverBtn');
        const addManualItemBtn = document.getElementById('addManualItemBtn');
        const exportDocxBtn = document.getElementById('exportDocxBtn');

        // ===================================================================
        // === THE FINAL, CORRECTED EXPORT FUNCTION ===
        // ===================================================================
        // Replace the old exportToDocx function with this new version

async function exportToDocx() {
    const productForms = document.querySelectorAll('.entry-container');
    if (productForms.length === 0) {
        Swal.fire({ icon: 'warning', title: 'No Data', text: 'There are no products to export.' });
        return;
    }

    Swal.fire({
        title: 'Archiving Document...',
        html: 'Please wait while your file is being saved to the server.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    const exportData = [];
    productForms.forEach(form => {
        const product = {
            product_name: form.querySelector('[data-field="product_name"]').value,
            brand_name: form.querySelector('[data-field="brand_name"]').value,
            form: form.querySelector('[data-field="form"]').value,
            strength: form.querySelector('[data-field="strength"]').value,
            batch_number: form.querySelector('[data-field="batch_number"]').value,
            expiry_date: form.querySelector('[data-field="expiry_date"]').value,
            quantity: form.querySelector('[data-field="quantity"]').value,
            location: form.querySelector('[data-field="location"]').value,
            season_peak: form.querySelector('[data-field="season_peak"]').value,
        };
        exportData.push(product);
    });

    try {
        // This is the updated fetch call logic
        const response = await fetch("{{ route('inventory.export') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json' // Important: We expect a JSON response
            },
            body: JSON.stringify({ products: exportData })
        });

        const data = await response.json(); // We parse the response as JSON, not a blob

        if (!response.ok) {
            // Throw an error if the server response is not successful
            throw new Error(data.message || `Server responded with status ${response.status}`);
        }

        // If successful, show a success message
        Swal.fire({
            icon: 'success',
            title: 'Export Successful!',
            text: data.message,
        });

    } catch (error) {
        console.error('Export Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: error.message || 'An unknown error occurred while saving the file.'
        });
    }
}

        // === ALL OTHER FUNCTIONS (renderProducts, saveProducts, etc.) remain the same ===
        function updateItemCount() {
            itemCountDisplay.textContent = `${document.querySelectorAll('.entry-container').length} items detected`;
        }

        function createNewProductTemplate() {
            const today = new Date();
            const defaultExpiry = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];
            return {
                product_name: '', brand_name: '', form: '', strength: '',
                batch_number: '', expiry_date: defaultExpiry, quantity: 1,
                location: selectedLocation, season_peak: 'All-Year', source: 'manual'
            };
        }

        function renderProducts() {
            dynamicFormContainer.innerHTML = '';
            products.forEach((product, index) => {
                const productCard = document.createElement('div');
                productCard.className = 'entry-container border border-gray-200 p-4 sm:p-5 rounded-lg bg-white shadow-sm hover:border-blue-200 relative animate-fade-in-up';
                productCard.setAttribute('data-product-index', index);
                
                productCard.innerHTML = `
                    <h4 class="font-bold text-base sm:text-lg text-gray-800 mb-3 flex items-center">
                        Product #${index + 1}
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full ml-3 font-medium">${product.source === 'ocr' ? 'OCR Extracted' : 'Manual Entry'}</span>
                        <button type="button" class="remove-product-btn absolute top-4 right-4 text-gray-400 hover:text-red-600 transition-colors duration-150"><i class="fas fa-times-circle text-xl"></i></button>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="col-span-full"><label class="block text-sm font-medium text-gray-700 mb-1">Product Name*</label><input type="text" data-field="product_name" value="${escapeHtml(product.product_name || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label><input type="text" data-field="brand_name" value="${escapeHtml(product.brand_name || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Form*</label><input type="text" data-field="form" value="${escapeHtml(product.form || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Strength*</label><input type="text" data-field="strength" value="${escapeHtml(product.strength || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Batch Number*</label><input type="text" data-field="batch_number" value="${escapeHtml(product.batch_number || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date*</label><input type="date" data-field="expiry_date" value="${product.expiry_date || ''}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div class="grid grid-cols-2 gap-3 col-span-full md:col-span-1">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity*</label><input type="number" data-field="quantity" value="${product.quantity || 1}" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Location*</label><select data-field="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>${allLocations.map(loc => `<option value="${escapeHtml(loc)}" ${product.location === loc ? 'selected' : ''}>${escapeHtml(loc)}</option>`).join('')}</select></div>
                        </div>
                        <div class="col-span-full md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Season Peak*</label>
                            <select data-field="season_peak" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="All-Year" ${product.season_peak === 'All-Year' ? 'selected' : ''}>All-Year</option>
                                <option value="Tag-init" ${product.season_peak === 'Tag-init' ? 'selected' : ''}>Tag-init</option>
                                <option value="Tag-ulan" ${product.season_peak === 'Tag-ulan' ? 'selected' : ''}>Tag-ulan</option>
                            </select>
                        </div>
                    </div>`;
                dynamicFormContainer.appendChild(productCard);
                
                productCard.querySelectorAll('input, select').forEach(input => {
                    input.addEventListener('input', e => {
                        const index = parseInt(e.target.closest('.entry-container').dataset.productIndex, 10);
                        const field = e.target.dataset.field;
                        if (products[index]) {
                            products[index][field] = (e.target.type === 'number') ? parseInt(e.target.value) || 0 : e.target.value;
                        }
                    });
                });
                
                productCard.querySelector('.remove-product-btn').addEventListener('click', (e) => {
                    const cardToRemove = e.target.closest('.entry-container');
                    const indexToRemove = parseInt(cardToRemove.dataset.productIndex, 10);
                    products.splice(indexToRemove, 1);
                    cardToRemove.remove();
                    document.querySelectorAll('.entry-container').forEach((card, newIndex) => {
                         card.setAttribute('data-product-index', newIndex);
                         card.querySelector('h4').firstChild.nodeValue = `Product #${newIndex + 1} `;
                    });
                    updateItemCount();
                });
            });
            updateItemCount();
        }

        async function saveProducts(event) {
            event.preventDefault();
            const currentProducts = [];
            document.querySelectorAll('.entry-container').forEach(form => {
                currentProducts.push({
                    product_name: form.querySelector('[data-field="product_name"]').value,
                    brand_name: form.querySelector('[data-field="brand_name"]').value,
                    form: form.querySelector('[data-field="form"]').value,
                    strength: form.querySelector('[data-field="strength"]').value,
                    batch_number: form.querySelector('[data-field="batch_number"]').value,
                    expiry_date: form.querySelector('[data-field="expiry_date"]').value,
                    quantity: form.querySelector('[data-field="quantity"]').value,
                    location: form.querySelector('[data-field="location"]').value,
                    season_peak: form.querySelector('[data-field="season_peak"]').value,
                });
            });
            if (currentProducts.length === 0) { Swal.fire({ icon: 'error', title: 'No Products', text: 'There are no products to save.' }); return; }
            Swal.fire({ title: 'Saving Inventory', html: 'Please wait...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const response = await fetch("{{ route('save.inventory') }}", {
                    method: "POST", headers: { "X-CSRF-TOKEN": getCsrfToken(), "Content-Type": "application/json", "Accept": "application/json" }, body: JSON.stringify({ products: currentProducts })
                });
                const data = await response.json();
                if (response.ok) {
                    Swal.fire({ icon: 'success', title: data.message || 'Success!', text: 'Inventory has been updated.' }).then(() => resetForm());
                } else if (response.status === 422) { /* ... same error handling ... */ }
                else { /* ... same error handling ... */ }
            } catch (error) { /* ... same error handling ... */ }
        }

        async function uploadReceipt(event) {
            event.preventDefault();
            const formData = new FormData(uploadForm);
            Swal.fire({ title: 'Uploading & Processing...', html: `Please wait...`, allowOutsideClick: false, showConfirmButton: false, willOpen: () => { Swal.showLoading(); } });
            try {
                const response = await axios.post("{{ route('process.receipt') }}", formData, { headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' } });
                const data = response.data;
                if (data.data && data.data.length > 0) {
                    Swal.close();
                    products = data.data.map(item => ({ ...item, source: 'ocr', location: selectedLocation }));
                    saveToLocalStorage(products);
                    validationErrors = {};
                    switchSections('review');
                    renderProducts();
                } else {
                    Swal.fire({ icon: 'warning', title: 'No Data Extracted', text: data.message || 'Could not find product data.' });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Upload Failed', text: error.response?.data?.message || error.message || 'An unknown error occurred.' });
            }
        }

    function switchSections(sectionId) {
        uploadSection.classList.toggle('hidden', sectionId !== 'upload');
        reviewSection.classList.toggle('hidden', sectionId !== 'review');
    }

        function resetForm() {
            uploadForm.reset(); fileNameDisplay.textContent = ''; uploadBtn.classList.add('hidden');
            products = []; validationErrors = {};
            localStorage.removeItem(LOCAL_STORAGE_KEY);
            dynamicFormContainer.innerHTML = '';
            switchSections('upload');
        }

        // === Event Listeners ===
        startOverBtn.addEventListener('click', resetForm);
        addManualItemBtn.addEventListener('click', () => {
            products.push(createNewProductTemplate());
            renderProducts();
            dynamicFormContainer.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'end' });
        });
        saveForm.addEventListener('submit', saveProducts);
        uploadForm.addEventListener('submit', uploadReceipt);
        exportDocxBtn.addEventListener('click', exportToDocx);
        receiptImageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                fileNameDisplay.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                uploadBtn.classList.remove('hidden');
            } else {
                fileNameDisplay.textContent = '';
                uploadBtn.classList.add('hidden');
            }
        });

        // === Initialize App ===
        (async () => {
            try {
                const response = await fetch('{{ route("get.locations") }}');
                if (response.ok) {
                    const data = await response.json();
                    if (data.locations && data.locations.length > 0) { allLocations = data.locations; }
                }
            } catch (error) { console.error('Failed to fetch locations:', error); }
            
            const savedData = loadFromLocalStorage();
            if (savedData && savedData.length > 0) {
                products = savedData;
                switchSections('review');
                renderProducts();
            } else {
                switchSections('upload');
            }
        })();
    });
    </script>
</body>
</html>
