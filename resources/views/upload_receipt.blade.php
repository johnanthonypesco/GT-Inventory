<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streamline Inventory with OCR | PharmaStock Pro</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
        .entry-container { transition: all 0.2s ease-in-out; transform: translateY(0); }
        .entry-container:hover { box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); transform: translateY(-3px); }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out forwards; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .hidden { display: none; }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <div class="container mx-auto px-4 py-8 max-w-5xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-3xl font-extrabold text-blue-800">PharmaStock Pro</h1>
                <p class="text-gray-600 text-lg">Intelligent Inventory Management</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                <a href="{{ route('admin.inventory') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Inventory
                </a>
                <div class="bg-blue-100 text-blue-800 px-5 py-2.5 rounded-full text-base font-semibold text-center w-full sm:w-auto">
                    OCR Data Import
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 px-6 py-5">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <i class="fas fa-receipt mr-3 text-blue-200"></i> Upload & Process Pharmacy Receipts
                </h2>
                <p class="text-blue-200 text-sm mt-1">Scan physical receipts to automatically extract and manage product inventory.</p>
            </div>

            <div class="p-6">
                <div id="uploadSection">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center mb-6 bg-gray-50 hover:border-blue-400 transition-colors duration-200">
                        <i class="fas fa-camera text-5xl text-blue-500 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Capture or Select Receipt Image</h3>
                        <p class="text-gray-500 text-sm mb-5">Supported formats: JPG, PNG. Maximum size: 4MB.</p>

                        <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col items-center">
                            <input type="file" name="receipt_image" id="receipt_image" accept="image/jpeg, image/png" required class="hidden">
                            <label for="receipt_image" class="cursor-pointer bg-blue-600 hover:bg-blue-700 text-white px-7 py-3.5 rounded-lg shadow-md font-medium transition duration-150 ease-in-out flex items-center text-lg">
                                <i class="fas fa-upload mr-3"></i> Select Image
                            </label>
                            <span id="fileNameDisplay" class="text-sm text-gray-600 mt-4 font-medium"></span>

                            <button type="submit" id="uploadBtn" class="mt-6 w-full max-w-sm bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow-md transition duration-150 ease-in-out flex items-center justify-center text-lg hidden">
                                <i class="fas fa-magic mr-3"></i> Process Receipt
                            </button>
                        </form>
                    </div>
                </div>

                <div id="reviewSection" class="hidden">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 sm:mb-0 flex items-center">
                            <i class="fas fa-clipboard-check mr-2 text-green-600"></i> Review & Confirm Inventory Data
                        </h3>
                        <span id="itemCountDisplay" class="bg-gray-100 text-gray-800 text-sm font-semibold px-3 py-1 rounded-full">0 items detected</span>
                    </div>

                    <form id="saveForm" method="POST">
                        <div id="dynamicFormContainer" class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                            {{-- Product entries will be rendered here by JavaScript --}}
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <button type="submit" id="confirmSaveBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-150 ease-in-out flex items-center justify-center text-lg">
                                <i class="fas fa-save mr-3"></i> Confirm & Save All
                            </button>
                            <button type="button" id="startOverBtn" class="w-full sm:w-auto bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
                                <i class="fas fa-redo mr-2"></i> Start Over
                            </button>
                            <button type="button" id="addManualItemBtn" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
                                <i class="fas fa-plus-circle mr-2"></i> Add Item Manually
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-10 text-center text-gray-500 text-sm">
            <p>PharmaStock Pro &copy; 2025 | Developed with care for your inventory needs.</p>
        </div>
    </div>

    <script>
        const LOCAL_STORAGE_KEY = 'ocrExtractedData';

        function saveToLocalStorage(data) { localStorage.setItem(LOCAL_STORAGE_KEY, JSON.stringify(data)); }
        function loadFromLocalStorage() { const data = localStorage.getItem(LOCAL_STORAGE_KEY); return data ? JSON.parse(data) : null; }
        function clearLocalStorage() { localStorage.removeItem(LOCAL_STORAGE_KEY); }
        function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute("content"); }
        function escapeHtml(str) {
            if (typeof str !== 'string') return str;
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        }

        let products = [];
        let allLocations = ['Baguio', 'Tarlac', 'Nueva Ecija', 'Pampanga', 'Pangasinan', 'Manila']; // Fallback list
        let selectedLocation = 'Baguio'; // Default value, will be updated
        let validationErrors = {}; 

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

        function updateItemCount() { itemCountDisplay.textContent = `${products.length} items detected`; }

        function createNewProductTemplate() {
            const today = new Date();
            const defaultExpiry = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];
            return {
                product_name: '', brand_name: '', form: '', strength: '', batch_number: '',
                expiry_date: defaultExpiry, quantity: 1, 
                location: selectedLocation, // Use the globally set selectedLocation
                season_peak: 'All-Year', source: 'manual'
            };
        }

        function renderProducts() {
            dynamicFormContainer.innerHTML = '';
            products.forEach((product, index) => {
                const productCard = document.createElement('div');
                productCard.className = 'entry-container border border-gray-200 p-5 rounded-lg bg-white shadow-sm hover:border-blue-200 relative animate-fade-in-up';
                productCard.setAttribute('data-product-index', index);
                const errorsForProduct = validationErrors[index] || {};

                productCard.innerHTML = `
                    <h4 class="font-bold text-lg text-gray-800 mb-3 flex items-center">
                        Product #${index + 1}
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full ml-3 font-medium">
                            ${product.source === 'ocr' ? 'OCR Extracted' : 'Manual Entry'}
                        </span>
                        <button type="button" class="remove-product-btn absolute top-4 right-4 text-gray-400 hover:text-red-600 transition-colors duration-150">
                            <i class="fas fa-times-circle text-xl"></i>
                        </button>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="col-span-full"><label class="block text-sm font-medium text-gray-700 mb-1">Product Name*</label><input type="text" data-field="product_name" value="${escapeHtml(product.product_name)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.product_name ? 'border-red-500' : ''}" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label><input type="text" data-field="brand_name" value="${escapeHtml(product.brand_name || '')}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Form*</label><input type="text" data-field="form" value="${escapeHtml(product.form)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.form ? 'border-red-500' : ''}" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Strength*</label><input type="text" data-field="strength" value="${escapeHtml(product.strength)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.strength ? 'border-red-500' : ''}" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Batch Number*</label><input type="text" data-field="batch_number" value="${escapeHtml(product.batch_number)}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.batch_number ? 'border-red-500' : ''}" required></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date*</label><input type="date" data-field="expiry_date" value="${product.expiry_date}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.expiry_date ? 'border-red-500' : ''}" required></div>
                        <div class="grid grid-cols-2 gap-3 col-span-full md:col-span-1">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity*</label><input type="number" data-field="quantity" value="${product.quantity}" min="1" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 ${errorsForProduct.quantity ? 'border-red-500' : ''}" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Location*</label><select data-field="location" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required>${allLocations.map(loc => `<option value="${escapeHtml(loc)}" ${product.location === loc ? 'selected' : ''}>${escapeHtml(loc)}</option>`).join('')}</select></div>
                        </div>
                        <div class="col-span-full md:col-span-1"><label class="block text-sm font-medium text-gray-700 mb-1">Season Peak*</label><select data-field="season_peak" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" required><option value="All-Year" ${product.season_peak === 'All-Year' ? 'selected' : ''}>All-Year</option><option value="Tag-init" ${product.season_peak === 'Tag-init' ? 'selected' : ''}>Tag-init</option><option value="Tag-ulan" ${product.season_peak === 'Tag-ulan' ? 'selected' : ''}>Tag-ulan</option></select></div>
                    </div>`;
                dynamicFormContainer.appendChild(productCard);

                productCard.querySelectorAll('input, select').forEach(input => {
                    input.addEventListener('input', e => {
                        const field = e.target.dataset.field;
                        products[index][field] = (e.target.type === 'number') ? parseInt(e.target.value) || 0 : e.target.value;
                    });
                });

                productCard.querySelector('.remove-product-btn').addEventListener('click', () => {
                    products.splice(index, 1);
                    validationErrors = {}; 
                    renderProducts();
                });
            });
            updateItemCount();
        }

        // ... (validateAllProducts and saveProducts functions remain unchanged)
        function validateAllProducts() {
            let isValid = true;
            validationErrors = {};

            products.forEach((product, index) => {
                let productErrors = {};

                if (!product.product_name) {
                    productErrors.product_name = 'Product name is required.';
                }
                if (!product.form) {
                    productErrors.form = 'Form is required.';
                }
                if (!product.strength) {
                    productErrors.strength = 'Strength is required.';
                }
                if (!product.batch_number) {
                    productErrors.batch_number = 'Batch number is required.';
                }
                if (!product.expiry_date) {
                    productErrors.expiry_date = 'Expiry date is required.';
                } else {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0); // Normalize to start of day
                    const expiryDate = new Date(product.expiry_date);
                    if (expiryDate < today) {
                        productErrors.expiry_date = 'Expiry date cannot be in the past.';
                    }
                }
                if (typeof product.quantity !== 'number' || product.quantity <= 0) {
                    productErrors.quantity = 'Quantity must be a positive number.';
                }
                if (!product.location) {
                    productErrors.location = 'Location is required.';
                }
                if (!product.season_peak) {
                    productErrors.season_peak = 'Season Peak is required.';
                }

                if (Object.keys(productErrors).length > 0) {
                    validationErrors[index] = productErrors;
                    isValid = false;
                }
            });
            return isValid;
        }

        async function saveProducts(event) {
            event.preventDefault();

            if (!validateAllProducts()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: 'Please correct the highlighted errors in the form.',
                    didClose: () => renderProducts() // Re-render to show error highlights
                });
                return;
            }

            Swal.fire({
                title: 'Saving Inventory',
                html: 'Please wait while we save your data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const response = await fetch("{{ route('save.inventory') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ products: products })
                });

                const data = await response.json();
                Swal.close();

                if (response.ok) {
                    let messageHtml = '<ul>';
                    if (data.results.inventory_created.length > 0) {
                        messageHtml += `<li><strong>Successfully Added:</strong><ul>${data.results.inventory_created.map(item => `<li>${item}</li>`).join('')}</ul></li>`;
                    }
                    if (data.results.duplicates.length > 0) {
                        messageHtml += `<li><strong>Skipped (Duplicates for Location):</strong><ul>${data.results.duplicates.map(item => `<li>${item}</li>`).join('')}</ul></li>`;
                    }
                    if (data.results.errors.length > 0) {
                        messageHtml += `<li><strong>Errors:</strong><ul>${data.results.errors.map(item => `<li>${item.product}: ${item.error}</li>`).join('')}</ul></li>`;
                    }
                    messageHtml += '</ul>';

                    Swal.fire({
                        icon: data.status === 'success' ? 'success' : 'warning',
                        title: data.message,
                        html: messageHtml
                    }).then(() => {
                        resetForm(); // Clear form and local storage
                    });
                } else {
                    // Handle validation errors from the server
                    if (response.status === 422 && data.errors) {
                        validationErrors = {};
                        // Map server errors to our local validationErrors structure
                        for (const key in data.errors) {
                            // Example: products.0.product_name -> 0: { product_name: [...] }
                            const match = key.match(/products\.(\d+)\.(.+)/);
                            if (match) {
                                const index = parseInt(match[1]);
                                const field = match[2];
                                if (!validationErrors[index]) {
                                    validationErrors[index] = {};
                                }
                                validationErrors[index][field] = data.errors[key][0]; // Take the first error message
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Failed',
                            html: 'Some entries have errors. Please review the highlighted fields.',
                            didClose: () => renderProducts() // Re-render to show error highlights
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Save',
                            text: data.message || 'An unexpected error occurred while saving inventory.'
                        });
                    }
                }
            } catch (error) {
                console.error('Error saving products:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not connect to the server. Please check your internet connection.'
                });
            }
        }

        uploadForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const file = receiptImageInput.files[0];
            if (!file) { Swal.fire({ icon: 'error', title: 'No File Selected', text: 'Please select an image.' }); return; }
            if (file.size > 4 * 1024 * 1024) { Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Max size is 4MB.' }); return; }

            const formData = new FormData();
            formData.append('receipt_image', file);
            Swal.fire({ title: 'Processing Receipt', html: 'Extracting product information...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const response = await fetch("{{ route('process.receipt') }}", { method: "POST", body: formData, headers: { "X-CSRF-TOKEN": getCsrfToken(), "Accept": "application/json" } });
                const data = await response.json();
                Swal.close();
                if (!response.ok) throw data;

                if (data.data && data.data.length > 0) {
                    products = data.data.map(item => ({ 
                        ...item, 
                        source: 'ocr', 
                        location: selectedLocation // Use the global default location for OCR items
                    }));
                    validationErrors = {};
                    saveToLocalStorage(products);
                    switchSections('review');
                    renderProducts();
                } else {
                    Swal.fire({ icon: 'warning', title: 'No Data Extracted', text: data.message || 'Could not find product data.' });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Processing Failed', text: error.message || 'An unknown error occurred.' });
            }
        });

        function switchSections(sectionId) {
            uploadSection.classList.toggle('hidden', sectionId !== 'upload');
            reviewSection.classList.toggle('hidden', sectionId !== 'review');
        }

        function resetForm() {
            uploadForm.reset();
            fileNameDisplay.textContent = '';
            uploadBtn.classList.add('hidden');
            products = [];
            validationErrors = {};
            clearLocalStorage();
            switchSections('upload');
            // No need to re-render here as products is empty and section is switched
        }

        startOverBtn.addEventListener('click', resetForm);
        addManualItemBtn.addEventListener('click', () => {
            products.push(createNewProductTemplate());
            switchSections('review');
            renderProducts();
            dynamicFormContainer.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'end' });
        });
        saveForm.addEventListener('submit', saveProducts);

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

        // --- MAIN LOGIC TO SET DEFAULT LOCATION ---
        document.addEventListener('DOMContentLoaded', async () => {
            // 1. Fetch all available locations from your backend
            try {
                const response = await fetch('{{ route('get.locations') }}');
                if (response.ok) {
                    const data = await response.json();
                    if (data.locations && data.locations.length > 0) {
                        allLocations = data.locations;
                    }
                }
            } catch (error) {
                console.error('Failed to fetch locations:', error);
                // Fallback list is already set
            }

            // 2. Get the location from the URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const locationFromUrl = urlParams.get('location');

            // 3. Set the global 'selectedLocation'
            if (locationFromUrl && allLocations.includes(locationFromUrl)) {
                selectedLocation = locationFromUrl;
            } else {
                // If no valid location in URL, try to get from local storage or default to first available
                const savedData = loadFromLocalStorage();
                if (savedData && savedData.length > 0 && savedData[0].location) {
                    selectedLocation = savedData[0].location;
                } else {
                    selectedLocation = allLocations[0] || 'Baguio'; // Fallback to first available location
                }
            }

            // 4. Check for locally saved data and proceed
            const savedData = loadFromLocalStorage();
            if (savedData && savedData.length > 0) {
                products = savedData.map(item => ({
                    ...item,
                    location: item.location || selectedLocation // Ensure saved items also get a location if somehow missing
                }));
                switchSections('review');
                renderProducts();
            } else {
                switchSections('upload');
            }
        });
    </script>
</body>
</html>