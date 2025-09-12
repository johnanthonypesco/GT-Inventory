
document.addEventListener('click', (e) => {
    const btn = e.target.closest('#addproductBtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#addproduct');
    if(form) showsweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#addstockBtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#addspecificstock');
    if(form) showsweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#edit-stock-btn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#edit-stock-form');
    if(form) showsweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#edit-prod-btn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#edit-prod-reset');
    if(form) showsweetalert(form);    
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('#addmultiplestockBtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#addmultiplestockform');
    if(form) showsweetalert(form);
});

document.addEventListener('click', (e) => {
    const btn = e.target.closest('.unarchivebtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('.unarchiveform');
    if(form) sweetalert(form);
});

function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action can't be undone. Please confirm if you want to proceed.",
        icon: 'info',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        confirmButtonText: 'Confirm',
        allowOutsideClick: false,
        customClass: {
            container: 'swal-container',
            popup: 'swal-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content', 
            confirmButton: 'swal-confirm-button',
            cancelButton: 'swal-cancel-button',
            icon: 'swal-icon'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    confirmButton: 'swal-confirm-button'
                },
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

