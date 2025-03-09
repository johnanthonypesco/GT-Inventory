<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 text-white">

    <div class="bg-gray-800 shadow-lg rounded-xl p-6 w-full max-w-md text-center relative">
        <h1 class="text-2xl font-semibold mb-4">📷 Scan QR Code</h1>

        <!-- Video Preview -->
        <div class="overflow-hidden rounded-lg shadow-lg border border-gray-700">
            <video id="preview" class="w-full"></video>
        </div>

        <!-- Status -->
        <p id="statusMessage" class="mt-4 text-sm text-gray-400">Position the QR code inside the camera view.</p>

        <!-- Button to Start Scanner -->
        <button id="startScan" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Start Scanning
        </button>
        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" onclick="window.location.href = '{{ route('admin.order') }}'"></i>
    </div>
</body>
<script src="{{ asset('js/scan.js') }}"></script>
</html>