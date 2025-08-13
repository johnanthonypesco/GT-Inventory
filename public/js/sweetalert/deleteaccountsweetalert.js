// Select all delete buttons using the common class 'deleteaccountbtn'
const deleteButtons = document.querySelectorAll('.deleteaccountbtn');

deleteButtons.forEach(button => {
    button.addEventListener('click', () => {
        // Retrieve the account ID from the data attribute
        const accountId = button.dataset.accountId;
        // Get the corresponding form using the account ID in the form's ID
        const deleteaccountform = document.getElementById(`deleteaccountform-${accountId}`);

        // Show confirmation using SweetAlert2
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                deleteaccountform.submit();
            }
        });
    });
});

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
