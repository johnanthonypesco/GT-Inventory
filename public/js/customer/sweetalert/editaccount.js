const editaccountform = document.getElementById('editaccountform');
const editaccountBtn = document.getElementById('editaccountBtn');

editaccountBtn.addEventListener('click', () => {
    Swal.fire ({
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
                title: 'Updated!',
                text: 'Your account has been successfully updated.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                editaccountform.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your account is safe.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    })
});
