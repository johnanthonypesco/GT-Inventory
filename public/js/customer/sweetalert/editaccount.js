const editaccountform = document.getElementById('editaccountform');
const editaccountBtn = document.getElementById('editaccountBtn');

editaccountBtn.addEventListener('click', () => {
    Swal.fire ({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
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
