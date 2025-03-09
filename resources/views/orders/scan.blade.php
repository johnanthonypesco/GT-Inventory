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
<script>

let scanner = new Instascan.Scanner({
    video: document.getElementById("preview"),
});

document.getElementById("startScan").addEventListener("click", function () {
    Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
                document.getElementById("statusMessage").textContent =
                    "Scanning...";
            } else {
                alert("No cameras found.");
            }
        })
        .catch(function (e) {
            console.error(e);
            alert("Error accessing camera.");
        });
});

scanner.addListener("scan", function (content) {
    console.log("Scanned QR Code:", content);

    let qrData;
    try {
        qrData = JSON.parse(content);
    } catch (e) {
        alert("Invalid QR Code format");
        return;
    }

    // Send the scanned data to Laravel
    fetch("/deduct-inventory", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: JSON.stringify(qrData),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.message.includes("already been scanned")) {
                alert("⚠ This QR code has already been used!");
            } else {
                alert(data.message);
            }
            console.log("Response:", data);
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("❌ Failed to process QR code scan.");
        });
});

</script>
</html>