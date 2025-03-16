<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner with Signature</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Instascan for QR Code Scanning -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <!-- Signature Pad Library -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <style>
        #signature-pad {
            border: 2px solid #ccc;
            width: 100%;
            height: 150px;
            background-color: white;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-900 text-white">

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

        <!-- Signature Pad -->
        <div class="mt-4">
            <p class="text-sm text-gray-300">Customer Signature (Required):</p>
            <canvas id="signature-pad"></canvas>
            <button onclick="clearSignature()" class="mt-2 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                Clear Signature
            </button>
        </div>

        <!-- Status -->
        <p id="statusMessage" class="mt-4 text-sm text-gray-400">Position the QR code inside the camera view.</p>

        <!-- Start Scanning Button -->
        <button id="startScan" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-md transition">
            Start Scanning
        </button>

        <!-- Back Icon -->
        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" 
           onclick="window.location.href = '{{ route('admin.order') }}'"></i>
    </div>

    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById("preview") });
        let camerasList = [];
        let signaturePad = new SignaturePad(document.getElementById("signature-pad"));

        // ✅ Get available cameras and populate dropdown
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

        // ✅ Start scanning with selected camera
        document.getElementById("startScan").addEventListener("click", function () {
            let selectedIndex = document.getElementById("cameraSelect").value;
            if (camerasList.length > 0 && selectedIndex !== "") {
                scanner.start(camerasList[selectedIndex]);
                document.getElementById("statusMessage").textContent = "Scanning...";
            } else {
                alert("Please select a camera.");
            }
        });

        // ✅ QR Code Scan Event Listener
        scanner.addListener("scan", async function (content) {
            console.log("Scanned QR Code:", content);

            // ✅ Ensure signature is provided before scanning
            if (signaturePad.isEmpty()) {
                alert("❌ Signature is required before scanning.");
                return;
            }

            let qrData;
            try {
                qrData = JSON.parse(content);
            } catch (e) {
                alert("Invalid QR Code format");
                return;
            }

            // ✅ Convert Signature to Blob
            let signatureDataURL = signaturePad.toDataURL("image/png");
            let signatureBlob = await fetch(signatureDataURL).then(res => res.blob());

            // ✅ Send Data + Signature to Laravel
            let formData = new FormData();
            formData.append("order_id", qrData.order_id);
            formData.append("product_name", qrData.product_name);
            formData.append("batch_number", qrData.batch_number);
            formData.append("expiry_date", qrData.expiry_date);
            formData.append("location", qrData.location);
            formData.append("quantity", qrData.quantity);
            formData.append("signature", signatureBlob, `signature_${qrData.order_id}.png`);

            fetch("/deduct-inventory", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                console.log("Server Response:", data);
                alert(data.message);
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("❌ Failed to process QR code scan.");
            });
        });

        function clearSignature() {
            signaturePad.clear();
        }
    </script>

</body>
</html>
