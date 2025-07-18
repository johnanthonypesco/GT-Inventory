<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner with Signature</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <style>
        #signature-pad {
            border: 2px solid #ccc;
            width: 100%;
            height: 150px;
            background-color: white;
        }
        /* Style to make the scanner fit the container */
        #reader {
            border: 2px solid #4a5568; /* border-gray-700 */
            border-radius: 0.5rem; /* rounded-lg */
            overflow: hidden;
        }
        /* Style for the camera selection dropdown */
        #html5-qrcode-select-camera {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border-radius: 0.375rem; /* rounded-md */
            background-color: #4a5568; /* bg-gray-700 */
            color: white;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-900 text-white">

    <div class="bg-gray-800 shadow-lg rounded-xl p-6 w-full max-w-md text-center relative">
        <h1 class="text-2xl font-semibold mb-4">📷 Scan QR Code</h1>

        <div id="reader" class="w-full"></div>

        <div class="mt-4">
            <p class="text-sm text-gray-300">Customer Signature (Required):</p>
            <canvas id="signature-pad"></canvas>
            <button onclick="clearSignature()" class="mt-2 bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md">
                Clear Signature
            </button>
        </div>

        <p id="statusMessage" class="mt-4 text-sm text-gray-400">Position the QR code inside the camera view.</p>

        <i class="fa-solid fa-arrow-left text-3xl absolute top-5 left-4 cursor-pointer" 
           onclick="window.location.href = '{{ route('admin.order') }}'"></i>
    </div>

    <script>
        const signaturePad = new SignaturePad(document.getElementById("signature-pad"));
        const statusMessage = document.getElementById("statusMessage");
        let isProcessing = false; // Flag to prevent multiple scans at once

        // This function is called when a QR code is successfully scanned
        async function onScanSuccess(decodedText, decodedResult) {
            // Prevent the scanner from processing multiple times for a single scan
            if (isProcessing) {
                return;
            }
            isProcessing = true;
            statusMessage.textContent = "✅ QR Code Detected. Processing...";
            console.log(`Scanned QR Code: ${decodedText}`);

            // Ensure signature is provided before processing
            if (signaturePad.isEmpty()) {
                alert("❌ Signature is required before scanning.");
                statusMessage.textContent = "Signature required. Please sign and rescan.";
                isProcessing = false; // Reset flag
                return;
            }

            let qrData;
            try {
                qrData = JSON.parse(decodedText);
            } catch (e) {
                alert("Invalid QR Code format. Please scan a valid QR code.");
                statusMessage.textContent = "Invalid QR code. Ready to scan.";
                isProcessing = false; // Reset flag
                return;
            }

            // Convert Signature to a file (Blob)
            const signatureDataURL = signaturePad.toDataURL("image/png");
            const signatureBlob = await fetch(signatureDataURL).then(res => res.blob());

            // Create FormData to send to your Laravel backend
            const formData = new FormData();
            formData.append("order_id", qrData.order_id);
            formData.append("product_name", qrData.product_name);
            formData.append("batch_number", qrData.batch_number);
            formData.append("expiry_date", qrData.expiry_date);
            formData.append("location", qrData.location);
            formData.append("quantity", qrData.quantity);
            formData.append("signature", signatureBlob, `signature_${qrData.order_id}.png`);

            // Send the data to your server
            try {
                const response = await fetch("/deduct-inventory", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                        "Accept": "application/json",
                    },
                    body: formData,
                });

                const data = await response.json();
                console.log("Server Response:", data);
                alert(data.message);

                if (response.ok) {
                    // Optional: Redirect on success
                    // window.location.href = '{{ route('admin.order') }}';
                }

            } catch (error) {
                console.error("Error:", error);
                alert("❌ Failed to process the QR code scan. Please try again.");
            } finally {
                // Reset the flag after a delay to allow the user to see the message
                setTimeout(() => {
                    statusMessage.textContent = "Ready to scan another code.";
                    isProcessing = false;
                }, 2000);
            }
        }

        function onScanFailure(error) {
            // This function is called when no QR code is found. We can ignore it for a smoother UI.
            // console.warn(`Code scan error = ${error}`);
        }

        // Initialize the QR Code scanner
        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", // The ID of the div element
            { 
                fps: 10, // Frames per second to scan
                qrbox: { width: 250, height: 250 }, // The size of the scanning box
                rememberLastUsedCamera: true // Good for user experience
            },
            /* verbose= */ false
        );

        // Render the scanner
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);

        function clearSignature() {
            signaturePad.clear();
        }
    </script>

</body>
</html>