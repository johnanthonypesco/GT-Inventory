const editButtons = document.getElementsByClassName('edit-btn');
const editModal = document.getElementById('editmodal');
const editForm = document.getElementById('editForm');
const updateButton = document.getElementById('updateButton');

Array.from(editButtons).forEach(button => {
    button.addEventListener('click', () => {
        const id = button.getAttribute('data-id');
        const aboutus1 = button.getAttribute('data-aboutus1');
        const aboutus2 = button.getAttribute('data-aboutus2');
        const aboutus3 = button.getAttribute('data-aboutus3');
        const contact_number = button.getAttribute('data-contact_number');
        const email = button.getAttribute('data-email');
        const address = button.getAttribute('data-address');

        editForm.elements['aboutus1'].value = aboutus1;
        editForm.elements['aboutus2'].value = aboutus2;
        editForm.elements['aboutus3'].value = aboutus3;
        editForm.elements['contact_number'].value = contact_number;
        editForm.elements['email'].value = email;
        editForm.elements['address'].value = address;

        editModal.style.display = 'block';
    });
});

updateButton.addEventListener('click', () => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You must be updating the content in the promotional page.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updated!',
                text: 'Your content has been successfully updated.',
                icon: 'success',
                confirmButtonColor: '#3085d6'
            }).then(() => {
                editForm.submit();
            });
        } else {
            Swal.fire({
                title: 'Cancelled',
                text: 'Your content is safe.',
                icon: 'info',
                confirmButtonColor: '#3085d6'
            });
        }
    });
});


window.addEventListener('click', (event) => {
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
});

function closeeditmodal() {
    editModal.style.display = 'none';
}