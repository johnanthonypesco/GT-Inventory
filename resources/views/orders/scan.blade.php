<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner with Signature | RMPOIMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .hidden { display: none !important; }

        #signature-pad {
            border: 2px solid #e2e8f0;
            width: 100%;
            height: 200px;
            background-color: white;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            touch-action: none;
        }

        #reader {
            border-radius: 0.75rem;
            overflow: hidden;
            border: 2px solid #e2e8f0;
        }
        #html5-qrcode-button-camera-permission {
            background-color: #2563eb !important;
            color: white !important;
            font-weight: 500 !important;
            border-radius: 0.5rem !important;
            padding: 0.75rem 1rem !important;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 p-4">

    <div class="bg-white shadow-xl rounded-xl w-full max-w-md border border-gray-200">
        <div class="p-4 flex items-center border-b border-gray-200">
            <a href="javascript:history.back()" class="text-gray-500 hover:text-gray-800 transition-colors">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <div class="flex-grow text-center">
                <h1 id="headerTitle" class="text-lg font-bold text-gray-800">Step 1: Customer Signature</h1>
            </div>
             <div class="w-8"></div> </div>
        
        <div class="p-6">
            <div id="signatureSection">
                <div class="flex flex-col items-center mb-4">
                    <div class="bg-blue-100 p-3 rounded-full mb-3">
                        <i class="fas fa-signature text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Customer Signature</h2>
                    <p class="text-sm text-gray-500 mt-1">Please sign below to confirm receipt.</p>
                </div>
                <canvas id="signature-pad"></canvas>
                <div class="flex justify-between mt-3">
                    <button id="clearBtn" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-eraser mr-2"></i> Clear
                    </button>
                    <button id="nextBtn" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        Next <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <div id="scannerSection" class="hidden">
                <div class="flex flex-col items-center mb-4">
                    <div class="bg-green-100 p-3 rounded-full mb-3">
                        <i class="fas fa-qrcode text-green-600 text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">Scan QR Code</h2>
                    <p class="text-sm text-gray-500 mt-1">Position the order QR code inside the frame.</p>
                </div>
                <div id="reader" class="w-full"></div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const signatureSection = document.getElementById('signatureSection');
        const scannerSection = document.getElementById('scannerSection');
        const headerTitle = document.getElementById('headerTitle');
        const clearBtn = document.getElementById('clearBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        const canvas = document.getElementById("signature-pad");
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: '#1e40af',
        });

        const html5QrcodeScanner = new Html5QrcodeScanner("reader", { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        }, false);

        let isProcessing = false;
        let signatureDataURL = null;

        const resizeCanvas = () => {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        };
        
        const goToScannerStep = () => {
            if (signaturePad.isEmpty()) {
                Swal.fire({ icon: 'error', title: 'Signature Required', text: 'Please provide a signature first.' });
                return;
            }
            signatureDataURL = signaturePad.toDataURL("image/png");
            
            signatureSection.classList.add('hidden');
            scannerSection.classList.remove('hidden');
            headerTitle.textContent = 'Step 2: Scan QR Code';
            
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        };
        
        async function onScanSuccess(decodedText) {
            if (isProcessing) return;
            isProcessing = true;
            html5QrcodeScanner.pause();
            
            Swal.fire({
                title: 'Processing...',
                text: 'Validating QR code and signature.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            let qrData;
            try {
                qrData = JSON.parse(decodedText);
                // console.log(qrData);
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Invalid QR Code', text: 'The scanned code is not in the correct format.' });
                isProcessing = false;
                html5QrcodeScanner.resume();
                return;
            }

            const signatureBlob = await fetch(signatureDataURL).then(res => res.blob());
            const formData = new FormData();
            formData.append("order_id", qrData.order_id);
            formData.append("product_name", qrData.product_name);
            formData.append("brand_name", qrData.brand_name);
            formData.append("form", qrData.form);
            formData.append("strength", qrData.strength);
            formData.append("batch_number", qrData.batch_number);
            formData.append("expiry_date", qrData.expiry_date);
            formData.append("location", qrData.location);
            formData.append("quantity", qrData.quantity);
            formData.append("signature", signatureBlob, `signature_${qrData.order_id}.png`);

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
                
                if (!response.ok) throw new Error(data.message || 'An unknown error occurred.');

                await Swal.fire({ 
                    icon: 'success', 
                    title: 'Success!', 
                    text: data.message,
                    timer: 2000, // Show success message for 2 seconds
                    showConfirmButton: false
                });

                // ✅ AUTOMATICALLY GO BACK TO THE PREVIOUS PAGE
                window.history.back();

            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Processing Failed', text: error.message });
                html5QrcodeScanner.resume();
                isProcessing = false;
            }
        }

        function onScanFailure(error) { /* Ignore failures */ }

        window.addEventListener('resize', resizeCanvas);
        clearBtn.addEventListener('click', () => signaturePad.clear());
        nextBtn.addEventListener('click', goToScannerStep);
        
        resizeCanvas();
    });
    </script>
</body>
</html>