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
                    title: 'Deleted!',
                    text: 'Your account has been successfully deleted.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    // Submit the corresponding form
                    deleteaccountform.submit();
                });
            } else {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your account is safe.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    });
});
