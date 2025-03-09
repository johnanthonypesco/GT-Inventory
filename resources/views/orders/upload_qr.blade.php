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
<script src="{{ asset('js/uploadqr.js') }}"></script>
</html>
