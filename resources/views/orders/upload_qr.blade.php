<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload QR Code</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h1>Upload QR Code</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="qr_code" id="qr_code" accept="image/*" required>
        <button type="submit"><i class="fa-solid fa-upload"></i> Upload</button>
    </form>

</body>
<script> document.getElementById('uploadForm').addEventListener('submit', function(event) {
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
});</script>
</html>
