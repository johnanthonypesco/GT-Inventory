document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append('receipt_image', document.getElementById('receipt_image').files[0]);

    fetch("{{ route('process.receipt') }}", {
        method: "POST",
        body: formData,
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.data) {
            document.getElementById('product_name').value = data.data.product_name;
            document.getElementById('batch_number').value = data.data.batch_number;
            document.getElementById('expiry_date').value = data.data.expiry_date;
            document.getElementById('quantity').value = data.data.quantity;
            document.getElementById('location').value = data.data.location;
        }
    })
    .catch(() => Swal.fire("Error", "Failed to process receipt.", "error"));
});

document.getElementById('saveForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append('product_name', document.getElementById('product_name').value);
    formData.append('batch_number', document.getElementById('batch_number').value);
    formData.append('expiry_date', document.getElementById('expiry_date').value);
    formData.append('quantity', document.getElementById('quantity').value);
    formData.append('location', document.getElementById('location').value);

    fetch("{{ route('save.receipt') }}", { 
        method: "POST",
        body: formData,
        headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            Swal.fire("Success", data.message, "success");
        } else {
            Swal.fire("Error", "Failed to save data.", "error");
        }
    })
    .catch(() => Swal.fire("Error", "Failed to connect to the server.", "error"));
});
