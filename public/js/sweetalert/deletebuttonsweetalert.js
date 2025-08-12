document.addEventListener('click', function(e) {
    if (e.target.closest('#deletedealbtn')) {
        e.preventDefault();
        const form = document.getElementById('deletedeal');
        deletesweetalert(form);
    }

    if (e.target.closest('#deletedealelsebtn')) {
        e.preventDefault();
        const form = document.getElementById('deletedealelse');
        deletesweetalert(form);
    }

    if (e.target.closest('#archivebtn')) {
        e.preventDefault();
        const form = document.getElementById('archiveform');
        deletesweetalert(form);
    }

    if (e.target.closest('#deletebtndefault')) {
        e.preventDefault();
        const form = document.getElementById('deleteformdefault');
        deletesweetalert(form);
    }

    if (e.target.closest('#deletebtndefaultelse')) {
        e.preventDefault();
        const form = document.getElementById('deleteformdefaultelse');
        deletesweetalert(form);
    }
});


function deletesweetalert(form) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            form.submit();
        }
    });
}

