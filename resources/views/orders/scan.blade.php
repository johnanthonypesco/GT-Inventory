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

        <!-- Camera Selection -->
        <label for="cameraSelect" class="block mt-4 text-sm text-gray-400">Select Camera:</label>
        <select id="cameraSelect" class="w-full p-2 mt-2 rounded-md bg-gray-700 text-white">
            <option value="">Loading...</option>
        </select>

        <!-- Status -->
        <p id="statusMessage" class="mt-4 text-sm text-gray-400">Position the QR code inside the camera view.</p>

        <!-- Button to Start Scanner -->
        <button id="startScan" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Start Scanning
        </button>

        <!-- Back Icon -->
        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" onclick="window.location.href = '{{ route('admin.order') }}'"></i>
    </div>

    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById("preview") });
        let camerasList = [];

        // Get available cameras and populate dropdown
        Instascan.Camera.getCameras()
            .then(function (cameras) {
                let cameraSelect = document.getElementById("cameraSelect");
                cameraSelect.innerHTML = ""; // Clear default option
                
                if (cameras.length > 0) {
                    camerasList = cameras;
                    cameras.forEach((camera, index) => {
                        let option = document.createElement("option");
                        option.value = index;
                        option.textContent = camera.name || `Camera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });
                } else {
                    cameraSelect.innerHTML = "<option>No cameras found</option>";
                }
            })
            .catch(function (e) {
                console.error(e);
                alert("Error accessing camera.");
            });

        // Start scanning with selected camera
        document.getElementById("startScan").addEventListener("click", function () {
            let selectedIndex = document.getElementById("cameraSelect").value;
            if (camerasList.length > 0 && selectedIndex !== "") {
                scanner.start(camerasList[selectedIndex]);
                document.getElementById("statusMessage").textContent = "Scanning...";
            } else {
                alert("Please select a camera.");
            }
        });

        // QR Code Scan Event Listener
        scanner.addListener("scan", function (content) {
            console.log("Scanned QR Code:", content);

            let qrData;
            try {
                qrData = JSON.parse(content);
            } catch (e) {
                alert("Invalid QR Code format");
                return;
            }

            // Send scanned data to Laravel
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
</body>
</html>
