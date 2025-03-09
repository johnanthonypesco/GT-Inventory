<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload & Review OCR Data</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Upload Receipt Image</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="receipt_image" id="receipt_image" accept="image/*" required>
        <button type="submit">Upload</button>
    </form>

    <h2>Extracted Data</h2>
    <form id="saveForm">
        <label>Product Name:</label>
        <input type="text" id="product_name" name="product_name"><br>

        <label>Batch Number:</label>
        <input type="text" id="batch_number" name="batch_number"><br>

        <label>Expiry Date:</label>
        <input type="date" id="expiry_date" name="expiry_date"><br>

        <label>Quantity:</label>
        <input type="number" id="quantity" name="quantity"><br>

        <label>Location:</label>
        <input type="text" id="location" name="location"><br>

        <button type="submit">Confirm & Save</button>
    </form>

    <script>
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
    <script>
        document.getElementById('saveForm').addEventListener('submit', function(event) {
            event.preventDefault();
    
            let formData = new FormData();
            formData.append('product_name', document.getElementById('product_name').value);
            formData.append('batch_number', document.getElementById('batch_number').value);
            formData.append('expiry_date', document.getElementById('expiry_date').value);
            formData.append('quantity', document.getElementById('quantity').value);
            formData.append('location', document.getElementById('location').value);
    
            fetch("{{ route('save.receipt') }}", { // ✅ Use save.receipt route instead of process.receipt
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
    
</body>
</html>
