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
