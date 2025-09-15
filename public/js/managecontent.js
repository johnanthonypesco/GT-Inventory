const editButtons = document.getElementsByClassName('edit-btn');
const editModal = document.getElementById('editmodal');

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

document.addEventListener('click', function(e) {
    if (e.target.closest('#updateButton')) {
        e.preventDefault();
        const form = document.getElementById('editForm');
        if (form) showsweetalert(form);
    }
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


window.addEventListener('click', (event) => {
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
});

function closeeditmodal() {
    editModal.style.display = 'none';
}