<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload & Review OCR Data</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

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
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-gray-600 text-sm">Product Name</label>
                    <input type="text" id="product_name" name="product_name" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                </div>
                <div>
                    <label class="text-gray-600 text-sm">Batch Number</label>
                    <input type="text" id="batch_number" name="batch_number" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                </div>
            </div>

            <label class="text-gray-600 text-sm">Expiry Date</label>
            <input type="date" id="expiry_date" name="expiry_date" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">

            <label class="text-gray-600 text-sm">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">

            <label class="text-gray-600 text-sm">Location</label>
            <input type="text" id="location" name="location" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">

            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-md mt-4 w-full">Confirm & Save</button>
        </form>
    </div>

   
    <script>document.getElementById('uploadForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        let formData = new FormData();
        formData.append('receipt_image', document.getElementById('receipt_image').files[0]);
    
        fetch("{{ route('process.receipt') }}", {
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                document.getElementById('product_name').value = data.data.product_name;
                document.getElementById('batch_number').value = data.data.batch_number;
                document.getElementById('expiry_date').value = data.data.expiry_date;
                document.getElementById('quantity').value = data.data.quantity;
                document.getElementById('location').value = data.data.location;
            }
        })
        .catch(() => Swal.fire("Error", "Failed to process receipt.", "error"));
    });
    
    document.getElementById('saveForm').addEventListener('submit', function(event) {
        event.preventDefault();
    
        let formData = new FormData();
        formData.append('product_name', document.getElementById('product_name').value);
        formData.append('batch_number', document.getElementById('batch_number').value);
        formData.append('expiry_date', document.getElementById('expiry_date').value);
        formData.append('quantity', document.getElementById('quantity').value);
        formData.append('location', document.getElementById('location').value);
    
        fetch("{{ route('save.receipt') }}", { 
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                Swal.fire("Success", data.message, "success");
            } else {
                Swal.fire("Error", "Failed to save data.", "error");
            }
        })
        .catch(() => Swal.fire("Error", "Failed to connect to the server.", "error"));
    });</script>

   

</body>
</html>
