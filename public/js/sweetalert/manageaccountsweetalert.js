const addaccountBtn = document.getElementById('addaccountBtn');
const editaccountBtn = document.getElementById('editaccountBtn');

const addaccountform = document.getElementById('addaccountform');
const editaccountform = document.getElementById('editaccountform');

addaccountBtn.addEventListener('click', () => {
    sweetalert(addaccountform);
});

editaccountBtn.addEventListener('click', () => {
    sweetalert(editaccountform);
});

function sweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to save this account?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Saved!',
                text: 'Your account has been successfully saved.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                form.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your changes were not saved.',
                icon: 'error',
                confirmButtonColor: '#3085d6'
            });
        }
    });
}
