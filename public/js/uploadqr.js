document.getElementById('uploadForm').addEventListener('submit', function(event) {
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
});