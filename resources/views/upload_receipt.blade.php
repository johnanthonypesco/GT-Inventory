<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Review OCR Data</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen py-8">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Upload Receipt Image</h1>

        <!-- Upload Form -->
        <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col items-center">
            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required class="hidden">
            <label for="receipt_image" class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 transition">
                Choose File
            </label>
            <span id="fileName" class="text-sm text-gray-600 mt-2"></span>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md mt-4 w-full hover:bg-blue-700 transition">Upload</button>
        </form>
        
        <h2 class="text-lg font-semibold text-gray-700 mt-6 border-b pb-2">Extracted Data</h2>

        <!-- Review & Edit Form -->
        <form id="saveForm" method="POST" class="mt-4 space-y-3">
            <div id="dynamicFormContainer" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2"></div>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md mt-4 w-full hover:bg-green-700 transition">Confirm & Save</button>
            <button type="button" onclick="goBack()" class="bg-gray-500 text-white px-6 py-2 rounded-md mt-2 w-full hover:bg-gray-600 transition">
                ⬅ Back
            </button>
        </form>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }

        let locations = [];

        // Display selected file name
        document.getElementById('receipt_image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            document.getElementById('fileName').textContent = fileName;
        });

        // Fetch locations from the backend
        fetch("{{ route('get.locations') }}")
        .then(response => response.json())
        .then(data => {
            locations = data.locations;
        })
        .catch(error => console.error("Failed to load locations:", error));

        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const fileInput = document.getElementById('receipt_image');
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.fire("Error", "Please select a file first.", "error");
                return;
            }
            
            const file = fileInput.files[0];
            if (file.size > 4 * 1024 * 1024) { // 4MB limit
                Swal.fire("Error", "File size exceeds 4MB limit.", "error");
                return;
            }
            
            let formData = new FormData();
            formData.append('receipt_image', file);

            // Show loading indicator
            Swal.fire({
                title: 'Processing',
                html: 'Please wait while we analyze your receipt...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('process.receipt') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") 
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                Swal.close();
                let formContainer = document.getElementById('dynamicFormContainer');
                formContainer.innerHTML = "";

                if (data.data && data.data.length > 0) {
                    data.data.forEach((entry, index) => {
                        let formattedDate = entry.expiry_date ? new Date(entry.expiry_date).toISOString().split('T')[0] : '';
                        
                        let locationOptions = locations.map(loc => 
                            `<option value="${loc}" ${loc === 'Tarlac' ? 'selected' : ''}>${loc}</option>`
                        ).join('');

                        // This HTML block now includes all necessary fields.
                        let entryHtml = `
                            <div class="entry-container border p-4 rounded-lg bg-gray-50 mt-3 shadow-sm">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-2">
                                    <div class="col-span-2">
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Product Name</label>
                                        <input type="text" name="products[${index}][product_name]" value="${entry.product_name || ''}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Brand Name</label>
                                        <input type="text" name="products[${index}][brand_name]" value="${entry.brand_name || ''}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                     <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Form</label>
                                        <input type="text" name="products[${index}][form]" value="${entry.form || ''}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Strength</label>
                                        <input type="text" name="products[${index}][strength]" value="${entry.strength || ''}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Season Peak</label>
                                        <select name="products[${index}][season_peak]" class="w-full border rounded-md px-3 py-2 bg-white">
                                            <option value="All-Year" ${entry.season_peak === 'All-Year' ? 'selected' : ''}>All-Year</option>
                                            <option value="Tag-init" ${entry.season_peak === 'Tag-init' ? 'selected' : ''}>Tag-init</option>
                                            <option value="Tag-ulan" ${entry.season_peak === 'Tag-ulan' ? 'selected' : ''}>Tag-ulan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Batch Number</label>
                                        <input type="text" name="products[${index}][batch_number]" value="${entry.batch_number || ''}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Expiry Date</label>
                                        <input type="date" name="products[${index}][expiry_date]" value="${formattedDate}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Quantity</label>
                                        <input type="number" name="products[${index}][quantity]" value="${entry.quantity || 0}" class="w-full border rounded-md px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-semibold mb-1">Location</label>
                                        <select name="products[${index}][location]" class="w-full border rounded-md px-3 py-2 bg-white">
                                            ${locationOptions}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        `;
                        formContainer.innerHTML += entryHtml;
                    });
                } else {
                    Swal.fire("Error", data.message || "No data extracted", "error");
                }
            })
            .catch(error => {
                Swal.fire("Error", error.message || "Failed to process receipt", "error");
                console.error("Upload error:", error);
            });
        });

        document.getElementById('saveForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            let products = [];
            let productElements = document.querySelectorAll('.entry-container');

            // This part now correctly reads all fields from the form.
            productElements.forEach((product, index) => {
                products.push({
                    product_name: product.querySelector(`[name="products[${index}][product_name]"]`).value,
                    brand_name: product.querySelector(`[name="products[${index}][brand_name]"]`).value,
                    form: product.querySelector(`[name="products[${index}][form]"]`).value,
                    strength: product.querySelector(`[name="products[${index}][strength]"]`).value,
                    season_peak: product.querySelector(`[name="products[${index}][season_peak]"]`).value,
                    batch_number: product.querySelector(`[name="products[${index}][batch_number]"]`).value,
                    expiry_date: product.querySelector(`[name="products[${index}][expiry_date]"]`).value,
                    quantity: parseInt(product.querySelector(`[name="products[${index}][quantity]"]`).value),
                    location: product.querySelector(`[name="products[${index}][location]"]`).value
                });
            });

            fetch("{{ route('save.receipt') }}", { 
                method: "POST",
                headers: { 
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ products: products })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === "success" || data.status === "partial") {
                    let successMessage = data.message;
                    if(data.results) {
                        if(data.results.created && data.results.created.length > 0) successMessage += `<br><br><b>New Products:</b> ${data.results.created.join(', ')}`;
                        if(data.results.updated && data.results.updated.length > 0) successMessage += `<br><b>Existing Products:</b> ${data.results.updated.join(', ')}`;
                        if(data.results.errors && data.results.errors.length > 0){
                             successMessage += `<br><br><b class='text-red-500'>Errors:</b> ${data.results.errors.length}`;
                        }
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Process Complete!',
                        html: successMessage,
                    });
                } else {
                    Swal.fire("Warning", data.message, "warning");
                }
            })
            .catch(error => {
                let errorMessage = "An unknown error occurred.";
                if (error.errors) {
                    errorMessage = "Please fix the following issues:<br><ul class='text-left mt-2 list-disc list-inside'>";
                    for (const key in error.errors) {
                        errorMessage += `<li>${error.errors[key][0]}</li>`;
                    }
                    errorMessage += "</ul>";
                } else if(error.message) {
                    errorMessage = error.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    html: errorMessage,
                });
                console.error("Save error:", error);
            });
        });
    </script>
</body>
</html>

{{-- sakses --}}