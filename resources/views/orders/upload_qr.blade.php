<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload QR Code</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}

</head>
<body>
    <h1>Upload QR Code</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="qr_code" id="qr_code" accept="image/*" required>
        <button type="submit"><i class="fa-solid fa-upload"></i> Upload</button>
    </form>

</body>
{{-- <script> document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append('qr_code', document.getElementById('qr_code').files[0]);

    fetch("{{ route('upload.qr.code') }}", {
        method: "POST",
        body: formData,
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message.includes('Error')) {
            Swal.fire("Error", data.message, "error");
        } else {
            Swal.fire("Success", data.message, "success");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire("Error", "Failed to process QR code upload.", "error");
    });
});</script> --}}
</html>
<script>
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
        if (data.data && data.data.length > 0) {
            let formContainer = document.getElementById('dynamicFormContainer');
            formContainer.innerHTML = ""; // Clear previous entries

            data.data.forEach((entry, index) => {
                let entryHtml = `
                    <div class="entry-container border p-3 rounded-md bg-gray-50 mt-3">
                        <label class="text-gray-600 text-sm">Product Name</label>
                        <input type="text" name="products[${index}][product_name]" value="${entry.product_name}" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                        
                        <label class="text-gray-600 text-sm">Batch Number</label>
                        <input type="text" name="products[${index}][batch_number]" value="${entry.batch_number}" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                        
                        <label class="text-gray-600 text-sm">Expiry Date</label>
                        <input type="date" name="products[${index}][expiry_date]" value="${entry.expiry_date}" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                        
                        <label class="text-gray-600 text-sm">Quantity</label>
                        <input type="number" name="products[${index}][quantity]" value="${entry.quantity}" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                        
                        <label class="text-gray-600 text-sm">Location</label>
                        <input type="text" name="products[${index}][location]" value="${entry.location}" class="w-full border rounded-md px-3 py-2 focus:outline-blue-400">
                    </div>
                `;
                formContainer.innerHTML += entryHtml;
            });
        } else {
            Swal.fire("Error", data.message, "error");
        }
    })
    .catch(error => Swal.fire("Error", "Failed to process receipt. " + error.message, "error"));
});

document.getElementById('saveForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);
    
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
</script>

