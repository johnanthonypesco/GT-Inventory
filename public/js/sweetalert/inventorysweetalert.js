document.addEventListener('click', function(e) {
    if (e.target.closest('#addproductBtn')) {
        e.preventDefault();
        const form = document.getElementById('addproduct');
        showsweetalert(form);
    }

    if (e.target.closest('#addstockBtn')) {
        e.preventDefault();
        const form = document.getElementById('addspecificstock');
        showsweetalert(form);
    }

    if (e.target.closest('#edit-stock-btn')) {
        e.preventDefault();
        const form = document.getElementById('edit-stock-form');
        showsweetalert(form);
    }

    if (e.target.closest('#edit-prod-btn')) {
        e.preventDefault();
        const form = document.getElementById('edit-prod-reset');
        showsweetalert(form);
    }

    if (e.target.closest('.unarchivebtn')) {
        e.preventDefault();
        const form = document.querySelector('.unarchiveform');
        showsweetalert(form);
    }

    if (e.target.closest('#addmultiplestockBtn')) {
        e.preventDefault();
        const form = document.querySelector('#addmultiplestockform');
        showsweetalert(form);
    }
});

function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!',
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
