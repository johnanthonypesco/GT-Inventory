document.addEventListener('click', (e) => {
    const btn = e.target.closest('#addproductlistingBtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#addproductlistingform');
    if(form) sweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#editproductlistingBtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#editproductlistingform');
    if(form) sweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('.unarchivebtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('.unarchiveform');
    if(form) sweetalert(form);
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