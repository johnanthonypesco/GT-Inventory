<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
</head>
<body>
    <h1>Scan QR Code</h1>

    <video id="preview" style="width: 100%; max-width: 500px;"></video>

    <script>
        let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    
        scanner.addListener('scan', function(content) {
            console.log("Scanned QR Code: ", content);
    
            let qrData;
            try {
                qrData = JSON.parse(content);
            } catch (e) {
                alert("Invalid QR Code format");
                return;
            }
    
            // Send the scanned data to Laravel
            fetch('/deduct-inventory', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(qrData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message.includes("already been scanned")) {
                    alert("⚠ This QR code has already been used!");
                } else {
                    alert(data.message);
                }
                console.log("Response:", data);
            })
            .catch(error => {
                console.error("Error:", error);
                alert("❌ Failed to process QR code scan.");
            });
        });
    
        Instascan.Camera.getCameras().then(function(cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                alert("No cameras found.");
            }
        }).catch(function(e) {
            console.error(e);
            alert("Error accessing camera.");
        });
    </script>
    