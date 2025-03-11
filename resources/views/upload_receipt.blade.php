<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload & Review OCR Data</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-lg relative">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Upload Receipt Image</h1>
        
        <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Upload</button>
        </form>
        
        <h2 class="text-xl font-semibold text-gray-800 mt-8 text-center">Extracted Data</h2>
        
        <form id="saveForm" class="mt-4 space-y-4">
            <div class="flex gap-2">
                <div class="w-full">
                    <label class="block text-gray-600">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" class="w-full p-2 border border-[#003852] rounded-lg outline-none">
                </div>
        
                <div class="w-full">
                    <label class="block text-gray-600">Batch Number:</label>
                    <input type="text" id="batch_number" name="batch_number" class="w-full p-2 border border-[#003852] rounded-lg outline-none">
                </div>
            </div>
        
            <div>
                <label class="block text-gray-600">Expiry Date:</label>
                <input type="date" id="expiry_date" name="expiry_date" class="w-full p-2 border border-[#003852] rounded-lg outline-none">
            </div>
        
            <div>
                <label class="block text-gray-600">Quantity:</label>
                <input type="number" id="quantity" name="quantity" class="w-full p-2 border border-[#003852] rounded-lg outline-none">
            </div>
        
            <div>
                <label class="block text-gray-600">Location:</label>
                <input type="text" id="location" name="location" class="w-full p-2 border border-[#003852] rounded-lg outline-none">
            </div>
        
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">Confirm & Save</button>
        </form>
        
        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" onclick="window.location.href = '{{ route('admin.inventory') }}'"></i>        
    </div>
</body>
<script>
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
});


document.getElementById('uploadForm').addEventListener('submit', function(event) {
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

</script>
</html>

