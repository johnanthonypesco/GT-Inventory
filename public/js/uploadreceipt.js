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
            let container = document.getElementById('products-container');
            container.innerHTML = '';

            data.data.forEach((product, index) => {
                container.innerHTML += `
                    <div>
                        <label>Product Name:</label>
                        <input type="text" name="products[${index}][product_name]" value="${product.product_name}">
                        <label>Batch Number:</label>
                        <input type="text" name="products[${index}][batch_number]" value="${product.batch_number}">
                        <label>Expiry Date:</label>
                        <input type="date" name="products[${index}][expiry_date]" value="${product.expiry_date}">
                        <label>Quantity:</label>
                        <input type="number" name="products[${index}][quantity]" value="${product.quantity}">
                        <label>Location:</label>
                        <input type="text" name="products[${index}][location]" value="${product.location ?? ''}">
                    </div>
                `;
            });
        }
    })
    .catch(() => Swal.fire("Error", "Failed to process receipt.", "error"));
});

// Handle Save Form Submission
document.getElementById('saveForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch("{{ route('save.inventory') }}", {
        method: "POST",
        body: new URLSearchParams(new FormData(this)), // Convert FormData to URL params
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            Swal.fire("Success", data.message, "success");
        } else {
            Swal.fire("Error", "Something went wrong!", "error");
        }
    })
    .catch(() => Swal.fire("Error", "Failed to save inventory.", "error"));
});