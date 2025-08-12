// Add Product Listing
// document.querySelectorAll('#addproductlistingBtn').forEach((btn, index) => {
//     btn.addEventListener('click', () => {
//         const form = document.querySelectorAll('#addproductlistingform')[index];
//         sweetalert(form);
//     });
// });

document.addEventListener('click', function(e) {
    if (e.target.closest('#addproductlistingBtn')) {
        e.preventDefault();
        const form = e.target.closest('#addproductlistingform');
        sweetalert(form);
    }
});

// Edit Product Listing
// document.querySelectorAll('#editproductlistingBtn').forEach((btn, index) => {
//     btn.addEventListener('click', () => {
//         const form = document.querySelectorAll('#editproductlistingform')[index];
//         sweetalert(form);
//     });
// });

document.addEventListener('click', function(e) {
    if (e.target.closest('#editproductlistingBtn')) {
        e.preventDefault();
        const form = e.target.closest('#editproductlistingform');
        sweetalert(form);
    }
});

function sweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            form.submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const successMessage = window.successMessage;
    const errorMessage = window.errorMessage;
    if (document.getElementById('successAlert')) {
        document.getElementById('successMessage').textContent = successMessage;
        setTimeout(() => {
            document.getElementById('successAlert').remove();
        }, 3000);
    }
    else if (document.getElementById('errorAlert')) {
        document.getElementById('errorMessage').textContent = errorMessage;
        setTimeout(() => {
            document.getElementById('errorAlert').remove();
        }, 3000);
    }
});