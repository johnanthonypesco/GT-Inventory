document.addEventListener('click', function(e) {
    const btn = e.target.closest('#checkoutbtn');
    if (!btn) return;
    e.preventDefault();
    const form = btn.closest('#ordersummaryform');
    if(form) showsweetalert(form);
});


function showsweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
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
                text: "Please wait, your request is being processed.",
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-container',
                    popup: 'swal-popup',
                    title: 'swal-title',
                    htmlContainer: 'swal-content', 
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
    } else if (document.getElementById('errorAlert')) {
        document.getElementById('errorMessage').textContent = errorMessage;
        setTimeout(() => {
            document.getElementById('errorAlert').remove();
        }, 3000);
    }
});