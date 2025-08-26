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
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
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
                            <button type="button" id="exportDocxBtn" class="w-full sm:w-auto bg-blue-800 hover:bg-blue-900 text-white px-5 py-3 rounded-lg font-medium transition duration-150 ease-in-out text-lg">
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
    let products = [];
    let allLocations = ['Baguio', 'Tarlac', 'Nueva Ecija', 'Pampanga', 'Pangasinan', 'Manila'];
    let selectedLocation = 'Nueva Ecija';
    let validationErrors = {};

    // === Utility Functions ===
    function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]').getAttribute("content"); }
    function escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/[&<>"']/g, match => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[match]));
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
    const confirmSaveBtn = document.getElementById('confirmSaveBtn');
    const exportDocxBtn = document.getElementById('exportDocxBtn');

    function updateButtonStates() {
        const hasUnregistered = products.some(p => p.is_registered === false);
        const buttons = [confirmSaveBtn, exportDocxBtn];
        buttons.forEach(btn => {
            btn.disabled = hasUnregistered;
            if (hasUnregistered) {
                btn.title = 'Cannot proceed while unregistered products are present.';
                btn.classList.add('opacity-50', 'cursor-not-allowed');
                btn.classList.remove('hover:bg-blue-700', 'hover:bg-blue-900');
            } else {
                btn.title = '';
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                if(btn.id === 'confirmSaveBtn') btn.classList.add('hover:bg-blue-700');
                if(btn.id === 'exportDocxBtn') btn.classList.add('hover:bg-blue-900');
            }
        });
    }
    
    function updateItemCount() {
        itemCountDisplay.textContent = `${products.length} items detected`;
    }
    
    function createNewProductTemplate() {
        const today = new Date();
        const defaultExpiry = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate()).toISOString().split('T')[0];
        return {
            product_name: '', brand_name: '', form: '', strength: '',
            batch_number: '', expiry_date: defaultExpiry, quantity: 1,
            location: selectedLocation, season_peak: null, source: 'manual',
            is_registered: false 
        };
    }

    // =================================================================
    // === NEW AND IMPROVED CODE FOR NO-RELOAD VALIDATION ===
    // =================================================================

    // NEW (Helper Function): Updates the UI of a single product card.
    function updateProductCardUI(index, isRegistered) {
        const productCard = document.querySelector(`.entry-container[data-product-index="${index}"]`);
        if (!productCard) return;

        // Update the visual state (border color)
        if (isRegistered) {
            productCard.classList.remove('border-red-500', 'bg-red-50');
            productCard.classList.add('border-gray-200', 'hover:border-blue-200');
        } else {
            productCard.classList.add('border-red-500', 'bg-red-50');
            productCard.classList.remove('border-gray-200', 'hover:border-blue-200');
        }

        // Add or remove the error message
        let errorP = productCard.querySelector('.error-message');
        if (!isRegistered && !errorP) {
            errorP = document.createElement('p');
            errorP.className = 'error-message text-red-600 text-sm mt-3 font-semibold';
            errorP.innerHTML = `<i class="fas fa-exclamation-triangle mr-2"></i>This product is not registered. Please correct the details or register it first.`;
            productCard.appendChild(errorP);
        } else if (isRegistered && errorP) {
            errorP.remove();
        }
    }

    // MODIFIED (Core Logic): Validates in real-time without reloading everything.
    async function validateProductRealtime(index) {
        const productCard = document.querySelector(`.entry-container[data-product-index="${index}"]`);
        if (!productCard) return;

        const product_name = productCard.querySelector('[data-field="product_name"]').value.trim();
        const brand_name = productCard.querySelector('[data-field="brand_name"]').value.trim() || null;
        const form = productCard.querySelector('[data-field="form"]').value.trim();
        const strength = productCard.querySelector('[data-field="strength"]').value.trim();
        
        // If required fields are empty, mark as unregistered immediately.
        if (!product_name || !form || !strength) {
            products[index].is_registered = false;
            updateProductCardUI(index, false);
            updateButtonStates();
            return;
        }

        try {
            const response = await axios.post("{{ route('product.check') }}", {
                product_name, brand_name, form, strength
            }, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() }
            });

            // Update the data in our products array
            products[index].is_registered = response.data.exists;
            
            // Surgically update only the UI for this card and the buttons
            updateProductCardUI(index, response.data.exists);
            updateButtonStates();

        } catch (error) {
            console.error("Validation check failed:", error);
            products[index].is_registered = false;
            updateProductCardUI(index, false);
            updateButtonStates();
        }
    }
    
    // This function now only builds the initial list. Updates are handled separately.
    function renderProducts() {
        dynamicFormContainer.innerHTML = '';
        products.forEach((product, index) => {
            const isRegistered = product.is_registered;
            const productCard = document.createElement('div');
            productCard.className = `entry-container border ${isRegistered === false ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-blue-200'} p-4 sm:p-5 rounded-lg bg-white shadow-sm relative animate-fade-in-up`;
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
                </div>
                ${isRegistered === false ? `<p class="error-message text-red-600 text-sm mt-3 font-semibold"><i class="fas fa-exclamation-triangle mr-2"></i>This product is not registered. Please correct the details or register it first.</p>` : ''}
            `;
            dynamicFormContainer.appendChild(productCard);

            const fieldsToValidate = ['product_name', 'brand_name', 'form', 'strength'];
            fieldsToValidate.forEach(fieldName => {
                productCard.querySelector(`[data-field="${fieldName}"]`)
                           .addEventListener('blur', () => validateProductRealtime(index));
            });
            
            productCard.querySelector('.remove-product-btn').addEventListener('click', () => {
                products.splice(index, 1);
                renderProducts(); // Re-render is okay here because we are deleting an element.
            });
        });
        updateItemCount();
        updateButtonStates();
    }
    
    // =================================================================
    // === END OF NEW CODE - REST OF THE SCRIPT IS THE SAME ===
    // =================================================================

    async function uploadReceipt(event) {
        event.preventDefault();
        const formData = new FormData(uploadForm);
        Swal.fire({ title: 'Uploading & Processing...', html: `Please wait...`, allowOutsideClick: false, showConfirmButton: false, willOpen: () => Swal.showLoading() });
        try {
            const response = await axios.post("{{ route('process.receipt') }}", formData, { headers: { 'Content-Type': 'multipart/form-data', 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' } });
            if (response.data.data && response.data.data.length > 0) {
                Swal.close();
                products = response.data.data.map(item => ({ ...item, source: 'ocr', location: selectedLocation }));
                switchSections('review');
                renderProducts();
            } else {
                Swal.fire({ icon: 'warning', title: 'No Data Extracted', text: response.data.message || 'Could not find product data.' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Upload Failed', text: error.response?.data?.message || error.message || 'An unknown error occurred.' });
        }
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
                season_peak: null,
            });
        });

        if (currentProducts.length === 0) {
            Swal.fire({ icon: 'error', title: 'No Products', text: 'There are no products to save.' });
            return;
        }

        Swal.fire({ title: 'Saving Inventory', html: 'Please wait...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        
        try {
            const response = await fetch("{{ route('save.inventory') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ products: currentProducts })
            });
            const data = await response.json();
            if (response.ok) {
                Swal.fire({ icon: 'success', title: data.message || 'Success!', text: 'Inventory has been updated.' }).then(() => resetForm());
            } else if (response.status === 422) {
                validationErrors = data.errors;
                let errorHtml = '<div class="text-left">Please correct the following errors:<ul>';
                Object.keys(validationErrors).forEach(key => {
                    const index = key.split('.')[1];
                    const field = key.split('.')[2];
                    errorHtml += `<li><strong>Product #${parseInt(index)+1} (${field})</strong>: ${escapeHtml(validationErrors[key][0])}</li>`;
                });
                errorHtml += '</ul></div>';
                Swal.fire({ icon: 'error', title: data.message, html: errorHtml });
            } else {
                throw new Error(data.message || 'An unknown error occurred.');
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Save Failed', text: error.message });
        }
    }

    async function exportToDocx() {
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
            });
        });

        if (currentProducts.length === 0) {
            Swal.fire({ icon: 'info', title: 'No Products', text: 'There are no products to export.' });
            return;
        }

        Swal.fire({ title: 'Generating Document', html: 'Please wait...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        try {
            const response = await axios.post("{{ route('inventory.export') }}", { 
                products: currentProducts 
            }, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                responseType: 'blob'
            });

            const blob = new Blob([response.data], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            
            const today = new Date().toISOString().slice(0, 10);
            link.download = `inventory_export_${today}.docx`;
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);

            Swal.close();

        } catch (error) {
            console.error('Export Error:', error);
            Swal.fire({ icon: 'error', title: 'Export Failed', text: 'Could not generate the document. Please check the console for details.' });
        }
    }

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
        dynamicFormContainer.innerHTML = '';
        switchSections('upload');
    }

    // === Event Listeners ===
    startOverBtn.addEventListener('click', resetForm);
    addManualItemBtn.addEventListener('click', () => {
        products.push(createNewProductTemplate());
        renderProducts(); // This is fine, as it's adding a new element.
        dynamicFormContainer.lastElementChild?.scrollIntoView({ behavior: 'smooth', block: 'end' });
    });
    
    uploadForm.addEventListener('submit', uploadReceipt);
    saveForm.addEventListener('submit', saveProducts); 
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
        switchSections('upload');
    })();
});
</script>
</body>
</html>