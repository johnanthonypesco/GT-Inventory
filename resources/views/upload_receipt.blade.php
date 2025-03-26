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
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
 <!-- Back Button -->
 
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold text-center text-gray-800 mb-4">Upload Receipt Image</h1>

        <!-- Upload Form -->
        <form id="uploadForm" enctype="multipart/form-data" class="flex flex-col items-center">
            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required class="hidden">
            <label for="receipt_image" class="cursor-pointer bg-blue-500 text-white px-4 py-2 rounded-md shadow-md">
                Choose File
            </label>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md mt-4 w-full">Upload</button>
        </form>
        
        <h2 class="text-lg font-semibold text-gray-700 mt-6">Extracted Data</h2>

        <!-- Review & Edit Form -->
        <form id="saveForm" class="mt-4 space-y-3">
            <div id="dynamicFormContainer" class="space-y-3"></div> <!-- 🛠 Dynamic product fields go here -->
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md mt-4 w-full">Confirm & Save</button>
            <button onclick="goBack()" class="bg-gray-500 text-white px-6 py-2 rounded-md mt-4 w-full">
                ⬅ Back
            </button>
        </form>
    </div>

    <script>

         function goBack() {
            window.history.back();
        }
        let locations = []; // Will store available locations

        // Fetch locations from the backend
        fetch("{{ route('get.locations') }}")
        .then(response => response.json())
        .then(data => {
            locations = data.locations; // Store locations globally
        })
        .catch(error => console.error("Failed to load locations:", error));

        document.getElementById('uploadForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let formData = new FormData();
            formData.append('receipt_image', document.getElementById('receipt_image').files[0]);

            fetch("{{ route('process.receipt') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content") 
                }
            })
            .then(response => response.json())
            .then(data => {
                let formContainer = document.getElementById('dynamicFormContainer');
                formContainer.innerHTML = ""; // Clear previous entries

                if (data.data && data.data.length > 0) {
                    data.data.forEach((entry, index) => {
                        let formattedDate = entry.expiry_date ? new Date(entry.expiry_date).toISOString().split('T')[0] : '';
                        let missingClass = entry.missing ? 'border-red-500 bg-red-100' : ''; // Highlight missing products

                        // Generate location dropdown
                        let locationOptions = locations.map(loc => 
                            `<option value="${loc}" ${loc === 'Tarlac' ? 'selected' : ''}>${loc}</option>`
                        ).join('');

                        let entryHtml = `
                            <div class="entry-container border p-3 rounded-md bg-gray-50 mt-3 ${missingClass}">
                                <label class="text-gray-600 text-sm">Product Name</label>
                                <input type="text" name="products[${index}][product_name]" value="${entry.product_name}" class="w-full border rounded-md px-3 py-2">
                                
                                <label class="text-gray-600 text-sm">Batch Number</label>
                                <input type="text" name="products[${index}][batch_number]" value="${entry.batch_number}" class="w-full border rounded-md px-3 py-2">
                                
                                <label class="text-gray-600 text-sm">Expiry Date</label>
                                <input type="date" name="products[${index}][expiry_date]" value="${formattedDate}" class="w-full border rounded-md px-3 py-2">
                                
                                <label class="text-gray-600 text-sm">Quantity</label>
                                <input type="number" name="products[${index}][quantity]" value="${entry.quantity}" class="w-full border rounded-md px-3 py-2">
                                
                                <label class="text-gray-600 text-sm">Location</label>
                                <select name="products[${index}][location]" class="w-full border rounded-md px-3 py-2">
                                    ${locationOptions}
                                </select>
                            </div>
                        `;
                        formContainer.innerHTML += entryHtml;
                    });

                    if (data.missing_products && data.missing_products.length > 0) {
                        Swal.fire({
                            title: "Warning",
                            text: "Some products are not registered. Please add them first.",
                            icon: "warning"
                        });
                    }
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            })
            .catch(error => Swal.fire("Error", "Failed to process receipt. " + error.message, "error"));
        });

        document.getElementById('saveForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let products = [];
            let missingProducts = [];
            let productElements = document.querySelectorAll('.entry-container');

            productElements.forEach((product, index) => {
                let productName = product.querySelector(`[name="products[${index}][product_name]"]`).value;
                let batchNumber = product.querySelector(`[name="products[${index}][batch_number]"]`).value;
                let expiryDate = product.querySelector(`[name="products[${index}][expiry_date]"]`).value;
                let quantity = product.querySelector(`[name="products[${index}][quantity]"]`).value;
                let location = product.querySelector(`[name="products[${index}][location]"]`).value;

                products.push({
                    product_name: productName,
                    batch_number: batchNumber,
                    expiry_date: expiryDate,
                    quantity: quantity,
                    location: location
                });

                if (product.classList.contains('border-red-500')) {
                    missingProducts.push(productName);
                }
            });

            if (missingProducts.length > 0) {
                Swal.fire("Warning", "Some products are not registered. Please add them first.", "warning");
                return;
            }

            fetch("{{ route('save.receipt') }}", { 
                method: "POST",
                body: JSON.stringify({ products: products }),
                headers: { 
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                }
            })
            .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            Swal.fire("Success", data.message, "success");
        } else if (data.status === "warning") {
            Swal.fire({
                title: "Warning",
                text: data.message,
                icon: "warning"
            });
        } else {
            Swal.fire("Error", "Failed to save data.", "error");
        }
    })
    .catch(() => Swal.fire("Error", "Failed to connect to the server.", "error"));
});
    </script>

</body>
</html>

