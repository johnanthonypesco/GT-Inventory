function deletesweetalert(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'The Product has been successfully deleted.',
                    icon: 'success',
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    button.closest('form').submit();
                });
            } else {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your Product is safe.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    }